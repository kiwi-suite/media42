<?php
/**
 * media42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2016 raum42 OG (http://www.raum42.at)
 *
 */

namespace Media42\Service;

use Media42\MediaOptions;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MediaOptionsFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return MediaOptions
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mediaConfig = $serviceLocator->get('config')['media'];

        return new MediaOptions($mediaConfig);
    }
}
