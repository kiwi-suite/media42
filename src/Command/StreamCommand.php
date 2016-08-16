<?php
/**
 * media42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2016 raum42 OG (http://www.raum42.at)
 *
 */

namespace Media42\Command;

use Media42\Model\Media;
use Core42\Command\AbstractCommand;
use Media42\TableGateway\MediaTableGateway;
use Zend\Http\Headers;
use Zend\Http\Response\Stream;

class StreamCommand extends AbstractCommand
{
    /**
     * @var array
     */
    protected $dimension;

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
     * @param string $dimension
     * @return $this
     */
    public function setDimension($dimension)
    {
        $this->dimension = $dimension;
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
        $headers = new Headers();
        $stream = new Stream();

        if (substr($this->media->getMimeType(), 0, 6) === "image/" && strlen($this->dimension)) {
            $cmd = $this->getCommand(ImageResizeCommand::class);
            $media = $cmd->setMedia($this->media)
                ->setDimensionName($this->dimension)
                ->run();
            $stream->setStream(fopen($media->getDirectory() . $media->getFilename(), 'r'));
            $headers->addHeaders([
                'Content-Type' => $media->getMimeType(),
                'Content-Length' => $media->getSize()
            ]);
        } else {
            $stream->setStream(fopen($this->media->getDirectory() . $this->media->getFilename(), 'r'));
            $headers->addHeaders([
                'Content-Disposition' => 'attachment; filename="' . $this->media->getFilename() .'"',
                'Content-Type' => $this->media->getMimeType(),
                'Content-Length' => $this->media->getSize()
            ]);
        }

        $stream->setHeaders($headers);
        return $stream;
    }
}
