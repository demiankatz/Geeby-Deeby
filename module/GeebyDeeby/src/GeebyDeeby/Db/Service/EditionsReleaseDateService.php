<?php

/**
 * Database service for the Editions_Release_Dates table.
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
use GeebyDeeby\Db\Entity\EditionEntityInterface;
use GeebyDeeby\Db\Entity\EditionsReleaseDate;
use GeebyDeeby\Db\Entity\EditionsReleaseDateEntityInterface;
use GeebyDeeby\Db\Entity\Item;
use GeebyDeeby\Db\Entity\ItemsAltTitle;
use GeebyDeeby\Db\Entity\Note;

/**
 * Database service for the Editions_Release_Dates table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsReleaseDateService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return EditionsReleaseDateEntityInterface
     */
    public function createEntity(): EditionsReleaseDateEntityInterface
    {
        $entity = new EditionsReleaseDate();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get a list of dates for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return EditionsReleaseDateEntityInterface[]
     */
    public function getDatesForItem(int $itemID): array
    {
        $dql = 'SELECT DISTINCT d FROM ' . EditionsReleaseDate::class . ' d '
            . 'LEFT JOIN ' . Edition::class . ' e ON d.edition=e.id OR d.edition=e.parentEdition '
            . 'WHERE e.item=:item '
            . 'ORDER BY d.year, d.month, d.day, e.editionName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('item', $itemID);
        return $query->getResult();
    }

    /**
     * Get a list of dates for the specified edition.
     *
     * @param int $editionID Edition ID
     *
     * @return EditionsReleaseDateEntityInterface[]
     */
    public function getDatesForEdition(int $editionID): array
    {
        $dql = 'SELECT DISTINCT d FROM ' . EditionsReleaseDate::class . ' d '
            . 'LEFT JOIN ' . Edition::class . ' e ON d.edition=e.id '
            . 'WHERE e.id=:edition '
            . 'ORDER BY d.year, d.month, d.day';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('edition', $editionID);
        return $query->getResult();
    }

    /**
     * Get a list of dates for the specified edition (or its immediate parent).
     *
     * @param int $editionID Edition ID
     *
     * @return EditionsReleaseDateEntityInterface[]
     */
    public function getDatesForEditionOrParentEdition(int $editionID): array
    {
        $dql = 'SELECT DISTINCT d FROM ' . EditionsReleaseDate::class . ' d '
            . 'LEFT JOIN ' . Edition::class . ' e ON d.edition=e.id OR d.edition=e.parentEdition '
            . 'WHERE e.id=:edition '
            . 'ORDER BY d.year, d.month, d.day';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('edition', $editionID);
        return $query->getResult();
    }

    /**
     * Get a list of items sorted by publication date.
     *
     * @return array
     */
    public function getItemsByYear(): array
    {
        $dql = 'SELECT d.month AS Month, d.day AS Day, d.year AS Year, '
            . 'i.id AS Item_ID, i.itemName AS Item_Name, iat.altName AS Item_AltName, '
            . 'n.id AS Note_ID, n.note AS Note, e.id AS Edition_ID, e.editionName AS Edition_Name '
            . 'FROM ' . EditionsReleaseDate::class . ' d '
            . 'INNER JOIN ' . Edition::class . ' e ON d.edition=e.id '
            . 'INNER JOIN ' . Item::class . ' i ON e.item=i.id '
            . 'LEFT JOIN ' . ItemsAltTitle::class . ' iat ON e.preferredItemAltName=iat.id '
            . 'LEFT JOIN ' . Note::class . ' n ON d.note=n.id '
            . 'ORDER BY d.year, i.itemName, e.editionName';
        $query = $this->entityManager->createQuery($dql);
        return $query->getResult();
    }

    /**
     * Get an entity by edition and date.
     *
     * @param int|EditionEntityInterface $edition Edition entity or ID
     * @param int                        $year    Year (or -1 for unpublished)
     * @param int                        $month   Month (or 0 for unspecified)
     * @param int                        $day     Day (or 0 for unspecified)
     *
     * @return ?EditionsReleaseDateEntityInterface
     */
    public function getByEditionAndYearAndMonthAndDay(
        int|EditionEntityInterface $edition,
        int $year,
        int $month,
        int $day
    ): ?EditionsReleaseDateEntityInterface {
        $params = ['edition' => $edition instanceof EditionEntityInterface ? $edition->getId() : $edition]
            + compact('year', 'month', 'day');
        $dql = 'SELECT d FROM ' . EditionsReleaseDate::class
            . ' d WHERE d.edition = :edition AND d.year = :year AND d.month = :month AND d.day = :day';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
