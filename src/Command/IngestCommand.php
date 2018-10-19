<?php

namespace App\Command;

use App\Entity\Episode;
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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Adapter\AdapterInterface;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;
use Vich\UploaderBundle\Storage\StorageInterface;

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
     * IngestTestCommand constructor.
     * @param Episodes $episodes
     * @param EpisodeRepository $episodeRepository
     * @param ObjectManager $objectManager
     */
    public function __construct(Episodes $episodes, EpisodeRepository $episodeRepository, ObjectManager $objectManager, StorageInterface $storage, PropertyMappingFactory $propertyMappingFactory)
    {
        $this->episodes = $episodes;
        $this->episodeRepository = $episodeRepository;
        $this->objectManager = $objectManager;
        $this->storage = $storage;
        $this->propertyMappingFactory = $propertyMappingFactory;

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

        $allExportedEpisodes = collect($this->episodes->getAll());

        $allExportedEpisodes->each(function (Episode $exportedEpisode) use ($io, $force) {
            $existingEpisode = $this->episodeRepository->findOneByGuid($exportedEpisode->getGuid());

            if ($existingEpisode && ! $force) {
                return;
            }

            if ($existingEpisode) {
                $io->writeln(sprintf("%s: %s already exists", $existingEpisode->getNumber(), $existingEpisode->getTitle()));
                if ($existingEpisode->getBackgroundImageUrl()) {
                    $io->writeln(sprintf(" - removing existing background image: %s", $this->storage->resolveUri($existingEpisode, 'backgroundImageFile')));
                    $this->storage->remove($existingEpisode, $this->propertyMappingFactory->fromField($existingEpisode, 'backgroundImageFile'));
                    $existingEpisode->setBackgroundImageUrl(null);
                    $existingEpisode->setBackgroundImageFile(null);
                }

                $existingEpisode->refreshFrom($exportedEpisode);
            } else {
                $this->objectManager->persist($exportedEpisode);
                $existingEpisode = $exportedEpisode;
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

                $client->get($existingEpisode->getBackgroundImageUrl());
                fclose($handle);

                $file = new UploadedFile($tmpFile, $originalFileName);
                $existingEpisode->setBackgroundImageFile($file);
            }

            $this->objectManager->flush();
        });
    }
}
