<?php
/**
 * media42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2016 raum42 OG (http://www.raum42.at)
 *
 */

namespace Media42\Link\Adapter\Service;

use Media42\Link\Adapter\MediaLink as MediaLinkAdapter;
use Media42\MediaOptions;
use Media42\TableGateway\MediaTableGateway;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MediaLinkFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return MediaLinkAdapter
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new MediaLinkAdapter(
            $serviceLocator->get('TableGateway')->get(MediaTableGateway::class),
            $serviceLocator->get(MediaOptions::class)
        );
    }
}
