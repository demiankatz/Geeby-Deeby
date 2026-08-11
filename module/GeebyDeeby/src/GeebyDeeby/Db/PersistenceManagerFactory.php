<?php

/**
 * Persistence manager factory.
 *
 * PHP version 8
 *
 * Copyright (C) Demian Katz 2026.
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
 * along with this program; if not, see
 * <https://www.gnu.org/licenses/>.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Db;

use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;

/**
 * Persistence manager factory.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PersistenceManagerFactory implements \Laminas\ServiceManager\Factory\FactoryInterface
{
    /**
     * Create service
     *
     * @param ContainerInterface $container Service manager
     * @param string             $name      Requested service name
     * @param ?array             $options   Extra options
     *
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(
        ContainerInterface $container,
        $name,
        ?array $options = null
    ) {
        $config = $container->get('Config');
        if (!empty($config['geeby-deeby']['activity_log_dir'])) {
            $user = $container->get('GeebyDeeby\Authentication')->getIdentity();
            $logDir = $config['geeby-deeby']['activity_log_dir'];
        } else {
            $user = $logDir = null;
        }
        return new $name($container->get(EntityManager::class), $user, $logDir);
    }
}
