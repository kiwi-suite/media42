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

namespace Media42\View\Helper;

use Media42\Model\Media as MediaModel;
use Media42\TableGateway\MediaTableGateway;
use Psr\Cache\CacheItemPoolInterface;
use Zend\View\Helper\AbstractHelper;

class Media extends AbstractHelper
{
    /**
     * @var MediaTableGateway
     */
    protected $mediaTableGateway;

    /**
     * @var CacheItemPoolInterface
     */
    protected $cache;

    /**
     * @param MediaTableGateway $mediaTableGateway
     * @param CacheItemPoolInterface $cache
     */
    public function __construct(MediaTableGateway $mediaTableGateway, CacheItemPoolInterface $cache)
    {
        $this->mediaTableGateway = $mediaTableGateway;
        $this->cache = $cache;
    }

    /**
     * @param null $mediaId
     * @return $this|MediaModel
     */
    public function __invoke($mediaId = null)
    {
        if ($mediaId !== null) {
            return $this->getMedia($mediaId);
        }

        return $this;
    }

    /**
     * @param $mediaId
     * @return MediaModel|null
     * @throws \Exception
     */
    public function getMedia($mediaId)
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
