<?php
/**
 * media42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2016 raum42 OG (http://www.raum42.at)
 *
 */

namespace Media42\View\Helper;

use Media42\MediaUrl as MediaUrlObject;
use Zend\View\Helper\AbstractHelper;

class MediaUrl extends AbstractHelper
{
    /**
     * @var MediaUrlObject;
     */
    protected $mediaUrl;

    /**
     * @param MediaUrlObject $mediaUrl
     */
    public function __construct(MediaUrlObject $mediaUrl)
    {
        $this->mediaUrl = $mediaUrl;
    }

    /**
     * @param $mediaId
     * @param string|null $dimension
     * @return string
     */
    public function __invoke($mediaId, $dimension = null)
    {
        return $this->mediaUrl->getUrl($mediaId, $dimension);
    }
}
