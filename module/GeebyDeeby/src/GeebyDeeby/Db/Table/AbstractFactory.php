<?php
/**
 * Abstract table factory
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2019.
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
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\Db\Table;

use Interop\Container\ContainerInterface;

/**
 * Abstract table factory
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class AbstractFactory
    implements \Zend\ServiceManager\Factory\AbstractFactoryInterface
{
    /**
     * Return row prototype object (null if unavailable)
     *
     * @param object $container     Service manager
     * @param string $requestedName Service being created
     *
     * @return object
     */
    protected function getRowPrototype($container, $requestedName)
    {
        $rowManager = $container->get('GeebyDeeby\Db\Row\PluginManager');
        // Map Table class to matching Row class.
        $name = str_replace("\\Table\\", "\\Row\\", $requestedName);
        return $rowManager->has($name) ? $rowManager->get($name) : null;
    }

    /**
     * Does the factory have a way to create an instance for the service?
     *
     * @param ContainerInterface $container     Service container
     * @param string             $requestedName Name of service
     *
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return class_exists($requestedName);
    }

    /**
     * Create a service for the specified name.
     *
     * @param ContainerInterface $container     Service container
     * @param string             $requestedName Name of service
     * @param array              $options       Options (unused)
     *
     * @return object
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName,
        array $options = null
    ) {
        $adapter = $container->get('Zend\Db\Adapter\Adapter');
        $tm = $container->get('GeebyDeeby\Db\Table\PluginManager');
        $rowPrototype = $this->getRowPrototype($container, $requestedName);
        return new $requestedName($adapter, $tm, $rowPrototype);
    }
}
