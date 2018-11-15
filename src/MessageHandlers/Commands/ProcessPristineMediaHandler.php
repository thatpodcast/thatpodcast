<?php

namespace App\MessageHandlers\Commands;

use App\Entity\Episode;
use App\FlysystemAssetManager\File;
use App\FlysystemAssetManager\FlysystemAssetManager;
use App\Messages\Commands\ProcessPristineMedia;
use App\Repository\EpisodeRepository;
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
     * ProcessPristineMediaHandler constructor.
     * @param string $projectDir
     * @param EpisodeRepository $episodeRepository
     * @param FlysystemAssetManager $flysystemAssetManager
     * @param ObjectManager $objectManager
     */
    public function __construct($projectDir, EpisodeRepository $episodeRepository, FlysystemAssetManager $flysystemAssetManager, ObjectManager $objectManager)
    {
        $this->projectDir = $projectDir;
        $this->episodeRepository = $episodeRepository;
        $this->flysystemAssetManager = $flysystemAssetManager;
        $this->objectManager = $objectManager;
    }

    public function __invoke(ProcessPristineMedia $command)
    {
        $getID3 = new getID3();

        $episode = $this->episodeRepository->find($command->episodeId);

        $pristineTmpFile = tempnam(sys_get_temp_dir(), 'pristine-media-download');
        $pristineTargetStream = fopen($pristineTmpFile, 'w');

        $pristineSourceStream = $this->flysystemAssetManager->getStream($episode->getPristineMedia());

        if (false === stream_copy_to_stream($pristineSourceStream, $pristineTargetStream)) {
            print "could not copy stream...\n";
            return;
        }

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

        $coverPhotoFileName = $this->projectDir.'/assets/images/that-podcast-cover-photo.jpg';
        $coverPhotoData = file_get_contents($coverPhotoFileName);
        $coverPhotoExifImageType = \exif_imagetype($coverPhotoFileName);
        $tagData['attached_picture'][] = [
            'data' => $coverPhotoData,
            'picturetypeid' => $coverPhotoExifImageType,
            'description' => basename($coverPhotoFileName),
            'mime' => image_type_to_mime_type($coverPhotoExifImageType),
        ];

        $tagwriter->tag_data = $tagData;
        if (! $tagwriter->WriteTags()) {
            print_r(['error' => $tagwriter->errors, 'warning' => $tagwriter->warnings]);
            return;
        }

        $mediaFile = new File(
            'content',
            Episode::generateMediaPath($episode, basename($episode->getPristineMedia()->getPath())),
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


        /*
        $id3 = $getID3->analyze($pristineTmpFile);
        print_r($id3);
        */

        // download pristine media
        // get episode meta data (from repository)
        // write meta data to mp3
        // upload media
        // write new media to episode
    }
}
