<?php

/**
 * Database service for the People table.
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

use GeebyDeeby\Db\Entity\PersonEntityInterface;
use GeebyDeeby\Db\Table\Person;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the People table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PersonService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param Person $personTable Person table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected Person $personTable
    ) {
    }

    /**
     * Create an empty entity
     *
     * @return PersonEntityInterface
     */
    public function createEntity(): PersonEntityInterface
    {
        return $this->personTable->createRow();
    }

    /**
     * Retrieve an entity using its primary key.
     *
     * @param int $id Primary key value
     *
     * @return PersonEntityInterface
     */
    public function getByPrimaryKey(int $id): PersonEntityInterface
    {
        return $this->personTable->getByPrimaryKey($id);
    }

    /**
     * Validate a populated entity -- return error message if problem found, null otherwise.
     *
     * @param PersonEntityInterface $entity Entity to validate
     *
     * @return ?string
     */
    public function getValidationError(PersonEntityInterface $entity): ?string
    {
        $name = $entity->getLastName();
        return empty($name) ? 'Last name cannot be blank.' : null;
    }

    /**
     * Get a list of authorities.
     *
     * @return array
     */
    public function getList(): array
    {
        return iterator_to_array($this->personTable->getList());
    }
}
