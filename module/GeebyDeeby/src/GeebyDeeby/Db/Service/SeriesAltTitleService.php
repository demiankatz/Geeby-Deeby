<?php

/**
 * Database service for the Series_AltTitles table.
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
use GeebyDeeby\Db\Entity\Note;
use GeebyDeeby\Db\Entity\Series;
use GeebyDeeby\Db\Entity\SeriesAltTitle;
use GeebyDeeby\Db\Entity\SeriesAltTitleEntityInterface;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Series_AltTitles table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesAltTitleService extends AbstractDbService
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
     * @return SeriesAltTitleEntityInterface
     */
    public function createEntity(): SeriesAltTitleEntityInterface
    {
        $entity = new SeriesAltTitle();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get a list of alternate titles for the specified series.
     *
     * @param int $seriesID Series ID
     *
     * @return array
     */
    public function getAltTitles(int $seriesID): array
    {
        $dql = 'SELECT s.id AS Series_ID, sa.altName as Series_AltName, n.id AS Note_ID, n.note AS Note, '
            . 'sa.id AS Sequence_ID FROM ' . SeriesAltTitle::class . ' sa LEFT JOIN '
            . Note::class . ' n ON sa.note=n.id JOIN ' . Series::class . ' s ON sa.series=s.id '
            . 'WHERE s.id = :series';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('series', $seriesID);
        return $query->getResult();
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?SeriesAltTitleEntityInterface
     */
    public function getByPrimaryKey(int $id): ?SeriesAltTitleEntityInterface
    {
        return $this->entityManager->find(SeriesAltTitle::class, $id);
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
        $where = array_map(fn ($i) => 'sa.altName LIKE ?' . $i, array_keys($tokens));
        $dql = 'SELECT s.id AS Series_ID, sa.altName AS Series_AltName FROM '
            . SeriesAltTitle::class . ' sa JOIN ' . Series::class . ' s ON sa.series=s.id WHERE '
            . implode(' AND ', $where) . ' ORDER BY sa.altName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters(array_map(fn ($token) => "%$token%", $tokens));
        return $query->getResult();
    }

    /**
     * Retrieve an existing entry using an series ID and sequence ID (null if not found).
     *
     * @param int    $seriesId Series ID
     * @param string $altId    Alt title sequence ID
     *
     * @return ?SeriesAltTitleEntityInterface
     */
    public function getBySeriesAndId(int $seriesId, string $altId): ?SeriesAltTitleEntityInterface
    {
        $dql = 'SELECT s FROM ' . SeriesAltTitle::class . ' s WHERE s.series = :series AND s.id = :id';
        $query = $this->entityManager->createQuery($dql);
        $query->setMaxResults(1);
        $query->setParameters(['series' => $seriesId, 'id' => $altId]);
        return $query->getOneOrNullResult();
    }

    /**
     * Retrieve an existing entry using an series ID and title (null if not found).
     *
     * @param int|SeriesEntityInterface $series Series ID or entity
     * @param string                    $title  Alt title
     *
     * @return ?SeriesAltTitleEntityInterface
     */
    public function getBySeriesAndTitle(
        int|SeriesEntityInterface $series,
        string $title
    ): ?SeriesAltTitleEntityInterface {
        $dql = 'SELECT s FROM ' . SeriesAltTitle::class . ' s WHERE s.series = :series AND s.altName = :title';
        $query = $this->entityManager->createQuery($dql);
        $query->setMaxResults(1);
        $seriesId = $series instanceof SeriesEntityInterface ? $series->getId() : $series;
        $query->setParameters(['series' => $seriesId, 'title' => $title]);
        return $query->getOneOrNullResult();
    }

    /**
     * Get entities by alt title.
     *
     * @param string $title Title to look up
     *
     * @return SeriesAltTitleEntityInterface[]
     */
    public function getByAltTitle(string $title): array
    {
        $dql = 'SELECT s FROM ' . SeriesAltTitle::class . ' s WHERE s.altName = :title';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('title', $title);
        return $query->getResult();
    }
}
