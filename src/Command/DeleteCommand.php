<?php
/**
 * media42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2016 raum42 OG (http://www.raum42.at)
 *
 */

namespace Media42\Command;

use Media42\Event\MediaEvent;
use Media42\Model\Media;
use Core42\Command\AbstractCommand;
use Media42\TableGateway\MediaTableGateway;

class DeleteCommand extends AbstractCommand
{
    /**
     * @var Media
     */
    protected $media;

    /**
     * @var int
     */
    protected $mediaId;

    /**
     * @param int $mediaId
     * @return $this
     */
    public function setMediaId($mediaId)
    {
        $this->mediaId = $mediaId;
        return $this;
    }

    /**
     * @param Media $media
     * @return $this
     */
    public function setMedia(Media $media)
    {
        $this->media = $media;
        return $this;
    }

    /**
     *
     */
    protected function preExecute()
    {
        if ($this->mediaId > 0) {
            $this->media = $this->getTableGateway(MediaTableGateway::class)->selectByPrimary((int) $this->mediaId);
        }

        if (empty($this->media)) {
            $this->addError("media", "media not found");
        }
    }

    /**
     * @return mixed|void
     */
    protected function execute()
    {
        $this->getCommand(CleanupDataDirectory::class)
            ->setMedia($this->media)
            ->run();

        $this->getTableGateway(MediaTableGateway::class)->delete($this->media);

        $this
            ->getServiceManager()
            ->get('Media42\EventManager')
            ->trigger(MediaEvent::EVENT_DELETE, $this->media);

        $this->getCache('media')->deleteItem($this->media->getId());
    }
}
