<?php

/**
 * EntityManager injector listener.
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
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */

namespace GeebyDeeby\Db\Entity\Feature;

use Doctrine\ORM\Event\PostLoadEventArgs;
use Exception;
use GeebyDeeby\Db\Entity\AbstractEntity;

use function is_callable;

/**
 * EntityManager injector listener.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
class EntityManagerInjector
{
    /**
     * Post-load callback to inject entity manager.
     *
     * @param AbstractEntity    $entity Entity to populate
     * @param PostLoadEventArgs $args   Event arguments
     *
     * @return void
     */
    public function postLoad(AbstractEntity $entity, PostLoadEventArgs $args): void
    {
        if (!is_callable([$entity, 'setEntityManager'])) {
            throw new Exception($entity::class . ' lacks setEntityManager method.');
        }
        $entity->setEntityManager($args->getObjectManager());
    }
}
