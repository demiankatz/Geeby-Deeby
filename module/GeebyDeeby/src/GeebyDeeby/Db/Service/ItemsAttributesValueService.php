<?php

/**
 * Database service for the Items_Attributes_Values table.
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
use GeebyDeeby\Db\Entity\ItemsAttributesValueEntityInterface;
use GeebyDeeby\Db\Table\ItemsAttributesValues;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Items_Attributes_Values table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsAttributesValueService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param ItemsAttributesValues $valuesTable ItemsAttribute table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected ItemsAttributesValues $valuesTable
    ) {
    }

    /**
     * Create an empty entity.
     *
     * @return ItemsAttributesValueEntityInterface
     */
    public function createEntity(): ItemsAttributesValueEntityInterface
    {
        return $this->valuesTable->createRow();
    }

    /**
     * Get a list of attributes for the specified item.
     *
     * @param int|int[] $itemID Item ID (or array of IDs)
     *
     * @return array
     */
    public function getAttributesForItem(int|array $itemID): array
    {
        return iterator_to_array($this->valuesTable->getAttributesForItem($itemID));
    }

    /**
     * Delete existing attributes associated with the provided item.
     *
     * @param int|ItemEntityInterface $item Item entity or ID
     *
     * @return void
     */
    public function deleteByItem(int|ItemEntityInterface $item): void
    {
        $where = ['Item_ID' => $item instanceof ItemEntityInterface ? $item->getId() : $item];
        $this->valuesTable->delete($where);
    }
}
