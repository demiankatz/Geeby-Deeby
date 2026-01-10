<?php

/**
 * Database service for the Cities table.
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

use GeebyDeeby\Db\Entity\CityEntityInterface;
use GeebyDeeby\Db\Table\City;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Cities table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class CityService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param City $cityTable City table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected City $cityTable
    ) {
    }

    /**
     * Create an empty entity
     *
     * @return CityEntityInterface
     */
    public function createEntity(): CityEntityInterface
    {
        return $this->cityTable->createRow();
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?CityEntityInterface
     */
    public function getByPrimaryKey(int $id): ?CityEntityInterface
    {
        return $this->cityTable->getByPrimaryKey($id);
    }

    /**
     * Validate a populated entity -- return error message if problem found, null otherwise.
     *
     * @param CityEntityInterface $entity Entity to validate
     *
     * @return ?string
     */
    public function getValidationError(CityEntityInterface $entity): ?string
    {
        $name = $entity->getCityName();
        return empty($name) ? 'City name cannot be blank.' : null;
    }

    /**
     * Get a list of cities.
     *
     * @return CityEntityInterface[]
     */
    public function getList(): array
    {
        return iterator_to_array($this->cityTable->getList());
    }
}
