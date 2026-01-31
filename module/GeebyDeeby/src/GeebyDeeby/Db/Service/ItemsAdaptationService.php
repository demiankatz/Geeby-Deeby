<?php

/**
 * Database service for the Items_Adaptations table.
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

use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\ItemsAdaptationEntityInterface;
use GeebyDeeby\Db\Row\Item;
use GeebyDeeby\Db\Table\ItemsAdaptations;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Items_Adaptations table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsAdaptationService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param ItemsAdaptations $itemsAdaptationsTable ItemsAdaptations table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected ItemsAdaptations $itemsAdaptationsTable
    ) {
    }

    /**
     * Create an empty entity.
     *
     * @return ItemsAdaptationEntityInterface
     */
    public function createEntity(): ItemsAdaptationEntityInterface
    {
        return $this->itemsAdaptationsTable->createRow();
    }

    /**
     * Get a list of items adapted from the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return array
     */
    public function getAdaptedFrom(int $itemID): array
    {
        return iterator_to_array($this->itemsAdaptationsTable->getAdaptedFrom($itemID));
    }

    /**
     * Get a list of items adapted into the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return array
     */
    public function getAdaptedInto(int $itemID): array
    {
        return iterator_to_array($this->itemsAdaptationsTable->getAdaptedInto($itemID));
    }

    /**
     * Get a row matching the provided source item/adapted item pair.
     *
     * @param int|ItemEntityInterface $source  Source item ID or entity
     * @param int|ItemEntityInterface $adapted Adapted item ID or entity
     *
     * @return ?ItemsAdaptationEntityInterface
     */
    public function getBySourceItemAndAdaptedItem(
        int|ItemEntityInterface $source,
        int|ItemEntityInterface $adapted
    ): ?ItemsAdaptationEntityInterface {
        $where = [
            'Source_Item_ID' => $source instanceof ItemEntityInterface ? $source->getId() : $source,
            'Adapted_Item_ID' => $adapted instanceof ItemEntityInterface ? $adapted->getId() : $adapted,
        ];
        foreach ($this->itemsAdaptationsTable->select($where) as $row) {
            return $row;
        }
        return null;
    }
}
