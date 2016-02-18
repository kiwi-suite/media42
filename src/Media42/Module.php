<?php
/**
 * media42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2016 raum42 OG (http://www.raum42.at)
 *
 */

namespace Media42;

use Admin42\Mvc\Controller\AbstractAdminController;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;

class Module implements ConfigProviderInterface, BootstrapListenerInterface
{
    /**
     * @return array
     */
    public function getConfig()
    {
        return array_merge(
            include __DIR__ . '/../../config/module.config.php',
            include __DIR__ . '/../../config/cli.config.php',
            include __DIR__ . '/../../config/caches.config.php',
            include __DIR__ . '/../../config/navigation.config.php',
            include __DIR__ . '/../../config/routing.config.php'
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
        $e->getApplication()->getEventManager()->getSharedManager()->attach(
            'Zend\Mvc\Controller\AbstractController',
            MvcEvent::EVENT_DISPATCH,
            function ($e) {
                $controller      = $e->getTarget();

                if (!($controller instanceof AbstractAdminController)) {
                    return;
                }

                $sm = $e->getApplication()->getServiceManager();

                $viewHelperManager = $sm->get('viewHelperManager');

                $headScript = $viewHelperManager->get('headScript');
                $basePath = $viewHelperManager->get('basePath');

                $headScript->appendFile($basePath('/assets/media42/core/js/vendor.min.js'));
                $headScript->appendFile($basePath('/assets/media42/core/js/media42.min.js'));

                $admin = $viewHelperManager->get('admin');

                $mediaOptions = $sm->get(MediaOptions::class);

                $admin->addJsonTemplate("mediaConfig", [
                    "baseUrl" => $mediaOptions->getUrl(),
                    "dimensions" => $mediaOptions->getDimensions(),
                ]);

            },
            100
        );
    }
}
