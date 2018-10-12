<?php

namespace App\Command;

use App\Entity\Episode;
use App\Podcast\Episodes;
use App\Repository\EpisodeRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

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
     * IngestTestCommand constructor.
     * @param Episodes $episodes
     * @param EpisodeRepository $episodeRepository
     * @param ObjectManager $objectManager
     */
    public function __construct(Episodes $episodes, EpisodeRepository $episodeRepository, ObjectManager $objectManager)
    {
        $this->episodes = $episodes;
        $this->episodeRepository = $episodeRepository;
        $this->objectManager = $objectManager;

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

        $allExportedEpisodes->each(function (Episode $exportedEpisode) use ($force) {
            $existingEpisode = $this->episodeRepository->findOneByGuid($exportedEpisode->getGuid());

            if ($existingEpisode && ! $force) {
                return;
            }

            if ($existingEpisode) {
                $existingEpisode->refreshFrom($exportedEpisode);
            } else {
                $this->objectManager->persist($exportedEpisode);
            }
        });

        $this->objectManager->flush();
    }
}
