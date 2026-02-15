<?php

/**
 * Database adapter factory.
 *
 * PHP version 8
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
 * along with this program; if not, see
 * <https://www.gnu.org/licenses/>.
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Db;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Proxy\ProxyFactory;
use Psr\Container\ContainerInterface;

/**
 * Database adapter factory.
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EntityManagerFactory implements \Laminas\ServiceManager\Factory\FactoryInterface
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
        $paths = [__DIR__ . '/Entity'];
        $isDevMode = false;
        $config = $container->get('Config');
        $dbParams = [
                'driver' => 'pdo_mysql',
                'charset' => 'utf8mb4',
                'host' => $config['geeby-deeby']['dbHost'],
                'user' => $config['geeby-deeby']['dbUser'],
                'password' => $config['geeby-deeby']['dbPass'],
                'dbname' => $config['geeby-deeby']['dbName'],
        ];
        $doctrineConfig = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);
        $doctrineConfig->setProxyDir(realpath(__DIR__ . '/../../../../../data/cache'));
        // TODO: this is only appropriate as a development setting:
        $doctrineConfig->setAutoGenerateProxyClasses(ProxyFactory::AUTOGENERATE_ALWAYS);
        $connection = DriverManager::getConnection($dbParams, $doctrineConfig);
        return new EntityManager($connection, $doctrineConfig);
    }
}
