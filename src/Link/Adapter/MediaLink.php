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

namespace Media42\Link\Adapter;

use Admin42\Link\Adapter\AdapterInterface;
use Media42\MediaOptions;
use Media42\MediaUrl;
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
     * @var MediaUrl
     */
    private $mediaUrl;

    /**
     * @param MediaTableGateway $mediaTableGateway
     * @param MediaOptions $mediaOptions
     */
    public function __construct(MediaTableGateway $mediaTableGateway, MediaOptions $mediaOptions, MediaUrl $mediaUrl)
    {
        $this->mediaTableGateway = $mediaTableGateway;
        $this->mediaOptions = $mediaOptions;
        $this->mediaUrl = $mediaUrl;
    }

    /**
     * @param mixed $value
     * @param array $options
     * @return string
     */
    public function assemble($value, $options = [])
    {
        $media = $this->getLinkData($value);
        if (empty($media)) {
            return '';
        }
        return $this->mediaUrl->getUrl($media->getId());
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function getDisplayName($value)
    {
        $media = $this->getLinkData($value);
        if (empty($media)) {
            return '';
        }

        return $media->getFilename();
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

    /**
     * @return array
     */
    public function getPartials()
    {
        return [
            'link/media.html' => 'link/media',
        ];
    }
}
