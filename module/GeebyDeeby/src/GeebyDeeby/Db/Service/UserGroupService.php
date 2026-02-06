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

use GeebyDeeby\Db\Entity\UserGroupEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\UserGroup;
use GeebyDeeby\ServiceManager\Factory\Autowire;

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
     * Constructor
     *
     * @param PersistenceManager $persistenceManager Persistence manager
     * @param UserGroup          $userGroupTable     User group table
     */
    public function __construct(
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected UserGroup $userGroupTable
    ) {
        parent::__construct($persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return UserGroupEntityInterface
     */
    public function createEntity(): UserGroupEntityInterface
    {
        return $this->userGroupTable->createRow();
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
        return $this->userGroupTable->getByPrimaryKey($id);
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
        return iterator_to_array($this->userGroupTable->getList());
    }
}
