<?php

/*
 * media42
 *
 * @package media42
 * @link https://github.com/kiwi-suite/media42
 * @copyright Copyright (c) 2010 - 2017 kiwi suite (https://kiwi-suite.com)
 * @license MIT License
 * @author kiwi suite <dev@kiwi-suite.com>
 */

namespace Media42\Command;

use Media42\MediaOptions;
use Media42\Model\Media;
use Core42\Command\AbstractCommand;
use Imagine\Image\ImagineInterface;
use Media42\TableGateway\MediaTableGateway;

class ImageSizeCommand extends AbstractCommand
{
    /**
     * @var Media
     */
    protected $media;

    /**
     * @var ImagineInterface
     */
    protected $imagine;


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
     * @throws \Exception
     */
    protected function preExecute()
    {
        if (empty($this->media)) {
            $this->addError('media', 'media not found');

            return;
        }

        $this->imagine = $this->getServiceManager()->get('Imagine');
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        $mediaOptions = $this->getServiceManager()->get(MediaOptions::class);

        $image = $this->imagine->open($mediaOptions->getPath() . $this->media->getDirectory() . $this->media->getFilename());
        $imageSize = $image->getSize();

        $meta = $this->media->getMeta();

        if (empty($meta)) {
            $meta = [];
        }
        if (empty($meta['original'])){
            $meta['original'] = [];
        }

        $meta['original']['width'] = $imageSize->getWidth();
        $meta['original']['height'] = $imageSize->getHeight();

        $this->media->setMeta($meta);
        $this->getTableGateway(MediaTableGateway::class)->update($this->media);

        return $this->media;
    }
}
