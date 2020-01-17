<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace GeebyDeebyLocal;

use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap($event)
    {
        $application  = $event->getApplication();
        $events       = $application->getEventManager();
        $sharedEvents = $events->getSharedManager();
        $injectTemplateListener  = new \GeebyDeebyLocal\View\InjectTemplateListener();
        $sharedEvents->attach('Zend\Stdlib\DispatchableInterface', MvcEvent::EVENT_DISPATCH, [$injectTemplateListener, 'injectTemplate'], -89);
        \EasyRdf\RdfNamespace::set('dime', 'https://dimenovels.org/ontology#');
        \EasyRdf\RdfNamespace::set('rda', 'http://rdaregistry.info/Elements/u/');
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }
}
