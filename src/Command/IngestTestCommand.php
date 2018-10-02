<?php

namespace App\Command;

use App\Ingest\ExportClient;
use App\Podcast\Episode;
use App\Podcast\Episodes;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class IngestTestCommand extends Command
{
    protected static $defaultName = 'app:ingest-test';

    /**
     * @var Episodes
     */
    private $episodes;

    /**
     * IngestTestCommand constructor.
     * @param Episodes $episodes
     */
    public function __construct(Episodes $episodes)
    {
        $this->episodes = $episodes;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Test out the ingest process (export client)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $allEpisodes = collect($this->episodes->getAll());

        $allEpisodes->each(function (Episode $episode) {
            printf("%s: %s (%s)\n", $episode->getNumber(), $episode->getTitle(), $episode->getSubtitle());
        });

        print "-----\n";

        $episode = $this->episodes->findBySlug('episode-48-the-one-without-all-the-css');
        printf("%s: %s (%s)\n", $episode->getNumber(), $episode->getTitle(), $episode->getSubtitle());


        //$export = $this->exportClient->fetchExport();
    }
}
