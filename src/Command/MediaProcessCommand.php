<?php

namespace App\Command;

use App\Messages\Commands\ProcessPristineMedia;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

class MediaProcessCommand extends Command
{
    /**
     * @var MessageBusInterface
     */
    protected $commandBus;

    protected static $defaultName = 'app:media:process';

    /**
     * MediaProcessCommand constructor.
     * @param MessageBusInterface $commandBus
     */
    public function __construct(MessageBusInterface $commandBus)
    {
        parent::__construct();
        $this->commandBus = $commandBus;
    }

    protected function configure()
    {
        $this
            ->setDescription('Process pristine media')
            ->addArgument('episode_id', InputArgument::REQUIRED, 'Episode ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $episodeId = $input->getArgument('episode_id');

        $this->commandBus->dispatch(new ProcessPristineMedia($episodeId));
    }
}
