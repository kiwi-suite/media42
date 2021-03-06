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

namespace Media42\Service;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Media42\MediaOptions;
use Media42\MediaUrl;
use Media42\Selector\MediaSelector;
use Media42\TableGateway\MediaTableGateway;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class MediaUrlFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $basePath = "";
        $request = $container->get('Request');
        if (method_exists($request, "getBasePath")) {
            $basePath = $container->get('Request')->getBasePath();
        }

        return new MediaUrl(
            $container->get('TableGateway')->get(MediaTableGateway::class),
            $container->get(MediaOptions::class),
            $container->get('Selector')->get(MediaSelector::class),
            $basePath
        );
    }
}
