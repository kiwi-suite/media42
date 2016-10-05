<?php
namespace Media42\Event;

use Core42\Stdlib\DefaultGetterTrait;
use Media42\Command\ImageResizeCommand;
use Media42\MediaOptions;
use Media42\Model\Media;
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
        $events->attach(MediaEvent::EVENT_ADD, [$this, 'resizeImages']);
    }

    /**
     * @param MediaEvent $event
     */
    public function resizeImages(MediaEvent $event)
    {
        /** @var Media $media */
        $media = $event->getTarget();

        if (substr($media->getMimeType(), 0, 6) != "image/") {
            return;
        }

        foreach ($this->getServiceManager()->get(MediaOptions::class)->getDimensions() as $dimension)
        {
            /* @var ImageResizeCommand $cmd */
            $cmd = $this->getCommand(ImageResizeCommand::class);
            $cmd->setMedia($media)
                ->setDimension($dimension)
                ->run();
        }
    }
}
