<?php
/**
 * ScriptManager view helper factory.
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
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\View\Helper;

use Interop\Container\ContainerInterface;

/**
 * ScriptManager view helper factory.
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ScriptManagerFactory implements \Zend\ServiceManager\Factory\FactoryInterface
{
    /**
     * Create service
     *
     * @param ContainerInterface $container Service manager
     * @param string             $name      Requested service name
     * @param array              $options   Extra options
     *
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $name,
        array $options = null
    ) {
        $base = $container->get('ViewHelperManager')->get('basePath')->__invoke();
        return new $name(
            $base, $container->get('ViewHelperManager')->get('headScript')
        );
    }
}