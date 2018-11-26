<?php

namespace App\Command;

use App\Messages\Commands\CreateFacebookCard;
use App\Messages\Commands\CreateHdCard;
use App\Messages\Commands\CreateTwitterCard;
use App\Repository\EpisodeRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

class CardsRebuildAllCommand extends Command
{
    protected static $defaultName = 'app:cards:rebuild-all';

    /**
     * @var EpisodeRepository
     */
    private $episodeRepository;

    /**
     * @var MessageBusInterface
     */
    private $commandBus;

    public function __construct(EpisodeRepository $episodeRepository, MessageBusInterface $commandBus)
    {
        parent::__construct();

        $this->episodeRepository = $episodeRepository;
        $this->commandBus = $commandBus;
    }


    protected function configure()
    {
        $this
            ->setDescription('Rebuild all cards')
            ->addArgument('type', InputArgument::OPTIONAL, 'Type')
            ->addOption('episode', null, InputOption::VALUE_OPTIONAL, 'Episode ID')
        ;
    }

    // bin/console app:cards:rebuild-all
    // bin/console app:cards:rebuild-all itunes
    // bin/console app:cards:rebuild-all twitter

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');
        $episodeId = $input->getOption('episode');

        $episodes = $episodeId
            ? [$this->episodeRepository->find($episodeId)]
            : $this->episodeRepository->findAll()
        ;

        foreach ($episodes as $episode) {
            if (! $type || $type == 'twitter') {
                $this->commandBus->dispatch(new CreateTwitterCard($episode->getId()));
            }

            if (! $type || $type == 'facebook') {
                $this->commandBus->dispatch(new CreateFacebookCard($episode->getId()));
            }

            if (! $type || $type == 'hd') {
                $this->commandBus->dispatch(new CreateHdCard($episode->getId()));
            }
        }
    }
}
