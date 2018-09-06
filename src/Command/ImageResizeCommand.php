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
use Imagine\Image\Box;
use Imagine\Image\ImagineInterface;
use Media42\Selector\MediaSelector;
use Media42\TableGateway\MediaTableGateway;

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
        $this->dimensionName = $dimension['name'];

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
            $this->addError('media', 'media not found');

            return;
        }

        $this->mediaOptions = $this->getServiceManager()->get(MediaOptions::class);
        if ($this->dimension === null) {
            $this->dimension = $this->mediaOptions->getDimension($this->dimensionName);
        }

        if (!$this->dimension === null) {
            $this->addError('dimensions', 'dimensionName invalid');

            return;
        }

        $this->imagine = $this->getServiceManager()->get('Imagine');
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        $filenameParts = explode('.', $this->media->getFilename());

        if (count($filenameParts) == 1) {
            $extension = '';
            $filename = $filenameParts[0];
        } else {
            $extension = array_pop($filenameParts);
            $filename = implode('.', $filenameParts);
        }

        $filename .= '-'
            . (($this->dimension['width'] == 'auto') ? '000' : $this->dimension['width'])
            . 'x'
            . (($this->dimension['height'] == 'auto') ? '000' : $this->dimension['height'])
            . '.' . $extension;

        $fullPath = $this->mediaOptions->getPath() . $this->media->getDirectory() . $filename;

        $media = new Media();
        $media->setFilename($filename)
            ->setDirectory($this->media->getDirectory());

        if (file_exists($fullPath)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $media->setMimeType(finfo_file($finfo, $fullPath));
            $media->setSize(filesize($fullPath));

            return $media;
        }

        $image = $this->imagine->open($this->mediaOptions->getPath() . $this->media->getDirectory() . $this->media->getFilename());

        $resizeType = (!empty($this->dimension['mode'])) ? $this->dimension['mode'] : 'crop';
        switch ($resizeType) {
            case 'crop':

                $meta = $this->media->getMeta();
                if (!empty($meta['crop'][$this->dimensionName])) {
                    $boxWidth = $meta['crop'][$this->dimensionName]['width'];
                    $boxHeight = $meta['crop'][$this->dimensionName]['height'];
                    $offsetX = $meta['crop'][$this->dimensionName]['x'];
                    $offsetY = $meta['crop'][$this->dimensionName]['y'];
                } else {
                    $imageSize = $image->getSize();
                    $offsetX = 0;
                    $offsetY = 0;

                    $imageRatio = $imageSize->getWidth() / $imageSize->getHeight();

                    if ($this->dimension['width'] != 'auto' && $this->dimension['height'] != 'auto') {
                        $dimensionRatio = $this->dimension['width'] / $this->dimension['height'];

                        if ($imageRatio < $dimensionRatio) {
                            $boxWidth = $imageSize->getWidth();
                            $boxHeight = round($imageSize->getWidth() / $dimensionRatio);
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
                }

                /** @var ImageCropCommand $imageCropCmd */
                $imageCropCmd = $this->getCommand(ImageCropCommand::class);

                return $imageCropCmd
                    ->setBoxWidth($boxWidth)
                    ->setBoxHeight($boxHeight)
                    ->setDimension($this->dimension)
                    ->setOffsetX($offsetX)
                    ->setOffsetY($offsetY)
                    ->setMedia($this->media)
                    ->run();

                break;
            case 'resize':

                $width = (($this->dimension['width'] == 'auto') ? PHP_INT_MAX : $this->dimension['width']);
                $height = (($this->dimension['height'] == 'auto') ? PHP_INT_MAX : $this->dimension['height']);

                $image->thumbnail(new Box($width, $height))->save($fullPath, [
                    'jpeg_quality' => 75,
                    'png_compression_level' => 7,
                ]);
                break;
        }

        return $media;
    }
}
