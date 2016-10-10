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

namespace Media42;

use Media42\Model\Media;
use Media42\TableGateway\MediaTableGateway;
use Psr\Cache\CacheItemPoolInterface;

class MediaUrl
{
    /**
     * @var MediaTableGateway;
     */
    protected $mediaTableGateway;

    /**
     * @var MediaOptions
     */
    protected $mediaOptions;

    /**
     * @var CacheItemPoolInterface
     */
    protected $cache;

    /**
     * @param MediaTableGateway $mediaTableGateway
     * @param MediaOptions $mediaOptions
     * @param CacheItemPoolInterface $cache
     */
    public function __construct(
        MediaTableGateway $mediaTableGateway,
        MediaOptions $mediaOptions,
        CacheItemPoolInterface $cache
    ) {
        $this->mediaTableGateway = $mediaTableGateway;
        $this->mediaOptions = $mediaOptions;
        $this->cache = $cache;
    }

    /**
     * @param $mediaId
     * @param null $dimension
     * @return string
     */
    public function getUrl($mediaId, $dimension = null)
    {
        $media = $this->loadMedia($mediaId);
        if (empty($media)) {
            return '';
        }

        if (substr($media->getMimeType(), 0, 6) != 'image/' || $dimension === null) {
            return $this->mediaOptions->getUrl() . $media->getDirectory() . $media->getFilename();
        }

        $dimension = $this->mediaOptions->getDimension($dimension);
        if ($dimension === null) {
            return '';
        }

        $pos = strrpos($media->getFilename(), '.');
        $filename = substr($media->getFilename(), 0, $pos);
        $extension = substr($media->getFilename(), $pos);

        $filename .= '-'
            . (($dimension['width'] == 'auto') ? '000' : $dimension['width'])
            . 'x'
            . (($dimension['height'] == 'auto') ? '000' : $dimension['height'])
            . $extension;


        return $this->mediaOptions->getUrl() . $media->getDirectory() . rawurlencode($filename);
    }

    /**
     * @param $mediaId
     * @return Media|null
     * @throws \Exception
     */
    public function loadMedia($mediaId)
    {
        if (empty($mediaId)) {
            return;
        }
        $item = $this->cache->getItem($mediaId);

        $media = $item->get();

        if (!$item->isHit()) {
            $media = $this->mediaTableGateway->selectByPrimary((int) $mediaId);
            $item->set($media);
            $this->cache->save($item);
        }

        return $media;
    }
}
