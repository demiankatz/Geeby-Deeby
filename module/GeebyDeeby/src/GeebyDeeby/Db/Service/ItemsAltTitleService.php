<?php

/**
 * Database service for the Items_AltTitles table.
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

use Doctrine\ORM\EntityManager;
use GeebyDeeby\Db\Entity\Item;
use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\ItemsAltTitle;
use GeebyDeeby\Db\Entity\ItemsAltTitleEntityInterface;
use GeebyDeeby\Db\Entity\Note;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Items_AltTitles table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsAltTitleService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param EntityManager      $entityManager      Entity manager
     * @param PersistenceManager $persistenceManager Persistence manager
     */
    #[Autowire()]
    public function __construct(
        protected EntityManager $entityManager,
        PersistenceManager $persistenceManager,
    ) {
        parent::__construct($persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return ItemsAltTitleEntityInterface
     */
    public function createEntity(): ItemsAltTitleEntityInterface
    {
        $entity = new ItemsAltTitle();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get a list of alternate titles for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return array
     */
    public function getAltTitles(int $itemID): array
    {
        $dql = 'SELECT i.id AS Item_ID, ia.altName as Item_AltName, n.id AS Note_ID, n.note AS Note, '
            . 'ia.id AS Sequence_ID FROM ' . ItemsAltTitle::class . ' ia LEFT JOIN '
            . Note::class . ' n ON ia.note=n.id JOIN ' . Item::class . ' i ON ia.item=i.id '
            . 'WHERE i.id = :item ORDER BY ia.altName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('item', $itemID);
        return $query->getResult();
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?ItemsAltTitleEntityInterface
     */
    public function getByPrimaryKey(int $id): ?ItemsAltTitleEntityInterface
    {
        return $this->entityManager->find(ItemsAltTitle::class, $id);
    }

    /**
     * Perform a keyword search.
     *
     * @param array $tokens Keywords.
     *
     * @return array
     */
    public function keywordSearch(array $tokens): array
    {
        $where = array_map(fn ($i) => 'ia.altName LIKE ?' . $i, array_keys($tokens));
        $dql = 'SELECT i.id AS Item_ID, ia.altName AS Item_AltName FROM '
            . ItemsAltTitle::class . ' ia JOIN ' . Item::class . ' i ON ia.item=i.id WHERE '
            . implode(' AND ', $where) . ' ORDER BY ia.altName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters(array_map(fn ($token) => "%$token%", $tokens));
        return $query->getResult();
    }

    /**
     * Retrieve an existing entry using an item ID and sequence ID (null if not found).
     *
     * @param int    $itemId Item ID
     * @param string $altId  Alt title sequence ID
     *
     * @return ?ItemsAltTitleEntityInterface
     */
    public function getByItemAndId(int $itemId, string $altId): ?ItemsAltTitleEntityInterface
    {
        $dql = 'SELECT i FROM ' . ItemsAltTitle::class . ' i WHERE i.item = :item AND i.id = :id';
        $query = $this->entityManager->createQuery($dql);
        $query->setMaxResults(1);
        $query->setParameters(['item' => $itemId, 'id' => $altId]);
        return $query->getOneOrNullResult();
    }

    /**
     * Retrieve an existing entry using an item ID and title (null if not found).
     *
     * @param int|ItemEntityInterface $item  Item entity or ID
     * @param string                  $title Alt title
     *
     * @return ?ItemsAltTitleEntityInterface
     */
    public function getByItemAndTitle(int|ItemEntityInterface $item, string $title): ?ItemsAltTitleEntityInterface
    {
        $dql = 'SELECT i FROM ' . ItemsAltTitle::class . ' i WHERE i.item = :item AND i.altName = :title';
        $query = $this->entityManager->createQuery($dql);
        $query->setMaxResults(1);
        $itemId = $item instanceof ItemEntityInterface ? $item->getId() : $item;
        $query->setParameters(['item' => $itemId, 'title' => $title]);
        return $query->getOneOrNullResult();
    }
}
