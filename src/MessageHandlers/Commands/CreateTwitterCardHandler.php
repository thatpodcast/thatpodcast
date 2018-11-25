<?php

namespace App\MessageHandlers\Commands;

use App\Card\CardBuilder;
use App\Card\CardConfiguration;
use App\Entity\Episode;
use App\FlysystemAssetManager\File;
use App\FlysystemAssetManager\FlysystemAssetManager;
use App\Messages\Commands\CreateTwitterCard;
use App\Repository\EpisodeRepository;
use Doctrine\Common\Persistence\ObjectManager;

class CreateTwitterCardHandler
{
    /**
     * @var EpisodeRepository
     */
    private $episodeRepository;

    /**
     * @var CardBuilder
     */
    private $cardBuilder;

    /**
     * @var FlysystemAssetManager
     */
    private $flysystemAssetManager;

    /**
     * @var ObjectManager
     */
    private $objectManager;


    /**
     * @var string
     */
    private $projectDir;

    public function __construct(EpisodeRepository $episodeRepository, CardBuilder $cardBuilder, FlysystemAssetManager $flysystemAssetManager, string $projectDir, ObjectManager $objectManager)
    {
        $this->episodeRepository = $episodeRepository;
        $this->cardBuilder = $cardBuilder;
        $this->flysystemAssetManager = $flysystemAssetManager;
        $this->projectDir = $projectDir;
        $this->objectManager = $objectManager;
    }

    public function __invoke(CreateTwitterCard $command)
    {
        $episode = $this->episodeRepository->find($command->episodeId);

        if (! $episode) {
            return;
        }

        $cardConfiguration = CardConfiguration::createTwitterCard()
            ->withDefaultFonts($this->projectDir)
            ->withDefaultLogo($this->projectDir)
            ->withEpisode($episode, $this->flysystemAssetManager)
        ;

        $cardFileName =  tempnam(sys_get_temp_dir(), 'episode-photo-') .'.jpg';

        $cardImage = $this->cardBuilder->buildCard($cardConfiguration);

        $cardImage->save($cardFileName, ['jpeg_quality' => 90]);

        $cardImageExifImageType = \exif_imagetype($cardFileName);

        $cardFile = File::create(
            'content',
            Episode::generateTwitterCardPath($episode, 'twitter.jpg'),
            image_type_to_mime_type($cardImageExifImageType),
            filesize($cardFileName)
        );

        $this->flysystemAssetManager->writeOrUpdateFromFile($cardFile, $cardFileName);

        unlink($cardFileName);

        $episode->setTwitterCard($cardFile);

        $this->objectManager->flush();
    }
}