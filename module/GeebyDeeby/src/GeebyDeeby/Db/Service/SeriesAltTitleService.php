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

use GeebyDeeby\Db\Entity\SeriesAltTitleEntityInterface;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\SeriesAltTitles;
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
     * @param PersistenceManager $persistenceManager   Persistence manager
     * @param SeriesAltTitles    $seriesAltTitlesTable SeriesAltTitles table
     */
    public function __construct(
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected SeriesAltTitles $seriesAltTitlesTable
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
        return $this->seriesAltTitlesTable->createRow();
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
        return iterator_to_array($this->seriesAltTitlesTable->getAltTitles($seriesID));
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
        return $this->seriesAltTitlesTable->getByPrimaryKey($id);
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
        return iterator_to_array($this->seriesAltTitlesTable->keywordSearch($tokens));
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
        foreach ($this->seriesAltTitlesTable->select(['Series_ID' => $seriesId, 'Sequence_ID' => $altId]) as $row) {
            return $row;
        }
        return null;
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
        $seriesId = $series instanceof SeriesEntityInterface ? $series->getId() : $series;
        foreach ($this->seriesAltTitlesTable->select(['Series_ID' => $seriesId, 'Series_AltName' => $title]) as $row) {
            return $row;
        }
        return null;
    }
}
