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

use GeebyDeeby\Db\Entity\Item;
use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\ItemsInCollection;
use GeebyDeeby\Db\Entity\ItemsInCollectionEntityInterface;
use GeebyDeeby\Db\Entity\MaterialType;
use GeebyDeeby\Db\Entity\Note;

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
     * Create an empty entity.
     *
     * @return ItemsInCollectionEntityInterface
     */
    public function createEntity(): ItemsInCollectionEntityInterface
    {
        $entity = new ItemsInCollection();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get a list of all collections.
     *
     * @return array
     */
    public function getAllCollections(): array
    {
        $dql = 'SELECT i.id AS Item_ID, i.itemName AS Item_Name '
            . 'FROM ' . ItemsInCollection::class . ' c '
            . 'INNER JOIN ' . Item::class . ' i ON c.collectionItem=i.id '
            . 'GROUP BY i.id, i.itemName ORDER BY i.itemName';
        $query = $this->entityManager->createQuery($dql);
        return $query->getResult();
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
        $dql = 'SELECT i.id AS Item_ID, i.itemName AS Item_Name, '
            . 'm.id AS Material_Type_ID, m.singularName AS Material_Type_Name, n.id AS Note_ID, n.note AS Note '
            . 'FROM ' . ItemsInCollection::class . ' c '
            . 'INNER JOIN ' . Item::class . ' i ON c.collectionItem=i.id '
            . 'INNER JOIN ' . MaterialType::class . ' m ON i.materialType=m.id '
            . 'LEFT JOIN ' . Note::class . ' n ON c.note=n.id '
            . 'WHERE c.item=:item ORDER BY m.singularName, i.itemName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('item', $itemID);
        return $query->getResult();
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
        $dql = 'SELECT i.id AS Item_ID, i.itemName AS Item_Name, c.position AS Position, '
            . 'm.id AS Material_Type_ID, m.singularName AS Material_Type_Name, n.id AS Note_ID, n.note AS Note '
            . 'FROM ' . ItemsInCollection::class . ' c '
            . 'INNER JOIN ' . Item::class . ' i ON c.item=i.id '
            . 'INNER JOIN ' . MaterialType::class . ' m ON i.materialType=m.id '
            . 'LEFT JOIN ' . Note::class . ' n ON c.note=n.id '
            . 'WHERE c.collectionItem=:collectionItem ORDER BY m.singularName, c.position, i.itemName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('collectionItem', $collectionID);
        return $query->getResult();
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
        $dql = 'DELETE FROM ' . ItemsInCollection::class . ' c WHERE c.collectionItem=:collectionItem';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('collectionItem', $collectionId);
        $query->execute();
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
        $dql = 'SELECT c FROM ' . ItemsInCollection::class
            . ' c WHERE c.collectionItem=:collectionItem AND c.item=:item';
        $params = [
            'collectionItem' => $collection instanceof ItemEntityInterface ? $collection->getId() : $collection,
            'item' => $item instanceof ItemEntityInterface ? $item->getId() : $item,
        ];
        if ($pos !== null) {
            $dql .= ' AND c.position=:position';
            $params['position'] = $pos;
        }
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
