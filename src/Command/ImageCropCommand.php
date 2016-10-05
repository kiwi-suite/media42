<?php
/**
 * media42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2016 raum42 OG (http://www.raum42.at)
 *
 */

namespace Media42\Command;

use Core42\Command\AbstractCommand;
use Imagine\Image\Box;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Point;
use Media42\Event\MediaEvent;
use Media42\MediaOptions;
use Media42\Model\Media;
use Media42\TableGateway\MediaTableGateway;
use Zend\Json\Json;

class ImageCropCommand extends AbstractCommand
{
    /**
     * @var int
     */
    protected $mediaId;

    /**
     * @var Media
     */
    protected $media;

    /**
     * @var MediaOptions
     */
    protected $mediaOptions;

    /**
     * @var ImagineInterface
     */
    protected $imagine;

    /**
     * @var string
     */
    protected $dimensionName;

    /**
     * @var array
     */
    protected $dimension;

    /**
     * @var int
     */
    protected $offsetX;

    /**
     * @var int
     */
    protected $offsetY;

    /**
     * @var int
     */
    protected $boxWidth;

    /**
     * @var int
     */
    protected $boxHeight;

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
     * @param string $dimensionName
     * @return $this
     */
    public function setDimensionName($dimensionName)
    {
        $this->dimensionName = $dimensionName;
        return $this;
    }

    /**
     * @param array $dimension
     * @return $this
     */
    public function setDimension($dimension)
    {
        $this->dimension = $dimension;
        return $this;
    }

    /**
     * @param $boxWidth
     * @return $this
     */
    public function setBoxWidth($boxWidth)
    {
        $this->boxWidth = $boxWidth;
        return $this;
    }

    /**
     * @param $boxHeight
     * @return $this
     */
    public function setBoxHeight($boxHeight)
    {
        $this->boxHeight = $boxHeight;
        return $this;
    }

    /**
     * @param $offsetX
     * @return $this
     */
    public function setOffsetX($offsetX)
    {
        $this->offsetX = $offsetX;
        return $this;
    }

    /**
     * @param $offsetY
     * @return $this
     */
    public function setOffsetY($offsetY)
    {
        $this->offsetY = $offsetY;
        return $this;
    }

    /**
     * @throws \Exception
     */
    protected function preExecute()
    {
        if ($this->mediaId > 0) {
            $this->media = $this->getTableGateway(MediaTableGateway::class)->selectByPrimary((int) $this->mediaId);
        }

        if (empty($this->media)) {
            $this->addError("media", "media not found");

            return;
        }

        $this->mediaOptions = $this->getServiceManager()->get(MediaOptions::class);

        if ($this->dimension === null) {
            $this->dimension = $this->mediaOptions->getDimension($this->dimensionName);
        }

        if (!$this->dimension === null) {
            $this->addError("dimensions", "dimensionName invalid");
            return;
        }

        $this->imagine = $this->getServiceManager()->get('Imagine');

        $this->offsetX = round($this->offsetX);
        $this->offsetY = round($this->offsetY);
        $this->boxWidth = round($this->boxWidth);
        $this->boxHeight = round($this->boxHeight);
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        $sourceFullPath = $this->mediaOptions->getPath() . $this->media->getDirectory() . $this->media->getFilename();

        $filenameParts = explode(".", $this->media->getFilename());

        $extension = array_pop($filenameParts);
        $filename = implode(".", $filenameParts);

        $filename .= '-'
            . (($this->dimension['width'] == 'auto') ? '000' : $this->dimension['width'])
            . 'x'
            . (($this->dimension['height'] == 'auto') ? '000' : $this->dimension['height'])
            . '.' . $extension;

        $fullPath = $this->mediaOptions->getPath() . $this->media->getDirectory() . $filename;

        $media = new Media();
        $media->setFilename($filename)
            ->setDirectory($this->media->getDirectory());

        $width = (($this->dimension['width'] == 'auto') ? PHP_INT_MAX : $this->dimension['width']);
        $height = (($this->dimension['height'] == 'auto') ? PHP_INT_MAX : $this->dimension['height']);

        $this->imagine
            ->open($sourceFullPath)
            ->crop(new Point($this->offsetX, $this->offsetY), new Box($this->boxWidth, $this->boxHeight))
            ->thumbnail(new Box($width, $height))
            ->save(
                $fullPath,
                [
                    'jpeg_quality' => 75,
                    'png_compression_level' => 7
                ]
            );

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $media->setMimeType(finfo_file($finfo, $fullPath));
        $media->setSize(filesize($fullPath));


        $meta = $this->media->getMeta();
        $meta = (strlen($meta)) ? Json::decode($meta, true) : [];
        $meta[$this->dimensionName] = [
            'x' => $this->offsetX,
            'y' => $this->offsetY,
            'width' => $this->boxWidth,
            'height' => $this->boxHeight
        ];
        $this->media->setMeta(Json::encode($meta));
        if ($this->media->hasChanged()) {
            $this->media->setUpdated(new \DateTime());
        }
        $this->getTableGateway(MediaTableGateway::class)->update($this->media);

        $this
            ->getServiceManager()
            ->get('Media42\EventManager')
            ->trigger(MediaEvent::EVENT_CROP, $media);

        return $media;
    }
}
