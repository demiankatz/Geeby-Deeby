<?php

/**
 * Database service for the Material_Types table.
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

use GeebyDeeby\Db\Entity\MaterialTypeEntityInterface;
use GeebyDeeby\Db\Table\MaterialType;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Material_Types table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class MaterialTypeService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param MaterialType $materialTypeTable Material type table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected MaterialType $materialTypeTable
    ) {
    }

    /**
     * Create an empty entity
     *
     * @return MaterialTypeEntityInterface
     */
    public function createEntity(): MaterialTypeEntityInterface
    {
        return $this->materialTypeTable->createRow();
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?MaterialTypeEntityInterface
     */
    public function getByPrimaryKey(int $id): ?MaterialTypeEntityInterface
    {
        return $this->materialTypeTable->getByPrimaryKey($id);
    }

    /**
     * Validate a populated entity -- return error message if problem found, null otherwise.
     *
     * @param MaterialTypeEntityInterface $entity Entity to validate
     *
     * @return ?string
     */
    public function getValidationError(MaterialTypeEntityInterface $entity): ?string
    {
        $name = $entity->getSingularName();
        return empty($name) ? 'Material type name cannot be blank.' : null;
    }

    /**
     * Get a list of authorities.
     *
     * @return MaterialTypeEntityInterface[]
     */
    public function getList(): array
    {
        return iterator_to_array($this->materialTypeTable->getList());
    }

    /**
     * Set a material type as the default.
     *
     * @param MaterialTypeEntityInterface $default New default material type
     *
     * @return void
     */
    public function setDefaultMaterialType(MaterialTypeEntityInterface $default): void
    {
        // First clear existing default:
        $this->materialTypeTable->update(['Default' => 0]);

        // Now set new default:
        $this->persistEntity($default->setIsDefault(true));
    }
}
