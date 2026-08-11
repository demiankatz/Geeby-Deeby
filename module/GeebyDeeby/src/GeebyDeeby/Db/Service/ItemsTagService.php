<?php

/**
 * Database service for the Items_Tags table.
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

use GeebyDeeby\Db\Entity\Edition;
use GeebyDeeby\Db\Entity\Item;
use GeebyDeeby\Db\Entity\ItemsAltTitle;
use GeebyDeeby\Db\Entity\ItemsTag;
use GeebyDeeby\Db\Entity\ItemsTagEntityInterface;
use GeebyDeeby\Db\Entity\Series;
use GeebyDeeby\Db\Entity\Tag;
use GeebyDeeby\Db\Entity\TagEntityInterface;

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
     * Create an empty entity.
     *
     * @return ItemsTagEntityInterface
     */
    public function createEntity(): ItemsTagEntityInterface
    {
        $entity = new ItemsTag();
        $entity->setEntityManager($this->entityManager);
        return $entity;
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
        if ($sort === 'series') {
            $extraJoin = 'INNER JOIN ' . Edition::class . ' e ON e.item=i.id '
                . 'LEFT JOIN ' . ItemsAltTitle::class . ' iat ON e.preferredItemAltName=iat.id '
                . 'INNER JOIN ' . Series::class . ' s ON e.series=s.id ';
            $extraSelect = ', s.id AS Series_ID, s.seriesName AS Series_Name, '
                . 'e.volume AS Volume, e.position AS Position, e.replacementNumber AS Replacement_Number, '
                . 'iat.altName AS Item_AltName';
            $order = 's.seriesName, s.id, e.volume, e.position, e.replacementNumber, i.itemName';
            $group = ' GROUP BY i.id, e.volume, e.position, e.replacementNumber';
        } else {
            $order = 'i.itemName';
            $extraJoin = $extraSelect = $group = '';
        }
        $dql = 'SELECT i.id AS Item_ID, i.itemName AS Item_Name' . $extraSelect
            . ' FROM ' . ItemsTag::class . ' it '
            . 'INNER JOIN ' . Item::class . ' i ON it.item=i.id '
            . $extraJoin
            . 'WHERE it.tag = :tag' . $group . ' ORDER BY ' . $order;
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('tag', $tagID);
        return $query->getResult();
    }

    /**
     * Get a list of tags for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return TagEntityInterface[]
     */
    public function getTagsForItem(int $itemID): array
    {
        $dql = 'SELECT t FROM ' . ItemsTag::class . ' it '
            . 'INNER JOIN ' . Tag::class . '  t ON it.tag=t.id WHERE it.item = :item ORDER BY t.tag';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('item', $itemID);
        return $query->getResult();
    }

    /**
     * Retrieve an existing entry using an item ID and tag ID (null if not found).
     *
     * @param int $itemId Item ID
     * @param int $tagId  Tag ID
     *
     * @return ?ItemsTagEntityInterface
     */
    public function getByItemAndTag(int $itemId, int $tagId): ?ItemsTagEntityInterface
    {
        $dql = 'SELECT it FROM ' . ItemsTag::class . ' it WHERE it.item = :item AND it.tag = :tag';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters(['item' => $itemId, 'tag' => $tagId]);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
