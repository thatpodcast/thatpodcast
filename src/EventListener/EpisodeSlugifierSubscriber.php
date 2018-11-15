<?php

namespace App\EventListener;

use App\Entity\Episode;
use Cocur\Slugify\SlugifyInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class EpisodeSlugifierSubscriber implements EventSubscriber
{
    /**
     * @var SlugifyInterface
     */
    private $slugify;

    /**
     * EpisodeSlugifierSubscriber constructor.
     * @param SlugifyInterface $slugify
     */
    public function __construct(SlugifyInterface $slugify)
    {
        $this->slugify = $slugify;
    }

    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'preUpdate',
        );
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->slugify($args);
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->slugify($args);
    }

    public function slugify(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (! $entity instanceof Episode) {
            return;
        }

        $entity->setPath($this->slugify->slugify(sprintf(
            'Episode %s %s',
            $entity->getNumber(),
            $entity->getTitle()
        )));
    }
}
