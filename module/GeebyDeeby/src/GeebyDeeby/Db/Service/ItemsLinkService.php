<?php

/**
 * Database service for the Items_Links table.
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
use GeebyDeeby\Db\Entity\ItemsLinkEntityInterface;
use GeebyDeeby\Db\Entity\LinkEntityInterface;
use GeebyDeeby\Db\Table\ItemsLinks;
use GeebyDeeby\ServiceManager\Factory\Autowire;

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
     * Constructor
     *
     * @param ItemsLinks $itemsLinksTable ItemsLinks table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected ItemsLinks $itemsLinksTable
    ) {
    }

    /**
     * Create an empty entity.
     *
     * @return ItemsLinkEntityInterface
     */
    public function createEntity(): ItemsLinkEntityInterface
    {
        return $this->itemsLinksTable->createRow();
    }

    /**
     * Get a list of items for the specified link.
     *
     * @param int $linkID Link ID
     *
     * @return array
     */
    public function getItemsForLink(int $linkID): array
    {
        return iterator_to_array($this->itemsLinksTable->getItemsForLink($linkID));
    }

    /**
     * Get a list of links for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return array
     */
    public function getLinksForItem(int $itemID): array
    {
        return iterator_to_array($this->itemsLinksTable->getLinksForItem($itemID));
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
        $where = [
            'Link_ID' => $link instanceof LinkEntityInterface ? $link->getId() : $link,
            'Item_ID' => $item instanceof ItemEntityInterface ? $item->getId() : $item,
        ];
        foreach ($this->itemsLinksTable->select($where) as $row) {
            return $row;
        }
        return null;
    }
}
