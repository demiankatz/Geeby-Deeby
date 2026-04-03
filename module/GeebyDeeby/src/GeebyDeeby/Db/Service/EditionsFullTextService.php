<?php

/**
 * Database service for the Editions_Full_Text table.
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
use GeebyDeeby\Db\Entity\EditionsFullText;
use GeebyDeeby\Db\Entity\EditionsFullTextEntityInterface;
use GeebyDeeby\Db\Entity\EditionsReleaseDate;
use GeebyDeeby\Db\Entity\FullTextSource;
use GeebyDeeby\Db\Entity\Item;
use GeebyDeeby\Db\Entity\ItemsAltTitle;
use GeebyDeeby\Db\Entity\Series;

/**
 * Database service for the Editions_Full_Text table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsFullTextService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return EditionsFullTextEntityInterface
     */
    public function createEntity(): EditionsFullTextEntityInterface
    {
        $entity = new EditionsFullText();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?EditionsFullTextEntityInterface
     */
    public function getByPrimaryKey(int $id): ?EditionsFullTextEntityInterface
    {
        return $this->entityManager->find(EditionsFullText::class, $id);
    }

    /**
     * Get a list of full text links for a particular edition.
     *
     * @param int $edition Edition ID
     *
     * @return EditionsFullTextEntityInterface[]
     */
    public function getFullTextForEdition(int $edition): array
    {
        $dql = 'SELECT DISTINCT eft FROM ' . EditionsFullText::class . ' eft '
            . 'INNER JOIN ' . FullTextSource::class . ' fts ON eft.source=fts.id '
            . 'INNER JOIN ' . Edition::class . ' e ON eft.edition=e.id '
            . 'WHERE e.id = :edition '
            . 'ORDER BY fts.sourceName, eft.url';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('edition', $edition);
        return $query->getResult();
    }

    /**
     * Get a list of full text links for a particular edition (or its immediate
     * parent).
     *
     * @param int $edition Edition ID
     *
     * @return EditionsFullTextEntityInterface[]
     */
    public function getFullTextForEditionOrParentEdition(int $edition): array
    {
        $dql = 'SELECT DISTINCT eft FROM ' . EditionsFullText::class . ' eft '
            . 'INNER JOIN ' . FullTextSource::class . ' fts ON eft.source=fts.id '
            . 'INNER JOIN ' . Edition::class . ' e ON eft.edition=e.id OR eft.edition=e.parentEdition '
            . 'WHERE e.id = :edition '
            . 'ORDER BY fts.sourceName, eft.url';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('edition', $edition);
        return $query->getResult();
    }

    /**
     * Get a list of full text links for a particular item.
     *
     * @param int $item Item ID
     *
     * @return EditionsFullTextEntityInterface[]
     */
    public function getFullTextForItem(int $item): array
    {
        $dql = 'SELECT DISTINCT eft FROM ' . EditionsFullText::class . ' eft '
            . 'INNER JOIN ' . FullTextSource::class . ' fts ON eft.source=fts.id '
            . 'INNER JOIN ' . Edition::class . ' e ON eft.edition=e.id OR eft.edition=e.parentEdition '
            . 'WHERE e.item = :item '
            . 'ORDER BY fts.sourceName, e.editionName, eft.url';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('item', $item);
        return $query->getResult();
    }

    /**
     * Get a list of full text entries by full text source.
     *
     * @param int $source Full text source ID
     *
     * @return EditionsFullTextEntityInterface[]
     */
    public function getFullTextForSource(int $source): array
    {
        $dql = 'SELECT eft FROM ' . EditionsFullText::class . ' eft WHERE eft.source = :source ORDER BY eft.url';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('source', $source);
        return $query->getResult();
    }

    /**
     * Get a list of items with full text.
     *
     * @param ?int $series Series ID (optional limiter)
     * @param bool $fuzzy  Use fuzzy matching? (default = false)
     * @param ?int $source Full text source ID (optional limiter)
     *
     * @return array
     */
    public function getItemsWithFullText(
        ?int $series = null,
        bool $fuzzy = false,
        ?int $source = null
    ): array {
        $editionJoin = $fuzzy
            ? 'INNER JOIN ' . Edition::class . ' e2 ON eft.edition=e2.id '
            . 'INNER JOIN ' . Edition::class . ' e ON e2.item=e.item '
            : 'INNER JOIN ' . Edition::class . ' e ON eft.edition=e.id ';
        $where = $params = [];
        if ($series) {
            $where[] = 'e.series = :series';
            $params['series'] = $series;
        }
        if ($source) {
            $where[] = 'eft.source = :source';
            $params['source'] = $source;
        }
        $dql = 'SELECT MIN(erd.year) AS Earliest_Year, '
            . 'e.volume AS Volume, e.position AS Position, e.replacementNumber AS Replacement_Number, '
            . 'i.itemName AS Item_Name, i.id AS Item_ID, iat.altName AS Item_AltName, '
            . 's.seriesName AS Series_Name, s.id AS Series_ID, '
            . 'GROUP_CONCAT('
            . "COALESCE(childIat.altName, childI.itemName) ORDER BY childE.positionInParent SEPARATOR '||'"
            . ') AS Child_Items '
            . 'FROM ' . EditionsFullText::class . ' eft ' . $editionJoin
            . 'INNER JOIN ' . Item::class . ' i ON e.item=i.id '
            . 'INNER JOIN ' . Series::class . ' s ON e.series=s.id '
            . 'LEFT JOIN ' . EditionsReleaseDate::class . ' erd ON e.id=erd.edition '
            . 'LEFT JOIN ' . ItemsAltTitle::class . ' iat ON e.preferredItemAltName=iat.id '
            . 'LEFT JOIN ' . Edition::class . ' childE ON childE.parentEdition=e.id '
            . 'LEFT JOIN ' . Item::class . ' childI ON childE.item=childI.id '
            . 'LEFT JOIN ' . ItemsAltTitle::class . ' childIat ON childE.preferredItemAltName=childIat.id '
            . ($where ? ('WHERE ' . implode(' AND ', $where) . ' ') : '')
            . 'GROUP BY i.id, s.id, e.volume, e.position, e.replacementNumber '
            . 'ORDER BY s.seriesName, s.id, e.volume, e.position, e.replacementNumber, i.itemName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        return $query->getResult();
    }
}
