<?php

namespace App\Command;

use App\Entity\Episode;
use App\FlysystemAssetManager\File;
use App\FlysystemAssetManager\FlysystemAssetManager;
use App\Form\FileType;
use App\Podcast\Episodes;
use App\Repository\EpisodeRepository;
use Doctrine\Common\Persistence\ObjectManager;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Form\FormTypeInterface;

class IngestCommand extends Command
{
    protected static $defaultName = 'app:ingest';

    /**
     * @var Episodes
     */
    private $episodes;

    /**
     * @var EpisodeRepository
     */
    private $episodeRepository;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var PropertyMappingFactory
     */
    private $propertyMappingFactory;

    /**
     * @var FlysystemAssetManager
     */
    private $flysystemAssetManager;

    /**
     * IngestTestCommand constructor.
     * @param Episodes $episodes
     * @param EpisodeRepository $episodeRepository
     * @param ObjectManager $objectManager
     */
    public function __construct(Episodes $episodes, EpisodeRepository $episodeRepository, ObjectManager $objectManager, FlysystemAssetManager $flysystemAssetManager)
    {
        $this->episodes = $episodes;
        $this->episodeRepository = $episodeRepository;
        $this->objectManager = $objectManager;
        $this->flysystemAssetManager = $flysystemAssetManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Ingest Episodes from old site\'s export')
            ->addOption(
                'force',
                null,
                InputOption::VALUE_NONE,
                'Force overwrite of Episodes already ingested'
            );
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $force = $input->getOption('force');

        $allExportedEpisodes = collect($this->episodes->getAll())->slice(0, 3);

        $allExportedEpisodes->each(function (Episode $exportedEpisode) use ($io, $force) {
            $existingEpisode = $this->episodeRepository->findOneByGuid($exportedEpisode->getGuid());

            if ($existingEpisode && ! $force) {
                return;
            }

            if ($existingEpisode) {
                $io->writeln(sprintf("%s: %s already exists", $existingEpisode->getNumber(), $existingEpisode->getTitle()));
                if ($existingEpisode->getBackgroundImageUrl()) {
                    $io->writeln(sprintf(" - removing existing background image: %s", $this->flysystemAssetManager->getUrl($existingEpisode->getBackgroundImage())));
                    $this->flysystemAssetManager->delete($existingEpisode->getBackgroundImage());
                    $existingEpisode->setBackgroundImageUrl(null);
                }

                if ($existingEpisode->getPristineMediaUrl()) {
                    if ($this->flysystemAssetManager->exists($existingEpisode->getPristineMedia())) {
                        $io->writeln(sprintf(" - removing existing pristine media: %s", $this->flysystemAssetManager->getUrl($existingEpisode->getPristineMedia())));
                        $this->flysystemAssetManager->delete($existingEpisode->getPristineMedia());
                        $existingEpisode->setPristineMediaUrl(null);
                    }
                }

                if ($existingEpisode->getMediaUrl()) {
                    if ($this->flysystemAssetManager->exists($existingEpisode->getMedia())) {
                        $io->writeln(sprintf(" - removing existing media: %s", $this->flysystemAssetManager->getUrl($existingEpisode->getMedia())));
                        $this->flysystemAssetManager->delete($existingEpisode->getMedia());
                        $existingEpisode->setMediaUrl(null);
                    }
                }

                $existingEpisode->refreshFrom($exportedEpisode);
            } else {
                $this->objectManager->persist($exportedEpisode);
                $existingEpisode = $exportedEpisode;
            }

            if (! $existingEpisode->getPristineMediaUrl()) {
                $tmpFile = tempnam(sys_get_temp_dir(), 'guzzle-download');

                $io->writeln(' - downloading media URL for Pristine Media: '. $existingEpisode->getMediaUrl());
                $parts = array_reverse(explode('/', $existingEpisode->getMediaUrl()));
                $originalFileName = $parts[0];

                $tmpFile = tempnam(sys_get_temp_dir(), 'guzzle-download');
                $handle = fopen($tmpFile, 'w');
                $client = new Client(array(
                    'base_uri' => '',
                    'verify' => false,
                    'sink' => $tmpFile,
                    'curl.options' => array(
                        'CURLOPT_RETURNTRANSFER' => true,
                        'CURLOPT_FILE' => $handle,
                    )
                ));

                $response = $client->get($existingEpisode->getMediaUrl());
                fclose($handle);

                $pristineMediaFile = new File(
                    'content',
                    Episode::generatePristineMediaPath($existingEpisode, $originalFileName),
                    $response->getHeader('content-type')[0],
                    (int) $response->getHeader('content-length')[0]
                );

                $mediaFile = new File(
                    'content',
                    Episode::generateMediaPath($existingEpisode, $originalFileName),
                    $response->getHeader('content-type')[0],
                    (int) $response->getHeader('content-length')[0]
                );

                $this->flysystemAssetManager->writeFromFile($pristineMediaFile, $tmpFile);
                $this->flysystemAssetManager->writeFromFile($mediaFile, $tmpFile);

                $existingEpisode->setPristineMediaUrl($pristineMediaFile->toUrl());
                $existingEpisode->setMediaUrl($mediaFile->toUrl());
            }

            if (substr( $existingEpisode->getBackgroundImageUrl(), 0, 8 ) === "https://") {
                $parts = array_reverse(explode('/', $existingEpisode->getBackgroundImageUrl()));
                $originalFileName = $parts[0];

                $tmpFile = tempnam(sys_get_temp_dir(), 'guzzle-download');
                $handle = fopen($tmpFile, 'w');
                $client = new Client(array(
                    'base_uri' => '',
                    'verify' => false,
                    'sink' => $tmpFile,
                    'curl.options' => array(
                        'CURLOPT_RETURNTRANSFER' => true,
                        'CURLOPT_FILE' => $handle,
                    )
                ));

                $response = $client->get($existingEpisode->getBackgroundImageUrl());
                fclose($handle);

                $backgroundImageFile = new File(
                    'content',
                    Episode::generateBackgroundImagePath($existingEpisode, $originalFileName),
                    $response->getHeader('content-type')[0],
                    (int) $response->getHeader('content-length')[0]
                );

                $this->flysystemAssetManager->writeFromFile($backgroundImageFile, $tmpFile);

                $existingEpisode->setBackgroundImageUrl($backgroundImageFile->toUrl());
            }

            $this->objectManager->flush();
        });
    }
}
