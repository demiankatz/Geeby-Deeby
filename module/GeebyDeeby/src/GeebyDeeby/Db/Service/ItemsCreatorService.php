<?php

/**
 * Database service for the Items_Creators table.
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

use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\ItemsCreatorEntityInterface;
use GeebyDeeby\Db\Entity\PersonEntityInterface;
use GeebyDeeby\Db\Entity\RoleEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\ItemsCreators;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Items_Creators table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsCreatorService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param PersistenceManager $persistenceManager Persistence manager
     * @param ItemsCreators      $itemsCreatorsTable ItemsCreators table
     */
    public function __construct(
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected ItemsCreators $itemsCreatorsTable
    ) {
        parent::__construct($persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return ItemsCreatorEntityInterface
     */
    public function createEntity(): ItemsCreatorEntityInterface
    {
        return $this->itemsCreatorsTable->createRow();
    }

    /**
     * Retrieve an entity by its primary key.
     *
     * @param int $id Primary key
     *
     * @return ?ItemsCreatorEntityInterface
     */
    public function getByPrimaryKey(int $id): ?ItemsCreatorEntityInterface
    {
        return $this->itemsCreatorsTable->getByPrimaryKey($id) ?: null;
    }

    /**
     * Get a list of credits attached to the specified person.
     *
     * @param int    $personID Person ID
     * @param string $sort     Type of sorting (series/title/year)
     *
     * @return array
     */
    public function getCitationsForPerson(int $personID, string $sort = 'series'): array
    {
        // Special case: bringing series into the mix makes things more complex:
        if ($sort == 'series') {
            return $this->getSeriesCitationsForPerson($personID);
        }
        return $this->getItemCitationsForPerson($personID, $sort);
    }

    /**
     * Get a list of credits attached to the specified person, sorted by
     * item.
     *
     * @param int    $personID Person ID
     * @param string $sort     Type of sorting (title or year)
     *
     * @return array
     */
    public function getItemCitationsForPerson(int $personID, string $sort = 'title'): array
    {
        return iterator_to_array($this->itemsCreatorsTable->getItemCitationsForPerson($personID, $sort));
    }

    /**
     * Get a list of credits attached to the specified person, sorted by
     * series.
     *
     * @param int $personID Person ID
     *
     * @return array
     */
    public function getSeriesCitationsForPerson(int $personID): array
    {
        return iterator_to_array($this->itemsCreatorsTable->getSeriesCitationsForPerson($personID));
    }

    /**
     * Given an item identifier, return a list of creators.
     *
     * @param int $itemID Item ID
     *
     * @return array
     */
    public function getCreatorsForItem(int $itemID): array
    {
        return iterator_to_array($this->itemsCreatorsTable->getCreatorsForItem($itemID));
    }

    /**
     * Get an entity by item, person and role.
     *
     * @param int|ItemEntityInterface   $item   Item entity or ID
     * @param int|PersonEntityInterface $person Person entity or ID
     * @param int|RoleEntityInterface   $role   Role entity or ID
     *
     * @return ?ItemsCreatorEntityInterface
     */
    public function getByItemAndPersonAndRole(
        int|ItemEntityInterface $item,
        int|PersonEntityInterface $person,
        int|RoleEntityInterface $role
    ): ?ItemsCreatorEntityInterface {
        $where = [
            'Item_ID' => $item instanceof ItemEntityInterface ? $item->getId() : $item,
            'Person_ID' => $person instanceof PersonEntityInterface ? $person->getId() : $person,
            'Role_ID' => $role instanceof RoleEntityInterface ? $role->getId() : $role,
        ];
        foreach ($this->itemsCreatorsTable->select($where) as $row) {
            return $row;
        }
        return null;
    }
}
