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

use Admin42\ModuleManager\Feature\AdminAwareModuleInterface;
use Admin42\ModuleManager\GetAdminConfigTrait;
use Core42\ModuleManager\GetConfigTrait;
use Core42\Mvc\Environment\Environment;
use Media42\Event\MediaEventListener;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\DependencyIndicatorInterface;

class Module implements
    ConfigProviderInterface,
    BootstrapListenerInterface,
    DependencyIndicatorInterface,
    AdminAwareModuleInterface
{
    use GetConfigTrait;
    use GetAdminConfigTrait;

    /**
     * Listen to the bootstrap event
     *
     * @param EventInterface $e
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();
        if (!$serviceManager->get(Environment::class)->is(\Admin42\Module::ENVIRONMENT_ADMIN)) {
            return;
        }
        $serviceManager
            ->get(MediaEventListener::class)
            ->attach($serviceManager->get('Media42\EventManager'));
    }

    /**
     * Expected to return an array of modules on which the current one depends on
     *
     * @return array
     */
    public function getModuleDependencies()
    {
        return [
            'Core42',
            'Admin42',
        ];
    }
}
