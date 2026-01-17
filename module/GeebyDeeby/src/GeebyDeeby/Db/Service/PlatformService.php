<?php

/**
 * Database service for the Platforms table.
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

use GeebyDeeby\Db\Entity\PlatformEntityInterface;
use GeebyDeeby\Db\Table\Platform;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Platforms table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PlatformService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param Platform $platformTable Platform table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected Platform $platformTable
    ) {
    }

    /**
     * Create an empty entity
     *
     * @return PlatformEntityInterface
     */
    public function createEntity(): PlatformEntityInterface
    {
        return $this->platformTable->createRow();
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?PlatformEntityInterface
     */
    public function getByPrimaryKey(int $id): ?PlatformEntityInterface
    {
        return $this->platformTable->getByPrimaryKey($id);
    }

    /**
     * Validate a populated entity -- return error message if problem found, null otherwise.
     *
     * @param PlatformEntityInterface $entity Entity to validate
     *
     * @return ?string
     */
    public function getValidationError(PlatformEntityInterface $entity): ?string
    {
        $name = $entity->getPlatformName();
        return empty($name) ? 'Platform cannot be blank.' : null;
    }

    /**
     * Get a list of file types.
     *
     * @return PlatformEntityInterface[]
     */
    public function getList(): array
    {
        return iterator_to_array($this->platformTable->getList());
    }
}
