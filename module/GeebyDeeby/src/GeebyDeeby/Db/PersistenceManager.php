<?php

/**
 * Class to manage database persistence operations.
 *
 * PHP version 8
 *
 * Copyright (C) Demian Katz 2025.
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
 * @package  Db
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org Main Site
 */

namespace GeebyDeeby\Db;

use GeebyDeeby\Db\Entity\EntityInterface;
use Laminas\Db\RowGateway\AbstractRowGateway;

/**
 * Class to manage database persistence operations.
 *
 * @category GeebyDeeby
 * @package  Db
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org Main Site
 */
class PersistenceManager
{
    /**
     * Persist an entity.
     *
     * @param EntityInterface $entity Entity to persist.
     *
     * @return void
     */
    public function persistEntity(EntityInterface $entity): void
    {
        if (!$entity instanceof AbstractRowGateway) {
            throw new \Exception('Unexpected entity type');
        }
        $entity->save();
    }

    /**
     * Delete an entity.
     *
     * @param EntityInterface $entity Entity to persist.
     *
     * @return void
     */
    public function deleteEntity(EntityInterface $entity): void
    {
        if (!$entity instanceof AbstractRowGateway) {
            throw new \Exception('Unexpected entity type');
        }
        $entity->delete();
    }
}
