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
use Imagine\Image\Box;
use Imagine\Image\ImagineInterface;

class ImageResizeCommand extends AbstractCommand
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
     * @throws \Exception
     */
    protected function preExecute()
    {
        if ($this->mediaId > 0) {
            $this->media = $this->getTableGateway('Admin42\Media')->selectByPrimary((int) $this->mediaId);
        }

        if (empty($this->media)) {
            $this->addError("media", "media not found");

            return;
        }

        $mediaConfig = $this->getServiceManager()->get('config')['media'];
        if (!isset($mediaConfig['images']['dimensions'][$this->dimensionName])) {
            $this->addError("dimensions", "dimensionName invalid");

            return;
        }

        $this->dimension = $mediaConfig['images']['dimensions'][$this->dimensionName];

        $this->imagine = $this->getServiceManager()->get('Imagine');
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        $filenameParts = explode(".", $this->media->getFilename());

        $extension = array_pop($filenameParts);
        $filename = implode(".", $filenameParts);

        $filename .= '-'
            . (($this->dimension['width'] == 'auto') ? '000' : $this->dimension['width'])
            . 'x'
            . (($this->dimension['height'] == 'auto') ? '000' : $this->dimension['height']);

        $media = new Media();
        $media->setFilename($filename . '.' . $extension)
            ->setDirectory($this->media->getDirectory());

        if (file_exists($media->getDirectory() . $media->getFilename())) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $media->setMimeType(finfo_file($finfo, $media->getDirectory() . $media->getFilename()));
            $media->setSize(filesize($media->getDirectory() . $media->getFilename()));

            return $media;
        }

        $image = $this->imagine->open($this->media->getDirectory() . $this->media->getFilename());
        $imageSize = $image->getSize();

        $imageRatio = $imageSize->getWidth() / $imageSize->getHeight();

        if ($this->dimension['width'] != "auto" && $this->dimension['height'] != "auto") {
            $dimensionRatio = $this->dimension['width'] / $this->dimension['height'];

            if ($imageRatio < $dimensionRatio) {
                $boxWidth = $imageSize->getWidth();
                $boxHeight = round($imageSize->getWidth()/ $dimensionRatio);
            } elseif ($imageRatio > $dimensionRatio) {
                $boxHeight = $imageSize->getHeight();
                $boxWidth = round($imageSize->getHeight() * $dimensionRatio);
            } else {
                $boxWidth = $imageSize->getWidth();
                $boxHeight = $imageSize->getHeight();
            }
        } else {
            $boxWidth = $imageSize->getWidth();
            $boxHeight = $imageSize->getHeight();
        }

        /** @var ImageCropCommand $imageCropCmd */
        $imageCropCmd = $this->getCommand('Media42\Media\ImageCrop');
        return $imageCropCmd->setBoxWidth($boxWidth)
            ->setBoxHeight($boxHeight)
            ->setDimensionName($this->dimensionName)
            ->setOffsetX(0)
            ->setOffsetY(0)
            ->setMedia($this->media)
            ->run();
    }
}
