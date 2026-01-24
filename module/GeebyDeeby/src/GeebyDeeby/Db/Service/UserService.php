<?php

/**
 * Database service for the Users table.
 *
 * PHP version 8
 *
 * Copyright (C) Villanova University 2026.
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

use GeebyDeeby\Db\Entity\UserEntityInterface;
use GeebyDeeby\Db\Table\User;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Users table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class UserService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param User $userTable User table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected User $userTable
    ) {
    }

    /**
     * Create an empty entity
     *
     * @return UserEntityInterface
     */
    public function createEntity(): UserEntityInterface
    {
        return $this->userTable->createRow();
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?UserEntityInterface
     */
    public function getByPrimaryKey(int $id): ?UserEntityInterface
    {
        return $this->userTable->getByPrimaryKey($id);
    }

    /**
     * Retrieve a user entity by username.
     *
     * @param string $username Username
     *
     * @return ?UserEntityInterface
     */
    public function getByUsername(string $username): ?UserEntityInterface
    {
        foreach ($this->userTable->select(['Username' => $username]) as $user) {
            return $user;
        }
        return null;
    }

    /**
     * Validate a populated entity -- return error message if problem found, null otherwise.
     *
     * @param UserEntityInterface $entity Entity to validate
     *
     * @return ?string
     */
    public function getValidationError(UserEntityInterface $entity): ?string
    {
        $name = $entity->getUsername();
        return empty($name) ? 'Username cannot be blank.' : null;
    }

    /**
     * Get a list of users.
     *
     * @param ?bool $approvedFilter Limit to a specific approval status? (Null for no filter)
     *
     * @return UserEntityInterface[]
     */
    public function getList(?bool $approvedFilter = null): array
    {
        return iterator_to_array($this->userTable->getList($approvedFilter));
    }

    /**
     * Attempt to log in using the specified username and password.  On successful
     * login, the specified user's row will be returned.
     *
     * @param string $username Username for login.
     * @param string $password Password for login.
     *
     * @return ?UserEntityInterface User on successful login, null otherwise.
     */
    public function passwordLogin(string $username, string $password): ?UserEntityInterface
    {
        return $this->userTable->passwordLogin($username, $password);
    }
}
