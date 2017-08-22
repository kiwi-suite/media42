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

namespace Media42\View\Helper;

use Media42\Model\Media as MediaModel;
use Media42\Selector\MediaSelector;
use Media42\TableGateway\MediaTableGateway;
use Zend\View\Helper\AbstractHelper;

class Media extends AbstractHelper
{
    /**
     * @var MediaTableGateway
     */
    protected $mediaTableGateway;

    /**
     * @var MediaSelector
     */
    protected $mediaSelector;

    /**
     * @param MediaTableGateway $mediaTableGateway
     * @param MediaSelector $mediaSelector
     */
    public function __construct(MediaTableGateway $mediaTableGateway, MediaSelector $mediaSelector)
    {
        $this->mediaTableGateway = $mediaTableGateway;
        $this->mediaSelector = $mediaSelector;
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

        $media = $this->mediaSelector->setMediaId($mediaId)->getResult();

        return $media;
    }
}
