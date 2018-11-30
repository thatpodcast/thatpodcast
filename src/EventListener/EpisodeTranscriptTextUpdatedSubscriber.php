<?php

namespace App\EventListener;

use App\Entity\Episode;
use App\Messages\Commands\ProcessTranscript;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class EpisodeTranscriptTextUpdatedSubscriber implements EventSubscriber
{
    /**
     * @var MessageBusInterface
     */
    private $commandBus;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(MessageBusInterface $commandBus, LoggerInterface $logger)
    {
        $this->commandBus = $commandBus;
        $this->logger = $logger;
    }

    public function getSubscribedEvents()
    {
        return [
            'postPersist',
            'postUpdate',
        ];
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->process($args);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->process($args);
    }

    public function process(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (! $entity instanceof Episode) {
            return;
        }

        $changeSet = $args->getEntityManager()->getUnitOfWork()->getEntityChangeSet($entity);

        $changesWeCareAbout = collect($changeSet)
            ->only([
                'transcriptText',
            ])
        ;

        $hasNoChanges = $changesWeCareAbout->keys()->isEmpty();

        if ($hasNoChanges) {
            return;
        }

        if (is_null($entity->getTranscriptText()) || strlen(trim($entity->getTranscriptText())) === 0) {
            // Do not do anything if the transcription text is blank...
            return;
        }

        $this->commandBus->dispatch(new ProcessTranscript($entity->getId()));
    }
}