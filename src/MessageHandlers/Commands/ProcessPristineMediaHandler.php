<?php

namespace App\MessageHandlers\Commands;

use App\Card\CardBuilder;
use App\Card\CardConfiguration;
use App\Entity\Episode;
use App\FlysystemAssetManager\File;
use App\FlysystemAssetManager\FlysystemAssetManager;
use App\Messages\Commands\ProcessPristineMedia;
use App\Repository\EpisodeRepository;
use Cocur\Slugify\SlugifyInterface;
use Doctrine\Common\Persistence\ObjectManager;
use getID3;
use getid3_writetags;

class ProcessPristineMediaHandler
{
    /**
     * @var string
     */
    private $projectDir;

    /**
     * @var EpisodeRepository
     */
    private $episodeRepository;

    /**
     * @var FlysystemAssetManager
     */
    private $flysystemAssetManager;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var CardBuilder
     */
    private $cardBuilder;

    /**
     * @var SlugifyInterface
     */
    private $slugify;

    public function __construct(
        $projectDir,
        EpisodeRepository $episodeRepository,
        FlysystemAssetManager $flysystemAssetManager,
        ObjectManager $objectManager,
        CardBuilder $cardBuilder,
        SlugifyInterface $slugify
    ) {
        $this->projectDir = $projectDir;
        $this->episodeRepository = $episodeRepository;
        $this->flysystemAssetManager = $flysystemAssetManager;
        $this->objectManager = $objectManager;
        $this->cardBuilder = $cardBuilder;
        $this->slugify = $slugify;
    }

    public function __invoke(ProcessPristineMedia $command)
    {
        $getID3 = new getID3();

        $episode = $this->episodeRepository->find($command->episodeId);

        if (! $episode) {
            return;
        }

        if (! $this->flysystemAssetManager->exists($episode->getPristineMedia())) {
            print " [ skipping; pristine media does not exist ]\n";
            return;
        }

        $pristineTmpFile = $this->flysystemAssetManager->getTemporaryLocalFileName($episode->getPristineMedia());

        $targetFileName = $this->slugify->slugify(implode(' ', [
            'That Podcast Episode',
            $episode->getNumber(),
            $episode->getTitle()
        ])) . '.mp3';

        $tagwriter = new getid3_writetags();
        $tagwriter->filename = $pristineTmpFile;
        $tagwriter->tagformats = ['id3v2.4'];
        $tagwriter->overwrite_tags = true;
        $tagwriter->tag_encoding = 'UTF-8';
        $tagwriter->remove_other_tags = true;

        $tagData['title'][] = sprintf('Episode %s: %s', $episode->getNumber(), $episode->getTitle());
        $tagData['artist'][] = 'Beau Simensen & Dave Marshall';
        $tagData['album'][] = 'That Podcast with Beau and Dave';
        $tagData['genre'][] = 'Podcast';
        $tagData['subtitle'][] = $episode->getItunesSummaryHtml();

        $cardConfiguration = CardConfiguration::createItunesCard()
            ->withDefaultFonts($this->projectDir)
            ->withDefaultLogo($this->projectDir)
            ->withEpisode($episode, $this->flysystemAssetManager)
            ;

        $itunesCardFileName =  tempnam(sys_get_temp_dir(), 'episode-itunes-photo-') .'.jpg';

        $itunesCardImage = $this->cardBuilder->buildCard($cardConfiguration);

        $itunesCardImage->save($itunesCardFileName, ['jpeg_quality' => 90]);

        $itunesCardImageExifImageType = \exif_imagetype($itunesCardFileName);
        $tagData['attached_picture'][] = [
            'data' => file_get_contents($itunesCardFileName),
            'picturetypeid' => $itunesCardImageExifImageType,
            'description' => 'that-podcast-cover-photo.jpg',
            'mime' => image_type_to_mime_type($itunesCardImageExifImageType),
        ];

        $itunesCardFile = File::create(
            'content',
            Episode::generateItunesCardPath($episode, 'itunes.jpg'),
            image_type_to_mime_type($itunesCardImageExifImageType),
            filesize($itunesCardFileName)
        );

        $tagwriter->tag_data = $tagData;
        if (! $tagwriter->WriteTags())  {
            // Old versions of MP3 files have been known to fail since getid3 cannot
            // determine what type of file they are.
            print_r(['error' => $tagwriter->errors, 'warning' => $tagwriter->warnings]);
        }

        $this->flysystemAssetManager->writeOrUpdateFromFile($itunesCardFile, $itunesCardFileName);

        $episode->setItunesCard($itunesCardFile);

        unlink($itunesCardFileName);

        $mediaFile = File::create(
            'content',
            Episode::generateMediaPath($episode, $targetFileName),
            $episode->getPristineMedia()->getContentType(),
            filesize($pristineTmpFile)
        );

        $this->flysystemAssetManager->writeOrUpdateFromFile($mediaFile, $pristineTmpFile);

        $episode->setMedia($mediaFile);

        $data = $getID3->analyze($pristineTmpFile);

        if (array_key_exists('playtime_seconds', $data)) {
            $episode->setDuration($data['playtime_seconds']);
        }

        $this->objectManager->flush();
        $this->objectManager->clear();

        unlink($pristineTmpFile);
    }
}
