<?php
namespace Media42\Stdlib;

use Media42\MediaUrl;
use Media42\Selector\MediaSelector;

class Media
{
    /**
     * @var int
     */
    protected $mediaId;

    /**
     * @var MediaSelector
     */
    protected $mediaSelector;

    /**
     * @var MediaUrl
     */
    protected $mediaUrl;

    /**
     * Media constructor.
     * @param $mediaId
     * @param MediaSelector $mediaSelector
     * @param MediaUrl $mediaUrl
     */
    public function __construct(
        $mediaId,
        MediaSelector $mediaSelector,
        MediaUrl $mediaUrl
    ) {
        $this->mediaId = $mediaId;
        $this->mediaSelector = $mediaSelector;
        $this->mediaUrl = $mediaUrl;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->mediaId;
    }

    /**
     * @param $method
     * @param $attributes
     * @return mixed
     */
    public function __call($method, $attributes)
    {
        $media = $this->mediaSelector->setMediaId($this->mediaId)->getResult();

        return call_user_func_array([$media, $method], $attributes);
    }

    /**
     * @param mixed $dimension
     * @return string
     */
    public function getUrl($dimension = null)
    {
        return $this->mediaUrl->getUrl($this->mediaId, $dimension);
    }

    /**
     * @param null $dimension
     * @return array|void
     */
    public function getImageSize($dimension = null)
    {
        $mimeType = $this->getMimeType();
        if (\substr($mimeType, 0, 6) != 'image/' ||
            \substr($mimeType, 0, 9) == 'image/svg'
        ) {
            return;
        }

        $meta = $this->getMeta();

        if ($dimension === null) {
            if (empty($meta['original'])) {
                return [
                    'width' => 0,
                    'height' => 0,
                ];
            }

            return $meta['original'];
        }

        if (empty($meta['crop'][$dimension])) {
            if (empty($meta['original'])) {
                return [
                    'width' => 0,
                    'height' => 0,
                ];
            }

            return $meta['original'];
        }

        return [
            'width' => $meta['crop'][$dimension]['resizeWidth'],
            'height' => $meta['crop'][$dimension]['resizeHeight'],
        ];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getUrl();
    }
}
