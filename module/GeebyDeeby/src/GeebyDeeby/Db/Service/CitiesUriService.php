<?php

/**
 * Database service for the Cities_URIs table.
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

use GeebyDeeby\Db\Entity\CitiesUriEntityInterface;
use GeebyDeeby\Db\Entity\CityEntityInterface;
use GeebyDeeby\Db\Table\CitiesURIs;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Cities_URIs table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class CitiesUriService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param CitiesURIs $citiesUrisTable CitiesURIs table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected CitiesURIs $citiesUrisTable
    ) {
    }

    /**
     * Create an empty entity
     *
     * @return CitiesUriEntityInterface
     */
    public function createEntity(): CitiesUriEntityInterface
    {
        return $this->citiesUrisTable->createRow();
    }

    /**
     * Get a list of cities for the specified URI.
     *
     * @param string $uri URI
     *
     * @return array
     */
    public function getCitiesForURI(string $uri): array
    {
        return iterator_to_array($this->citiesUrisTable->getCitiesForURI($uri));
    }

    /**
     * Get a list of URIs for the specified city.
     *
     * @param int|CityEntityInterface $city City entity or ID
     *
     * @return array
     */
    public function getURIsForCity(int|CityEntityInterface $city): array
    {
        $cityId = $city instanceof CityEntityInterface ? $city->getId() : $city;
        return iterator_to_array($this->citiesUrisTable->getURIsForCity($cityId));
    }

    /**
     * Retrieve an existing entry using a city ID and URI (null if not found).
     *
     * @param int    $cityId City ID
     * @param string $uri    URI
     *
     * @return ?CitiesUriEntityInterface
     */
    public function getByCityAndUri(int $cityId, string $uri): ?CitiesUriEntityInterface
    {
        foreach ($this->citiesUrisTable->select(['City_ID' => $cityId, 'URI' => $uri]) as $row) {
            return $row;
        }
        return null;
    }
}
