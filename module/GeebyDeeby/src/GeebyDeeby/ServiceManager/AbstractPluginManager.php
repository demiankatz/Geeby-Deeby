<?php
/**
 * GeebyDeeby Plugin Manager
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2012.
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
 * @package  ServiceManager
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\ServiceManager;

use Zend\ServiceManager\AbstractPluginManager as Base;
use Zend\ServiceManager\Exception\RuntimeException as ServiceManagerRuntimeException;

/**
 * GeebyDeeby Plugin Manager
 *
 * @category GeebyDeeby
 * @package  ServiceManager
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
abstract class AbstractPluginManager extends Base
{
    /**
     * Validate the plugin
     *
     * Checks that the filter loaded is either a valid callback or an instance
     * of FilterInterface.
     *
     * @param mixed $plugin Plugin to validate
     *
     * @throws ServiceManagerRuntimeException if invalid
     * @return void
     */
    public function validatePlugin($plugin)
    {
        $expectedInterface = $this->getExpectedInterface();
        if (!($plugin instanceof $expectedInterface)) {
            throw new ServiceManagerRuntimeException(
                'Plugin ' . get_class($plugin) . ' does not belong to '
                . $expectedInterface
            );
        }
    }

    /**
     * Return the name of the base class or interface that plug-ins must conform
     * to.
     *
     * @return string
     */
    abstract protected function getExpectedInterface();
}
