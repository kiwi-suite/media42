<?php
namespace Media42\Stdlib\Service;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Media42\MediaUrl;
use Media42\Selector\MediaSelector;
use Media42\Stdlib\Media;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class MediaFactory implements FactoryInterface
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
        $mediaId = (isset($options['mediaId'])) ? $options['mediaId'] : 0;

        return new Media(
            $mediaId,
            $container->get('Selector')->get(MediaSelector::class),
            $container->get(MediaUrl::class)
        );
    }
}
