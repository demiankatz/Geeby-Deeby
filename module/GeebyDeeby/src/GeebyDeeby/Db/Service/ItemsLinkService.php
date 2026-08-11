<?php

/**
 * Database service for the Items_Links table.
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
use GeebyDeeby\Db\Entity\ItemsLink;
use GeebyDeeby\Db\Entity\ItemsLinkEntityInterface;
use GeebyDeeby\Db\Entity\Link;
use GeebyDeeby\Db\Entity\LinkEntityInterface;

/**
 * Database service for the Items_Links table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsLinkService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return ItemsLinkEntityInterface
     */
    public function createEntity(): ItemsLinkEntityInterface
    {
        $entity = new ItemsLink();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get a list of items for the specified link.
     *
     * @param int $linkID Link ID
     *
     * @return ItemEntityInterface[]
     */
    public function getItemsForLink(int $linkID): array
    {
        $dql = 'SELECT i FROM ' . ItemsLink::class . ' il '
            . 'INNER JOIN ' . Item::class . ' i ON il.item=i.id '
            . 'WHERE il.link = :link ORDER BY i.itemName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('link', $linkID);
        return $query->getResult();
    }

    /**
     * Get a list of links for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return LinkEntityInterface[]
     */
    public function getLinksForItem(int $itemID): array
    {
        $dql = 'SELECT l FROM ' . ItemsLink::class . ' il '
            . 'INNER JOIN ' . Link::class . ' l ON il.link=l.id '
            . 'WHERE il.item = :item ORDER BY l.linkName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('item', $itemID);
        return $query->getResult();
    }

    /**
     * Retrieve the record for the specified link and item.
     *
     * @param int|LinkEntityInterface $link Link ID or entity
     * @param int|ItemEntityInterface $item Item ID or entity
     *
     * @return ?ItemsLinkEntityInterface
     */
    public function getForLinkAndItem(
        int|LinkEntityInterface $link,
        int|ItemEntityInterface $item
    ): ?ItemsLinkEntityInterface {
        $params = [
            'link' => $link instanceof LinkEntityInterface ? $link->getId() : $link,
            'item' => $item instanceof ItemEntityInterface ? $item->getId() : $item,
        ];
        $dql = 'SELECT il FROM ' . ItemsLink::class . ' il WHERE il.item = :item AND il.link = :link';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
