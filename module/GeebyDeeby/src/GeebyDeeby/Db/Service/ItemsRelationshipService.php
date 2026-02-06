<?php

/**
 * Database service for the Items_Relationships table.
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

use GeebyDeeby\Db\Entity\ItemsRelationshipEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\ItemsRelationship;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Items_Relationships table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsRelationshipService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param PersistenceManager $persistenceManager     Persistence manager
     * @param ItemsRelationship  $itemsRelationshipTable ItemsRelationship table
     */
    public function __construct(
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected ItemsRelationship $itemsRelationshipTable
    ) {
        parent::__construct($persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return ItemsRelationshipEntityInterface
     */
    public function createEntity(): ItemsRelationshipEntityInterface
    {
        return $this->itemsRelationshipTable->createRow();
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?ItemsRelationshipEntityInterface
     */
    public function getByPrimaryKey(int $id): ?ItemsRelationshipEntityInterface
    {
        return $this->itemsRelationshipTable->getByPrimaryKey($id);
    }

    /**
     * Validate a populated entity -- return error message if problem found, null otherwise.
     *
     * @param ItemsRelationshipEntityInterface $entity Entity to validate
     *
     * @return ?string
     */
    public function getValidationError(ItemsRelationshipEntityInterface $entity): ?string
    {
        $name = $entity->getRelationshipName();
        return empty($name) ? 'Name cannot be blank.' : null;
    }

    /**
     * Get a list of item relationships.
     *
     * @return ItemsRelationshipEntityInterface[]
     */
    public function getList(): array
    {
        return iterator_to_array($this->itemsRelationshipTable->getList());
    }

    /**
     * Get a list of relationships, formatted to populate a select control.
     *
     * @param bool $includePredicate Should we return labels only (false) or an
     * array with label and predicate (true)?
     *
     * @return array
     */
    public function getOptionList(bool $includePredicate = false): array
    {
        return $this->itemsRelationshipTable->getOptionList($includePredicate);
    }
}
