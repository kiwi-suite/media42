<?php

/*
 * media42
 *
 * @package media42
 * @link https://github.com/raum42/media42
 * @copyright Copyright (c) 2010 - 2016 raum42 (https://www.raum42.at)
 * @license MIT License
 * @author raum42 <kiwi@raum42.at>
 */

namespace Media42\Event;

use Core42\Stdlib\DefaultGetterTrait;
use Media42\Command\ImageResizeCommand;
use Media42\Command\ImageSizeCommand;
use Media42\MediaOptions;
use Media42\Model\Media;
use Media42\Selector\MediaSelector;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\ServiceManager;

class MediaEventListener extends AbstractListenerAggregate
{
    use DefaultGetterTrait;

    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     * @param int $priority
     * @return void
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $events->attach(MediaEvent::EVENT_ADD, [$this, 'setImageSize']);
        $events->attach(MediaEvent::EVENT_ADD, [$this, 'resizeImages']);
        $events->attach(MediaEvent::EVENT_ADD, [$this, 'setCache']);
        $events->attach(MediaEvent::EVENT_EDIT, [$this, 'setCache']);
        $events->attach(MediaEvent::EVENT_DELETE, [$this, 'removeCache']);
    }

    /**
     * @param MediaEvent $event
     */
    public function setImageSize(MediaEvent $event)
    {
        /** @var Media $media */
        $media = $event->getTarget();

        if (substr($media->getMimeType(), 0, 6) != 'image/') {
            return;
        }

        $cmd = $this->getCommand(ImageSizeCommand::class);
        $cmd->setMedia($media)
            ->run();
    }

    /**
     * @param MediaEvent $event
     */
    public function resizeImages(MediaEvent $event)
    {
        /** @var Media $media */
        $media = $event->getTarget();

        if (substr($media->getMimeType(), 0, 6) != 'image/') {
            return;
        }

        foreach ($this->getServiceManager()->get(MediaOptions::class)->getDimensions() as $dimension) {
            /* @var ImageResizeCommand $cmd */
            $cmd = $this->getCommand(ImageResizeCommand::class);
            $cmd->setMedia($media)
                ->setDimension($dimension)
                ->run();
        }
    }

    /**
     * @param MediaEvent $event
     */
    public function setCache(MediaEvent $event)
    {
        $this
            ->getSelector(MediaSelector::class)
            ->setDisableCache(true)
            ->setMediaId($event->getTarget()->getId())
            ->getResult();
    }

    /**
     * @param MediaEvent $event
     */
    public function removeCache(MediaEvent $event)
    {
        $this->getCache('media')->deleteItem($event->getTarget()->getId());
    }
}
