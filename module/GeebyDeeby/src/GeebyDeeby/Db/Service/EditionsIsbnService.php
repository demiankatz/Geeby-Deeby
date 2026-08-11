<?php

/**
 * Database service for the Editions_ISBNs table.
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
use GeebyDeeby\Db\Entity\EditionsIsbn;
use GeebyDeeby\Db\Entity\EditionsIsbnEntityInterface;
use GeebyDeeby\Db\Entity\Item;

/**
 * Database service for the Editions_ISBNs table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsIsbnService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return EditionsIsbnEntityInterface
     */
    public function createEntity(): EditionsIsbnEntityInterface
    {
        $entity = new EditionsIsbn();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Retrieve a row by its primary key.
     *
     * @param int $id Identifier to retrieve
     *
     * @return ?EditionsIsbnEntityInterface
     */
    public function getByPrimaryKey(int $id): ?EditionsIsbnEntityInterface
    {
        return $this->entityManager->find(EditionsIsbn::class, $id);
    }

    /**
     * Get a list of ISBNs for the specified edition.
     *
     * @param int $editionID Edition ID
     *
     * @return EditionsIsbnEntityInterface[]
     */
    public function getISBNsForEdition(int $editionID): array
    {
        $dql = 'SELECT ei FROM ' . EditionsIsbn::class
            . ' ei WHERE ei.edition = :edition ORDER BY ei.isbn13';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('edition', $editionID);
        return $query->getResult();
    }

    /**
     * Get a list of ISBNs for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return EditionsIsbnEntityInterface[]
     */
    public function getISBNsForItem(int $itemID): array
    {
        $dql = 'SELECT ei FROM ' . EditionsIsbn::class . ' ei '
            . 'INNER JOIN ' . Edition::class . ' e ON e.id=ei.edition '
            . 'WHERE e.item = :item ORDER BY ei.isbn13';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('item', $itemID);
        return $query->getResult();
    }

    /**
     * Find items matching an ISBN search query.
     *
     * @param string $q Query
     *
     * @return array
     */
    public function searchForItems(string $q): array
    {
        $dql = 'SELECT i.id AS Item_ID, i.itemName AS Item_Name, ei.isbn10 AS ISBN, ei.isbn13 AS ISBN13 '
            . 'FROM ' . EditionsIsbn::class . ' ei '
            . 'INNER JOIN ' . Edition::class . ' e ON e.id=ei.edition '
            . 'INNER JOIN ' . Item::class . ' i ON e.item=i.id '
            . 'WHERE ei.isbn10 LIKE :query OR ei.isbn13 LIKE :query ORDER BY i.itemName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('query', "$q%");
        return $query->getResult();
    }
}
