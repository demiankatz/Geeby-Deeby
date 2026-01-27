<?php

/**
 * Database service for the Series_Publishers table.
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

use GeebyDeeby\Db\Entity\SeriesPublisherEntityInterface;
use GeebyDeeby\Db\Table\SeriesPublishers;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Series_Publishers table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesPublisherService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param SeriesPublishers $seriesPublishersTable SeriesPublishers table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected SeriesPublishers $seriesPublishersTable
    ) {
    }

    /**
     * Create an empty entity.
     *
     * @return SeriesPublisherEntityInterface
     */
    public function createEntity(): SeriesPublisherEntityInterface
    {
        return $this->seriesPublishersTable->createRow();
    }

    /**
     * Get a list of series for the specified city.
     *
     * @param int $cityID City ID
     *
     * @return array
     */
    public function getSeriesForCity(int $cityID): array
    {
        return iterator_to_array($this->seriesPublishersTable->getSeriesForCity($cityID));
    }

    /**
     * Get a list of series for the specified country.
     *
     * @param int $countryID Country ID
     *
     * @return array
     */
    public function getSeriesForCountry(int $countryID): array
    {
        return iterator_to_array($this->seriesPublishersTable->getSeriesForCountry($countryID));
    }

    /**
     * Get a list of series for the specified address.
     *
     * @param int $addressID Address ID
     *
     * @return array
     */
    public function getSeriesForAddress(int $addressID): array
    {
        return iterator_to_array($this->seriesPublishersTable->select(['Address_ID' => $addressID]));
    }

    /**
     * Get a list of series for the specified imprint.
     *
     * @param int $imprintID Imprint ID
     *
     * @return array
     */
    public function getSeriesForImprint(int $imprintID): array
    {
        return iterator_to_array($this->seriesPublishersTable->select(['Imprint_ID' => $imprintID]));
    }

    /**
     * Get a list of series for the specified publisher.
     *
     * @param int $publisherID Publisher ID
     *
     * @return array
     */
    public function getSeriesForPublisher(int $publisherID): array
    {
        return iterator_to_array($this->seriesPublishersTable->getSeriesForPublisher($publisherID));
    }

    /**
     * Get a list of publishers for the specified series.
     *
     * @param int $seriesID Series ID
     *
     * @return array
     */
    public function getPublishersForSeries(int $seriesID): array
    {
        return iterator_to_array($this->seriesPublishersTable->getPublishers($seriesID));
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?SeriesPublisherEntityInterface
     */
    public function getByPrimaryKey(int $id): ?SeriesPublisherEntityInterface
    {
        return $this->seriesPublishersTable->getByPrimaryKey($id);
    }

    /**
     * Retrieve an existing entry using an series ID and sequence ID (null if not found).
     *
     * @param int    $seriesId Series ID
     * @param string $spId     Series/publisher primary key
     *
     * @return ?SeriesPublisherEntityInterface
     */
    public function getBySeriesAndId(int $seriesId, string $spId): ?SeriesPublisherEntityInterface
    {
        $query = ['Series_ID' => $seriesId, 'Series_Publisher_ID' => $spId];
        foreach ($this->seriesPublishersTable->select($query) as $row) {
            return $row;
        }
        return null;
    }
}
