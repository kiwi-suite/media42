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

use Zend\Stdlib\AbstractOptions;

class MediaOptions extends AbstractOptions
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var array
     */
    protected $images = [];

    /**
     * @var array
     */
    protected $categories = [];

    /**
     * @var string
     */
    protected $uploadHost = '';

    /**
     * @var bool
     */
    protected $prependBasePath = true;

    /**
     * @var array
     */
    protected $allowedMimeTypes = null;

    /**
     * @param $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        if (!empty($url)) {
            $url = rtrim($url, '/') . '/';
        }

        $this->url = $url;

        return $this;
    }

    /**
     * @param array $images
     * @return $this;
     */
    public function setImages(array $images)
    {
        $this->images = $images;

        return $this;
    }

    /**
     * @return array
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param bool $includeSystem
     * @return array
     */
    public function getDimensions($includeSystem = true)
    {
        $dimensions = [];

        if ($includeSystem === true) {
            return $this->images['dimensions'];
        }

        foreach ($this->images['dimensions'] as $name => $_dimensions) {
            if ($includeSystem === false
                && array_key_exists('system', $_dimensions)
                && $_dimensions['system'] === true
            ) {
                continue;
            }

            $dimensions[$name] = $_dimensions;
        }

        return $dimensions;
    }

    /**
     * @param string $dimension
     * @return bool
     */
    public function hasDimension($dimension)
    {
        return array_key_exists($dimension, $this->images['dimensions']);
    }

    /**
     * @param $dimension
     * @return array|null
     */
    public function getDimension($dimension)
    {
        if ($this->hasDimension($dimension)) {
            return $this->images['dimensions'][$dimension];
        }
    }

    /**
     * @param array $categories
     * @return $this
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return array
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @return string
     */
    public function getUploadHost()
    {
        return $this->uploadHost;
    }

    /**
     * @param string $uploadHost
     */
    public function setUploadHost($uploadHost)
    {
        $this->uploadHost = $uploadHost;
    }

    /**
     * @return boolean
     */
    public function getPrependBasePath()
    {
        return $this->prependBasePath;
    }

    /**
     * @param boolean $prependBasePath
     * @return MediaOptions
     */
    public function setPrependBasePath($prependBasePath)
    {
        $this->prependBasePath = $prependBasePath;

        return $this;
    }

    /**
     * @return array
     */
    public function getAllowedMimeTypes()
    {
        return $this->allowedMimeTypes;
    }

    /**
     * @param array $allowedMimeTypes
     * @return MediaOptions
     */
    public function setAllowedMimeTypes($allowedMimeTypes)
    {
        $this->allowedMimeTypes = $allowedMimeTypes;
        return $this;
    }
}
