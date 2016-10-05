<?php
/**
 * media42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2016 raum42 OG (http://www.raum42.at)
 *
 */

namespace Media42;

use Admin42\ModuleManager\Feature\AdminAwareModuleInterface;
use Admin42\ModuleManager\GetAdminConfigTrait;
use Core42\ModuleManager\GetConfigTrait;
use Core42\Mvc\Environment\Environment;
use Media42\Event\MediaEventListener;
use Media42\FormElements\FileSelect;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\DependencyIndicatorInterface;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\Factory\InvokableFactory;
use Media42\View\Helper\Form\FileSelect as ViewFileSelect;

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
        $e->getApplication()->getEventManager()->attach(
            MvcEvent::EVENT_ROUTE,
            function(MvcEvent $event){
                /* @var \Zend\Mvc\Application $application */
                $application    = $event->getApplication();
                $serviceManager = $application->getServiceManager();

                /** @var Environment $environment */
                $environment = $serviceManager->get(Environment::class);

                if (!$environment->is(\Admin42\Module::ENVIRONMENT_ADMIN)) {
                    return;
                }

                $viewHelperManager = $serviceManager->get('ViewHelperManager');

                $angular = $viewHelperManager->get('angular');

                $mediaOptions = $serviceManager->get(MediaOptions::class);

                $angular->addJsonTemplate("mediaConfig", [
                    "baseUrl" => $mediaOptions->getUrl(),
                    "dimensions" => $mediaOptions->getDimensions(),
                ]);
            },
            100
        );

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
            'Admin42'
        ];
    }
}
