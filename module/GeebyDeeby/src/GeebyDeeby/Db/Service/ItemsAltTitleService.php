<?php

/**
 * Database service for the Items_AltTitles table.
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

use GeebyDeeby\Db\Entity\ItemsAltTitleEntityInterface;
use GeebyDeeby\Db\Table\ItemsAltTitles;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Items_AltTitles table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsAltTitleService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param ItemsAltTitles $itemsAltTitlesTable ItemsAltTitles table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected ItemsAltTitles $itemsAltTitlesTable
    ) {
    }

    /**
     * Create an empty entity
     *
     * @return ItemsAltTitleEntityInterface
     */
    public function createEntity(): ItemsAltTitleEntityInterface
    {
        return $this->itemsAltTitlesTable->createRow();
    }

    /**
     * Get a list of alternate titles for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return array
     */
    public function getAltTitles(int $itemID): array
    {
        return iterator_to_array($this->itemsAltTitlesTable->getAltTitles($itemID));
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?ItemsAltTitleEntityInterface
     */
    public function getByPrimaryKey(int $id): ?ItemsAltTitleEntityInterface
    {
        return $this->itemsAltTitlesTable->getByPrimaryKey($id);
    }

    /**
     * Perform a keyword search.
     *
     * @param array $tokens Keywords.
     *
     * @return array
     */
    public function keywordSearch(array $tokens): array
    {
        return iterator_to_array($this->itemsAltTitlesTable->keywordSearch($tokens));
    }

    /**
     * Retrieve an existing entry using an item ID and sequence ID (null if not found).
     *
     * @param int    $itemId Item ID
     * @param string $altId  Alt title sequence ID
     *
     * @return ?ItemsAltTitleEntityInterface
     */
    public function getByItemAndId(int $itemId, string $altId): ?ItemsAltTitleEntityInterface
    {
        foreach ($this->itemsAltTitlesTable->select(['Item_ID' => $itemId, 'Sequence_ID' => $altId]) as $row) {
            return $row;
        }
        return null;
    }

    /**
     * Retrieve an existing entry using an item ID and title (null if not found).
     *
     * @param int    $itemId Item ID
     * @param string $title  Alt title
     *
     * @return ?ItemsAltTitleEntityInterface
     */
    public function getByItemAndTitle(int $itemId, string $title): ?ItemsAltTitleEntityInterface
    {
        foreach ($this->itemsAltTitlesTable->select(['Item_ID' => $itemId, 'Item_AltName' => $title]) as $row) {
            return $row;
        }
        return null;
    }
}
