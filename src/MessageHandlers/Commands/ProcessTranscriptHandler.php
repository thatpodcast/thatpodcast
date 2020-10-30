<?php

namespace App\MessageHandlers\Commands;

use App\Repository\EpisodeRepository;
use Doctrine\Common\Persistence\ObjectManager;

class ProcessTranscriptHandler
{
    /**
     * @var EpisodeRepository
     */
    private $episodeRepository;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function __construct(
        EpisodeRepository $episodeRepository,
        ObjectManager $objectManager
    ) {
        $this->episodeRepository = $episodeRepository;
        $this->objectManager = $objectManager;
    }

    public function __invoke(\App\Messages\Commands\ProcessTranscript $command)
    {
        $episode = $this->episodeRepository->find($command->episodeId);

        if (! $episode) {
            return;
        }

        if (is_null($episode->getTranscriptText()) || strlen(trim($episode->getTranscriptText())) === 0) {
            // Do not do anything if the transcription text is blank...
            return;
        }

        $transcriptHtml = collect(explode("\r\n\r\n", $episode->getTranscriptText()))->map(function ($line) {
            if (preg_match('/^(.+?)\s+(\([\d]{2}:[\d]{2}\)):\s+([\S].*)$/s', $line, $matches)) {
                list ($junk, $speaker, $time, $content) = $matches;

                return <<<CHUNK
<p class="transcript">
    <span class="transcript__speaker">$speaker</span>
    <span class="transcript__time">$time</span>
    <span class="transcript__content">$content</span>
</p>
CHUNK;
            }

            return '';
        })->implode("\n");

        $episode->setTranscriptHtml($transcriptHtml);

        $this->objectManager->flush();
        $this->objectManager->clear();
    }
}