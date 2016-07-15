<?php
/**
 * media42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2016 raum42 OG (http://www.raum42.at)
 *
 */

namespace Media42\View\Helper\Service;

use Media42\TableGateway\MediaTableGateway;
use Media42\View\Helper\Media;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MediaFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return Media
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mediaTableGateway = $serviceLocator->getServiceLocator()->get('TableGateway')->get(MediaTableGateway::class);
        $cache = $serviceLocator->getServiceLocator()->get('Cache\Media');

        return new Media($mediaTableGateway, $cache);
    }
}
