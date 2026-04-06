<?php

/**
 * Database service for the Items_Reviews table.
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
use GeebyDeeby\Db\Entity\Enum\Approved;
use GeebyDeeby\Db\Entity\Item;
use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\ItemsAltTitle;
use GeebyDeeby\Db\Entity\ItemsReview;
use GeebyDeeby\Db\Entity\ItemsReviewEntityInterface;
use GeebyDeeby\Db\Entity\Series;
use GeebyDeeby\Db\Entity\User;
use GeebyDeeby\Db\Entity\UserEntityInterface;

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
     * Create an empty entity.
     *
     * @return ItemsReviewEntityInterface
     */
    public function createEntity(): ItemsReviewEntityInterface
    {
        $entity = new ItemsReview();
        $entity->setEntityManager($this->entityManager);
        return $entity;
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
        $params = ['item' => $itemID];
        $dql = 'SELECT r.review AS Review, r.added as Added, u.id AS User_ID, u.username AS Username, u.name AS Name '
            . 'FROM ' . ItemsReview::class . ' r '
            . 'INNER JOIN ' . User::class . ' u ON r.user=u.id '
            . 'WHERE r.item=:item';
        if ($approved) {
            $dql .= ' AND r.approved=:approved';
            $params['approved'] = Approved::from($approved);
        }
        $dql .= ' ORDER BY u.username';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        return $query->getResult();
    }

    /**
     * Get a list of item IDs reviewed by the specified user.
     *
     * @param int     $userID   User ID
     * @param ?string $approved 'y' to get only approved items, 'n' for only
     * unapproved items, null for all items
     *
     * @return ItemsReviewEntityInterface[]
     */
    public function getReviewIDsByUser(int $userID, ?string $approved = 'y'): array
    {
        $params = ['user' => $userID];
        $dql = 'SELECT r FROM ' . ItemsReview::class . ' r WHERE r.user=:user';
        if ($approved) {
            $dql .= ' AND r.approved=:approved';
            $params['approved'] = Approved::from($approved);
        }
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        return $query->getResult();
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
        $params = $where = [];
        $extraSelect = $extraJoin = $groupBy = '';
        $order = 'i.itemName';
        if ($approved) {
            $where[] = 'r.approved=:approved';
            $params['approved'] = Approved::from($approved);
        }
        if ($userID) {
            $where[] = 'r.user=:user';
            $params['user'] = $userID;
        }
        if ($series) {
            $groupBy = 'GROUP BY i.id, r.user, s.id, Volume, Position, Replacement_Number';
            // Add more sort settings when series are included:
            $order = 's.seriesName, s.id, Volume, Position, Replacement_Number, ' . $order;
            $extraSelect .= 'iat.altName AS Item_AltName, COALESCE(e.volume, parent.volume) AS Volume, '
                . 'COALESCE(e.position, parent.position) AS Position, '
                . 'COALESCE(e.replacementNumber, parent.replacementNumber) AS Replacement_Number, '
                . 's.id AS Series_ID, s.seriesName AS Series_Name, ';
            $extraJoin .= 'INNER JOIN ' . Edition::class . ' e ON e.item=i.id '
                . 'INNER JOIN ' . Series::class . ' s ON e.series=s.id '
                . 'LEFT JOIN ' . ItemsAltTitle::class . ' iat ON e.preferredItemAltName=iat.id '
                . 'LEFT JOIN ' . Edition::class . ' parent ON e.parentEdition=parent.id ';
        }
        $dql = 'SELECT r.review AS Review, r.added as Added, ' . $extraSelect
            . 'u.id AS User_ID, u.username AS Username, u.name AS Name, '
            . 'i.id AS Item_ID, i.itemName AS Item_Name '
            . 'FROM ' . ItemsReview::class . ' r '
            . 'INNER JOIN ' . Item::class . ' i ON r.item=i.id '
            . 'INNER JOIN ' . User::class . ' u ON r.user=u.id '
            . $extraJoin
            . ($where ? 'WHERE ' . implode(' AND ', $where) . ' ' : '')
            . "$groupBy ORDER BY $order";
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        return $query->getResult();
    }

    /**
     * Get recent item reviews.
     *
     * @return array
     */
    public function getRecentItemReviews(): array
    {
        $dql = 'SELECT r.review AS Review, r.added as Added, u.id AS User_ID, u.username AS Username, u.name AS Name, '
            . 'i.id AS Item_ID, i.itemName AS Item_Name '
            . 'FROM ' . ItemsReview::class . ' r '
            . 'INNER JOIN ' . User::class . ' u ON r.user=u.id '
            . 'INNER JOIN ' . Item::class . ' i ON r.item=i.id '
            . 'WHERE r.approved=:approved ORDER BY r.added DESC, u.username';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('approved', Approved::Yes);
        return $query->getResult();
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
        $params = [
            'user' => $user instanceof UserEntityInterface ? $user->getId() : $user,
            'item' => $item instanceof ItemEntityInterface ? $item->getId() : $item,
        ];
        $dql = 'SELECT r FROM ' . ItemsReview::class . ' r WHERE r.item = :item AND r.user = :user';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
