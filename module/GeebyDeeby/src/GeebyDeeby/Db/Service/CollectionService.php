<?php

/**
 * Database service for the Collections table.
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

use GeebyDeeby\Db\Entity\Collection;
use GeebyDeeby\Db\Entity\CollectionEntityInterface;
use GeebyDeeby\Db\Entity\Edition;
use GeebyDeeby\Db\Entity\Enum\CollectionStatus;
use GeebyDeeby\Db\Entity\Item;
use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\ItemsAltTitle;
use GeebyDeeby\Db\Entity\Language;
use GeebyDeeby\Db\Entity\Series;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\Entity\User;
use GeebyDeeby\Db\Entity\UserEntityInterface;

/**
 * Database service for the Collections table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class CollectionService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return CollectionEntityInterface
     */
    public function createEntity(): CollectionEntityInterface
    {
        $entity = new Collection();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get an existing collection entry.
     *
     * @param int|UserEntityInterface   $user   User ID or entity
     * @param int|ItemEntityInterface   $item   Item ID or entity
     * @param int|SeriesEntityInterface $series Series ID or entity
     * @param string                    $status Status (extra/have/want)
     *
     * @return ?CollectionEntityInterface
     */
    public function getExistingEntry(
        int|UserEntityInterface $user,
        int|ItemEntityInterface $item,
        int|SeriesEntityInterface $series,
        string $status
    ): ?CollectionEntityInterface {
        $dql = 'SELECT c FROM ' . Collection::class
            . ' c WHERE c.user=:user AND c.item=:item AND c.series=:series AND c.status=:status';
        $params = [
            'user' => $user instanceof UserEntityInterface ? $user->getId() : $user,
            'item' => $item instanceof ItemEntityInterface ? $item->getId() : $item,
            'series' => $series instanceof SeriesEntityInterface ? $series->getId() : $series,
            'status' => CollectionStatus::from($status),
        ];
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }

    /**
     * Delete an existing collection entry.
     *
     * @param int|UserEntityInterface   $user   User ID or entity
     * @param int|ItemEntityInterface   $item   Item ID or entity
     * @param int|SeriesEntityInterface $series Series ID or entity
     * @param string                    $status Status (extra/have/want)
     *
     * @return ?CollectionEntityInterface
     */
    public function deleteEntry(
        int|UserEntityInterface $user,
        int|ItemEntityInterface $item,
        int|SeriesEntityInterface $series,
        string $status
    ): void {
        if ($entity = $this->getExistingEntry($user, $item, $series, $status)) {
            $this->deleteEntity($entity);
        }
    }

    /**
     * Create or update a collection entry.
     *
     * @param int|UserEntityInterface   $user   User ID or entity
     * @param int|ItemEntityInterface   $item   Item ID or entity
     * @param int|SeriesEntityInterface $series Series ID or entity
     * @param string                    $status Status (extra/have/want)
     * @param ?string                   $note   Note to set (null for none)
     *
     * @return ?CollectionEntityInterface
     */
    public function updateEntry(
        int|UserEntityInterface $user,
        int|ItemEntityInterface $item,
        int|SeriesEntityInterface $series,
        string $status,
        ?string $note
    ): void {
        if (!($entity = $this->getExistingEntry($user, $item, $series, $status))) {
            $entity = $this->createEntity()->setUser($user)->setItem($item)->setSeries($series)->setStatus($status);
        }
        $entity->setNote($note);
        $this->persistEntity($entity);
    }

    /**
     * Get a list of users owning/interested in an item.
     *
     * @param int     $itemID Item ID
     * @param ?string $type   List type ('extra', 'have', 'want' or null for all)
     *
     * @return array
     */
    public function getForItem(int $itemID, ?string $type = null): array
    {
        $params = ['item' => $itemID];
        $dql = 'SELECT u.id AS User_ID, u.username AS Username, c.note AS Collection_Note, '
            . 'c.status AS Collection_Status FROM ' . Collection::class
            . ' c INNER JOIN ' . User::class . ' u ON c.user=u.id WHERE c.item=:item ';
        if ($type) {
            $dql .= 'AND c.status=:status ';
            $params['status'] = CollectionStatus::from($type);
        }
        $dql .= 'ORDER BY u.username, c.status, c.note';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        return $query->getResult();
    }

    /**
     * Get a list of items on a user's collection list(s).
     *
     * @param int                  $userID      User ID
     * @param string|string[]|null $type        List type ('extra', 'have', 'want' -- or
     * array of these values -- or null for all)
     * @param bool                 $groupByLang Should we group by language?
     *
     * @return array
     */
    public function getForUser(int $userID, string|array|null $type = null, bool $groupByLang = false): array
    {
        $extraJoins = $extraSelects = $extraWhere = '';
        $order = 's.seriesName, s.id, c.status, e.volume, e.position, e.replacementNumber, i.itemName';
        $params = ['user' => $userID];
        if ($groupByLang) {
            $extraJoins = 'INNER JOIN ' . Language::class . ' l ON s.language=l.id ';
            $extraSelects = ', l.id AS Language_ID, l.languageName AS Language_Name';
            $order = 'l.languageName, ' . $order;
        }
        if ($type) {
            $extraWhere = 'AND c.status IN (:type) ';
            $params['type'] = (array)$type;
        }
        $dql = 'SELECT c.status AS Collection_Status, c.note AS Collection_Note, '
            . 'i.id AS Item_ID, i.itemName AS Item_Name, '
            . 'iat.altName as Item_AltName, '
            . 'e.volume AS Volume, e.position AS Position, e.replacementNumber AS Replacement_Number, '
            . 's.id AS Series_ID, s.seriesName AS Series_Name' . $extraSelects
            . ' FROM ' . Collection::class . ' c INNER JOIN ' . Item::class . ' i ON c.item=i.id '
            . 'INNER JOIN ' . Edition::class . ' e ON i.id=e.item '
            . 'LEFT JOIN ' . ItemsAltTitle::class . ' iat ON e.preferredItemAltName=iat.id '
            . 'INNER JOIN ' . Series::class . ' s ON c.series=s.id AND e.series=s.id ' . $extraJoins
            . 'WHERE c.user = :user ' . $extraWhere . 'GROUP BY ' . $order . ', i.id ORDER BY ' . $order;
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        return $query->getResult();
    }

    /**
     * Find items from other users' lists that have $desiredStatus and match items on
     * $userID's lists that have $userStatus.
     *
     * @param int    $userID        User ID
     * @param string $userStatus    The user's status that should be matched against $desiredStatus
     * @param string $desiredStatus The status for which to retrieve matching items
     *
     * @return array
     */
    public function compareCollections(int $userID, string $userStatus, string $desiredStatus): array
    {
        $orderAndGroup = 'u.username, s.seriesName, s.id, e.volume, e.position, e.replacementNumber, i.itemName, i.id';
        $dql = 'SELECT c.status AS Collection_Status, c.note AS Collection_Note,'
            . ' o.note AS Other_Note,'
            . ' i.id AS Item_ID, i.itemName AS Item_Name,'
            . ' s.id AS Series_ID, s.seriesName AS Series_Name,'
            . ' u.id AS User_ID, u.username AS Username, u.name AS Name,'
            . ' e.id AS Edition_ID, e.editionName AS Edition_Name, e.volume AS Volume, e.position AS Position,'
            . ' e.replacementNumber as Replacement_Number, iat.altName AS Item_AltName'
            . ' FROM ' . Collection::class . ' c'
            . ' INNER JOIN ' . Collection::class . ' o ON c.series=o.series AND c.item=o.item'
            . ' INNER JOIN ' . Series::class . ' s ON c.series=s.id'
            . ' INNER JOIN ' . Item::class . ' i ON c.item=i.id'
            . ' INNER JOIN ' . User::class . ' u ON c.user=u.id'
            . ' INNER JOIN ' . Edition::class . ' e ON c.item=e.item AND c.series=e.series'
            . ' LEFT JOIN ' . ItemsAltTitle::class . ' iat ON e.preferredItemAltName=iat.id'
            . ' WHERE o.user = :user AND c.user != :user AND o.status = :userStatus AND c.status = :desiredStatus'
            . ' GROUP BY ' . $orderAndGroup
            . ' ORDER BY ' . $orderAndGroup;
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters(compact('userStatus', 'desiredStatus') + ['user' => $userID]);
        return $query->getResult();
    }

    /**
     * Get statistics on a user's collection.
     *
     * @param int $userID User ID
     *
     * @return array
     */
    public function getUserStatistics(int $userID): array
    {
        $dql = 'SELECT c.status, count(c.item) AS count FROM '
            . Collection::class . ' c WHERE c.user=:user GROUP BY c.status';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('user', $userID);
        $retVal = ['have' => 0, 'want' => 0, 'extra' => 0];
        foreach ($query->getResult() as $current) {
            $retVal[$current['status']->value] = $current['count'];
        }
        return $retVal;
    }
}
