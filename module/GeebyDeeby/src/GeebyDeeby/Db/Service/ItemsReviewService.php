<?php

/**
 * Database service for the Items_Reviews table.
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
use GeebyDeeby\Db\Entity\ItemsReviewEntityInterface;
use GeebyDeeby\Db\Entity\UserEntityInterface;
use GeebyDeeby\Db\Table\ItemsReviews;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Items_Reviews table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsReviewService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param ItemsReviews $itemsReviewsTable ItemsReviews table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected ItemsReviews $itemsReviewsTable
    ) {
    }

    /**
     * Create an empty entity.
     *
     * @return ItemsReviewEntityInterface
     */
    public function createEntity(): ItemsReviewEntityInterface
    {
        return $this->itemsReviewsTable->createRow();
    }

    /**
     * Get a list of reviews for the specified item.
     *
     * @param int     $itemID   Item ID
     * @param ?string $approved 'y' to get only approved items, 'n' for only
     * unapproved items, null for all items
     *
     * @return array
     */
    public function getReviewsForItem(int $itemID, ?string $approved = 'y'): array
    {
        return iterator_to_array($this->itemsReviewsTable->getReviewsForItem($itemID, $approved));
    }

    /**
     * Get a list of item IDs reviewed by the specified user.
     *
     * @param int     $userID   User ID
     * @param ?string $approved 'y' to get only approved items, 'n' for only
     * unapproved items, null for all items
     *
     * @return array
     */
    public function getReviewIDsByUser(int $userID, ?string $approved = 'y'): array
    {
        return iterator_to_array($this->itemsReviewsTable->getReviewIDsByUser($userID, $approved));
    }

    /**
     * Get a list of items reviewed by the specified user.
     *
     * @param ?int    $userID   User ID (null for all users)
     * @param ?string $approved 'y' to get only approved items, 'n' for only
     * unapproved items, null for all items
     * @param bool    $series   Include series information in result set?
     *
     * @return array
     */
    public function getReviewsByUser(?int $userID, ?string $approved = 'y', bool $series = true): array
    {
        return iterator_to_array($this->itemsReviewsTable->getReviewsByUser($userID, $approved, $series));
    }

    /**
     * Get recent item reviews.
     *
     * @return array
     */
    public function getRecentItemReviews(): array
    {
        return iterator_to_array($this->itemsReviewsTable->getRecentItemReviews());
    }

    /**
     * Get a row matching the provided item and source.
     *
     * @param int|UserEntityInterface $user User ID or entity
     * @param int|ItemEntityInterface $item Item ID or entity
     *
     * @return ?ItemsReviewEntityInterface
     */
    public function getByUserAndItem(
        int|UserEntityInterface $user,
        int|ItemEntityInterface $item
    ): ?ItemsReviewEntityInterface {
        $where = [
            'User_ID' => $user instanceof UserEntityInterface ? $user->getId() : $user,
            'Item_ID' => $item instanceof ItemEntityInterface ? $item->getId() : $item,
        ];
        foreach ($this->itemsReviewsTable->select($where) as $row) {
            return $row;
        }
        return null;
    }
}
