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
use Media42\MediaUrl;
use Media42\TableGateway\MediaTableGateway;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MediaUrlFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new MediaUrl(
            $serviceLocator->get('TableGateway')->get(MediaTableGateway::class),
            $serviceLocator->get(MediaOptions::class),
            $serviceLocator->get('Cache\Media')
        );
    }
}
