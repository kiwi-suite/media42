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
