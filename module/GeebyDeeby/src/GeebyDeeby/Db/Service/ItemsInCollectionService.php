<?php

/**
 * Database service for the Items_In_Collections table.
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
use GeebyDeeby\Db\Entity\ItemsInCollectionEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\ItemsInCollections;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Items_In_Collections table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsInCollectionService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param PersistenceManager $persistenceManager      Persistence manager
     * @param ItemsInCollections $itemsInCollectionsTable ItemsInCollections table
     */
    public function __construct(
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected ItemsInCollections $itemsInCollectionsTable
    ) {
        parent::__construct($persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return ItemsInCollectionEntityInterface
     */
    public function createEntity(): ItemsInCollectionEntityInterface
    {
        return $this->itemsInCollectionsTable->createRow();
    }

    /**
     * Get a list of all collections.
     *
     * @return array
     */
    public function getAllCollections(): array
    {
        return iterator_to_array($this->itemsInCollectionsTable->getAllCollections());
    }

    /**
     * Get a list of collections for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return array
     */
    public function getCollectionsForItem(int $itemID): array
    {
        return iterator_to_array($this->itemsInCollectionsTable->getCollectionsForItem($itemID));
    }

    /**
     * Get a list of items for the specified collection.
     *
     * @param int $collectionID Item ID
     *
     * @return array
     */
    public function getItemsForCollection(int $collectionID): array
    {
        return iterator_to_array($this->itemsInCollectionsTable->getItemsForCollection($collectionID));
    }

    /**
     * Delete all of the contents of the specified collection.
     *
     * @param int $collectionId ID of item representing collection
     *
     * @return void
     */
    public function deleteCollection(int $collectionId): void
    {
        $this->itemsInCollectionsTable->delete(['Collection_Item_ID' => $collectionId]);
    }

    /**
     * Get a row matching the provided source container/contained item pair.
     *
     * @param int|ItemEntityInterface $collection Container item ID or entity
     * @param int|ItemEntityInterface $item       Contained item ID or entity
     * @param ?int                    $pos        Position to match (null for first match of $collection + $item)
     *
     * @return ?ItemsInCollectionEntityInterface
     */
    public function getByCollectionItemAndItemAndPosition(
        int|ItemEntityInterface $collection,
        int|ItemEntityInterface $item,
        ?int $pos = null
    ): ?ItemsInCollectionEntityInterface {
        $where = [
            'Collection_Item_ID' => $collection instanceof ItemEntityInterface ? $collection->getId() : $collection,
            'Item_ID' => $item instanceof ItemEntityInterface ? $item->getId() : $item,
        ];
        if ($pos !== null) {
            $where['Position'] = $pos;
        }
        foreach ($this->itemsInCollectionsTable->select($where) as $row) {
            return $row;
        }
        return null;
    }
}
