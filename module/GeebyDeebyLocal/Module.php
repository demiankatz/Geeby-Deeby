<?php
/**
 * Geeby-Deeby local code module
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2020.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category GeebyDeeby
 * @package  Modules
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeebyLocal;

use GeebyDeebyLocal\View\InjectTemplateListener;
use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\View\Http\InjectTemplateListener as LaminasInjectTemplateListener;
use Laminas\ServiceManager\Factory\InvokableFactory;

/**
 * Geeby-Deeby local code module
 *
 * @category GeebyDeeby
 * @package  Modules
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class Module
{
    /**
     * On bootstrap event
     *
     * @param MvcEvent $event Event object
     *
     * @return void
     */
    public function onBootstrap($event)
    {
        \EasyRdf\RdfNamespace::set('dime', 'https://dimenovels.org/ontology#');
        \EasyRdf\RdfNamespace::set('rda', 'http://rdaregistry.info/Elements/u/');
    }

    /**
     * Get module configuration.
     *
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * Return service configuration.
     *
     * @return array
     */
    public function getServiceConfig()
    {
        return [
            'aliases' => [
                LaminasInjectTemplateListener::class =>
                    InjectTemplateListener::class,
            ],
            'factories' => [
                InjectTemplateListener::class => InvokableFactory::class,
            ],
        ];
    }

    /**
     * Get autoloader configuration.
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return [
            'Laminas\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }
}
