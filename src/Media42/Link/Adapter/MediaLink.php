<?php
/**
 * media42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2016 raum42 OG (http://www.raum42.at)
 *
 */

namespace Media42\Link\Adapter;

use Admin42\Link\Adapter\AdapterInterface;
use Media42\MediaOptions;
use Media42\Model\Media;
use Media42\TableGateway\MediaTableGateway;

class MediaLink implements AdapterInterface
{
    /**
     * @var MediaTableGateway
     */
    protected $mediaTableGateway;

    /**
     * @var MediaOptions
     */
    protected $mediaOptions;

    /**
     * @param MediaTableGateway $mediaTableGateway
     * @param MediaOptions $mediaOptions
     */
    public function __construct(MediaTableGateway $mediaTableGateway, MediaOptions $mediaOptions)
    {
        $this->mediaTableGateway = $mediaTableGateway;
        $this->mediaOptions = $mediaOptions;
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function assemble($value)
    {
        $media = $this->getLinkData($value);
        if (empty($media)) {
            return "";
        }

        return $this->mediaOptions->getUrl() . $media->getDirectory() . $media->getFilename();
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function getDisplayName($value)
    {
        $media = $this->getLinkData($value);
        if (empty($media)) {
            return "";
        }

        return $media->getTitle();
    }

    /**
     * @param $value
     * @return Media
     * @throws \Exception
     */
    protected function getLinkData($value)
    {
        return $this->mediaTableGateway->selectByPrimary((int) $value['id']);
    }
}
