<?php

/**
 * Database service for the Collections table.
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

use GeebyDeeby\Db\Entity\CollectionEntityInterface;
use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\Entity\UserEntityInterface;
use GeebyDeeby\Db\Table\Collections;
use GeebyDeeby\ServiceManager\Factory\Autowire;

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
     * Constructor
     *
     * @param Collections $collectionTable Collections table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected Collections $collectionTable
    ) {
    }

    /**
     * Create an empty entity.
     *
     * @return CollectionEntityInterface
     */
    public function createEntity(): CollectionEntityInterface
    {
        return $this->collectionTable->createRow();
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
        $where = [
            'User_ID' => $user instanceof UserEntityInterface ? $user->getId() : $user,
            'Item_ID' => $item instanceof ItemEntityInterface ? $item->getId() : $item,
            'Series_ID' => $series instanceof SeriesEntityInterface ? $series->getId() : $series,
            'Collection_Status' => $status,
        ];
        foreach ($this->collectionTable->select($where) as $row) {
            return $row;
        }
        return null;
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
        return iterator_to_array($this->collectionTable->getForItem($itemID, $type));
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
        return iterator_to_array($this->collectionTable->getForUser($userID, $type, $groupByLang));
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
        return iterator_to_array($this->collectionTable->compareCollections($userID, $userStatus, $desiredStatus));
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
        return $this->collectionTable->getUserStatistics($userID);
    }
}
