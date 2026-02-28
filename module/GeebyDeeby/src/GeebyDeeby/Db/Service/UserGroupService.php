<?php

/**
 * Database service for the User_Groups table.
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

namespace GeebyDeeby\Db\Service;

use GeebyDeeby\Db\Entity\UserGroup;
use GeebyDeeby\Db\Entity\UserGroupEntityInterface;

/**
 * Database service for the User_Groups table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class UserGroupService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return UserGroupEntityInterface
     */
    public function createEntity(): UserGroupEntityInterface
    {
        return new UserGroup();
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?UserGroupEntityInterface
     */
    public function getByPrimaryKey(int $id): ?UserGroupEntityInterface
    {
        return $this->entityManager->find(UserGroup::class, $id);
    }

    /**
     * Validate a populated entity -- return error message if problem found, null otherwise.
     *
     * @param UserGroupEntityInterface $entity Entity to validate
     *
     * @return ?string
     */
    public function getValidationError(UserGroupEntityInterface $entity): ?string
    {
        $name = $entity->getGroupName();
        return empty($name) ? 'Name cannot be blank.' : null;
    }

    /**
     * Get a list of authorities.
     *
     * @return UserGroupEntityInterface[]
     */
    public function getList(): array
    {
        $dql = 'SELECT g FROM ' . UserGroup::class . ' g ORDER BY g.groupName';
        $query = $this->entityManager->createQuery($dql);
        return $query->getResult();
    }
}
