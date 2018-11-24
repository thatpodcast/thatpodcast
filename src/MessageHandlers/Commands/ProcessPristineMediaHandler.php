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
            ;

        if ($episode->getBackgroundImageUrl()) {
            $backgroundImageTmpFile = $this->flysystemAssetManager->getTemporaryLocalFileName($episode->getBackgroundImage());

            $cardConfiguration = $cardConfiguration
                ->withBackgroundFileName($backgroundImageTmpFile)
                ;
        }

        if ($episode->getPublishedDate()) {
            $cardConfiguration = $cardConfiguration
                ->withDate($episode->getPublishedDate()->format('F jS, Y'))
            ;
        }

        if ($episode->getNumber()) {
            $cardConfiguration = $cardConfiguration
                ->withNumber($episode->getNumber())
                ;
        }

        if ($episode->getTitle()) {
            $cardConfiguration = $cardConfiguration
                ->withTitle($episode->getTitle())
            ;
        }

        if ($episode->getSubtitle()) {
            $cardConfiguration = $cardConfiguration
                ->withSubtitle($episode->getSubtitle());
            ;
        }

        $itunesCardFileName =  tempnam(sys_get_temp_dir(), 'episode-itunes-photo-') .'.jpg';

        $itunesCardImage = $this->cardBuilder->buildCard($cardConfiguration);

        $itunesCardImage->save($itunesCardFileName, ['jpeg_quality' => 60]);

        $itunesCardImageExifImageType = \exif_imagetype($itunesCardFileName);
        $tagData['attached_picture'][] = [
            'data' => file_get_contents($itunesCardFileName),
            'picturetypeid' => $itunesCardImageExifImageType,
            'description' => 'that-podcast-cover-photo.jpg',
            'mime' => image_type_to_mime_type($itunesCardImageExifImageType),
        ];

        unlink($itunesCardFileName);

        $tagwriter->tag_data = $tagData;
        if (! $tagwriter->WriteTags()) {
            print_r(['error' => $tagwriter->errors, 'warning' => $tagwriter->warnings]);
            return;
        }

        $mediaFile = new File(
            'content',
            Episode::generateMediaPath($episode, $targetFileName),
            $episode->getPristineMedia()->getContentType(),
            filesize($pristineTmpFile)
        );

        if ($this->flysystemAssetManager->exists($mediaFile)) {
            $this->flysystemAssetManager->delete($mediaFile);
        }

        $this->flysystemAssetManager->writeFromFile($mediaFile, $pristineTmpFile);

        $episode->setMedia($mediaFile);

        $this->objectManager->flush();
        $this->objectManager->clear();

        unlink($pristineTmpFile);
    }
}
