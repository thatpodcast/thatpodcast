<?php

namespace App\EventSubscriber;

use App\Entity\Episode;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Event\Events;

class EpisodeBackgroundImageUploadSubscriber implements EventSubscriberInterface
{
    public function onPreUpload(Event $event)
    {
        if (! $event->getObject() instanceof Episode) {
            return;
        }

        if ($event->getMapping()->getMappingName() !== 'episode_background_image') {
            return;
        }

        /** @var UploadedFile $file */
        $file = $event->getMapping()->getFile($event->getObject());

        /** @var Episode $episode */
        $episode = $event->getObject();

        list ($width, $height) = getimagesize($file->getRealPath());
        $episode->setBackgroundImageWidth($width);
        $episode->setBackgroundImageHeight($height);
    }

    public static function getSubscribedEvents()
    {
        return [
           Events::PRE_UPLOAD => 'onPreUpload',
        ];
    }
}
