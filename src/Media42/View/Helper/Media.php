<?php
/**
 * media42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2016 raum42 OG (http://www.raum42.at)
 *
 */

namespace Media42\View\Helper;

use Media42\Model\Media as MediaModel;
use Media42\TableGateway\MediaTableGateway;
use Zend\Cache\Storage\StorageInterface;
use Zend\View\Helper\AbstractHelper;

class Media extends AbstractHelper
{
    /**
     * @var MediaTableGateway
     */
    protected $mediaTableGateway;

    /**
     * @var StorageInterface
     */
    protected $cache;

    /**
     * @param MediaTableGateway $mediaTableGateway
     * @param StorageInterface $cache
     */
    public function __construct(MediaTableGateway $mediaTableGateway, StorageInterface $cache)
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
            return null;
        }
        if (!$this->cache->hasItem('media_'. $mediaId)) {
            $this->cache->setItem(
                'media_'. $mediaId,
                $this->mediaTableGateway->selectByPrimary((int) $mediaId)
            );
        }

        return $this->cache->getItem('media_'. $mediaId);
    }
}
