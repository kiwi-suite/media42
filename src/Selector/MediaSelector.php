<?php
namespace Media42\Selector;

use Core42\Selector\AbstractSelector;
use Core42\Selector\CacheAbleTrait;
use Media42\TableGateway\MediaTableGateway;

class MediaSelector extends AbstractSelector
{
    use CacheAbleTrait;

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
     * @return string
     */
    protected function getCacheName()
    {
        return "media";
    }

    /**
     * @return string
     */
    protected function getCacheKey()
    {
        return $this->mediaId;
    }

    /**
     * @return mixed
     */
    protected function getUncachedResult()
    {
        return $this->getTableGateway(MediaTableGateway::class)->selectByPrimary((int) $this->mediaId);
    }
}
