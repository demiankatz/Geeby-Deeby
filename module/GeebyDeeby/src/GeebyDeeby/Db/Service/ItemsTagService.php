<?php

/**
 * Database service for the Items_Tags table.
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

use GeebyDeeby\Db\Entity\ItemsTagEntityInterface;
use GeebyDeeby\Db\Table\ItemsTags;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Items_Tags table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsTagService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param ItemsTags $itemsTagsTable ItemsTags table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected ItemsTags $itemsTagsTable
    ) {
    }

    /**
     * Create an empty entity
     *
     * @return ItemsTagEntityInterface
     */
    public function createEntity(): ItemsTagEntityInterface
    {
        return $this->itemsTagsTable->createRow();
    }

    /**
     * Get items for the specified tag.
     *
     * @param int    $tagID Tag ID
     * @param string $sort  Sort type (title or series; default = title)
     *
     * @return array
     */
    public function getItemsForTag(int $tagID, string $sort = 'title'): array
    {
        return iterator_to_array($this->itemsTagsTable->getItemsForTag($tagID, $sort));
    }

    /**
     * Get a list of tags for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return array
     */
    public function getTagsForItem(int $itemID): array
    {
        return iterator_to_array($this->itemsTagsTable->getTags($itemID));
    }

    /**
     * Retrieve an existing entry using an item ID and tag ID (null if not found).
     *
     * @param int    $itemId Item ID
     * @param string $tagId  Tag ID
     *
     * @return ?ItemsTagEntityInterface
     */
    public function getByItemAndTag(int $itemId, string $tagId): ?ItemsTagEntityInterface
    {
        foreach ($this->itemsTagsTable->select(['Item_ID' => $itemId, 'Tag_ID' => $tagId]) as $row) {
            return $row;
        }
        return null;
    }
}
