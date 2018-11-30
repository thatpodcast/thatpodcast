<?php

namespace App\EventListener;

use App\Entity\Episode;
use App\Messages\Commands\CreateFacebookCard;
use App\Messages\Commands\CreateHdCard;
use App\Messages\Commands\CreateTwitterCard;
use App\Messages\Commands\ProcessPristineMedia;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class EpisodePristineMediaUpdatedSubscriber implements EventSubscriber
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
                'pristineMediaUrl',
                'pristineMediaUrlUpdated',
                'backgroundImageUrl',
                'backgroundImageUpdated',
                'title',
                'subtitle',
                'number',
                'published'
            ])
        ;

        if ($changesWeCareAbout->has('pristineMediaUrlUpdated')) {
            $left = $changesWeCareAbout->get('pristineMediaUrlUpdated')[0];
            $right = $changesWeCareAbout->get('pristineMediaUrlUpdated')[1];

            if ($left == $right) {
                $changesWeCareAbout->forget('pristineMediaUrlUpdated');
                $this->logger->notice('pristine media URL updated value is NOT actually different');
            } else {
                $this->logger->debug('pristine media URL updated value is actually different');
            }
        }

        if ($changesWeCareAbout->has('backgroundImageUpdated')) {
            $left = $changesWeCareAbout->get('backgroundImageUpdated')[0];
            $right = $changesWeCareAbout->get('backgroundImageUpdated')[1];

            if ($left == $right) {
                $changesWeCareAbout->forget('backgroundImageUpdated');
                $this->logger->notice('background image value is NOT actually different');
            } else {
                $this->logger->debug('background image value is actually different');
            }
        }

        if ($changesWeCareAbout->has('published')) {
            $left = $changesWeCareAbout->get('published')[0];
            $right = $changesWeCareAbout->get('published')[1];

            if ($left == $right) {
                $changesWeCareAbout->forget('published');
                $this->logger->notice('published value is NOT actually different');
            } else {
                $this->logger->debug('published value is actually different');
            }
        }

        $hasNoChanges = $changesWeCareAbout->keys()->isEmpty();

        if ($hasNoChanges) {
            return;
        }

        if (! is_null($entity->getPristineMediaUrl())) {
            $this->commandBus->dispatch(new ProcessPristineMedia($entity->getId()));
        }

        $this->commandBus->dispatch(new CreateFacebookCard($entity->getId()));
        $this->commandBus->dispatch(new CreateTwitterCard($entity->getId()));
        $this->commandBus->dispatch(new CreateHdCard($entity->getId()));
    }
}