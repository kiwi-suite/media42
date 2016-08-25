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
use Core42\Mvc\Environment\Environment;
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
    /**
     * @return array
     */
    public function getConfig()
    {
        return array_merge(
            include __DIR__ . '/../config/module.config.php',
            include __DIR__ . '/../config/cli.config.php',
            include __DIR__ . '/../config/caches.config.php',
            include __DIR__ . '/../config/navigation.config.php',
            include __DIR__ . '/../config/services.config.php',
            include __DIR__ . '/../config/routing.config.php'
        );
    }

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

                $admin = $viewHelperManager->get('admin');

                $mediaOptions = $serviceManager->get(MediaOptions::class);

                $admin->addJsonTemplate("mediaConfig", [
                    "baseUrl" => $mediaOptions->getUrl(),
                    "dimensions" => $mediaOptions->getDimensions(),
                ]);
            },
            100
        );
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

    /**
     * @return array
     */
    public function getAdminStylesheets()
    {
        return [
            '/assets/admin/media42/css/media42.min.css',
        ];
    }

    /**
     * @return array
     */
    public function getAdminJavascript()
    {
        return [
            '/assets/admin/media42/js/vendor.min.js',
            '/assets/admin/media42/js/media42.min.js'
        ];
    }

    /**
     * @return array
     */
    public function getAdminViewHelpers()
    {
        return [
            'factories' => [
                ViewFileSelect::class => InvokableFactory::class
            ],
            'aliases' => [
                'formfileselect' => ViewFileSelect::class,
            ],
        ];
    }

    /**
     * @return array
     */
    public function getAdminFormElements()
    {
        return [
            'factories' => [
                FileSelect::class => InvokableFactory::class
            ],
            'aliases' => [
                'fileSelect' => FileSelect::class,
            ],
        ];
    }
}
