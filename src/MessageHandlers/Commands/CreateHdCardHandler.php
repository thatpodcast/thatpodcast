<?php

namespace App\MessageHandlers\Commands;

use App\Card\CardBuilder;
use App\Card\CardConfiguration;
use App\Entity\Episode;
use App\FlysystemAssetManager\File;
use App\FlysystemAssetManager\FlysystemAssetManager;
use App\Messages\Commands\CreateHdCard;
use App\Repository\EpisodeRepository;
use Doctrine\Common\Persistence\ObjectManager;

class CreateHdCardHandler
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
     * @var string
     */
    private $projectDir;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function __construct(EpisodeRepository $episodeRepository, CardBuilder $cardBuilder, FlysystemAssetManager $flysystemAssetManager, string $projectDir, ObjectManager $objectManager)
    {
        $this->episodeRepository = $episodeRepository;
        $this->cardBuilder = $cardBuilder;
        $this->flysystemAssetManager = $flysystemAssetManager;
        $this->objectManager = $objectManager;
        $this->projectDir = $projectDir;
    }

    public function __invoke(CreateHdCard $command)
    {
        $episode = $this->episodeRepository->find($command->episodeId);

        if (! $episode) {
            return;
        }

        $cardConfiguration = CardConfiguration::create1080pCard()
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
            Episode::generateHdCardPath($episode, 'hd.jpg'),
            image_type_to_mime_type($cardImageExifImageType),
            filesize($cardFileName)
        );

        $this->flysystemAssetManager->writeOrUpdateFromFile($cardFile, $cardFileName);

        unlink($cardFileName);

        $episode->setHdCard($cardFile);

        $this->objectManager->flush();
    }
}