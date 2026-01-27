<?php

/**
 * Database service for the Countries_URIs table.
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

use GeebyDeeby\Db\Entity\CountriesUriEntityInterface;
use GeebyDeeby\Db\Entity\CountryEntityInterface;
use GeebyDeeby\Db\Table\CountriesURIs;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Countries_URIs table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class CountriesUriService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param CountriesURIs $countriesUrisTable CountriesURIs table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected CountriesURIs $countriesUrisTable
    ) {
    }

    /**
     * Create an empty entity.
     *
     * @return CountriesUriEntityInterface
     */
    public function createEntity(): CountriesUriEntityInterface
    {
        return $this->countriesUrisTable->createRow();
    }

    /**
     * Get a list of countries for the specified URI.
     *
     * @param string $uri URI
     *
     * @return array
     */
    public function getCountriesForURI(string $uri): array
    {
        return iterator_to_array($this->countriesUrisTable->getCountriesForURI($uri));
    }

    /**
     * Get a list of URIs for the specified country.
     *
     * @param int|CountryEntityInterface $country Country entity or ID
     *
     * @return array
     */
    public function getURIsForCountry(int|CountryEntityInterface $country): array
    {
        $countryId = $country instanceof CountryEntityInterface ? $country->getId() : $country;
        return iterator_to_array($this->countriesUrisTable->getURIsForCountry($countryId));
    }

    /**
     * Retrieve an existing entry using a country ID and URI (null if not found).
     *
     * @param int    $countryId Country ID
     * @param string $uri       URI
     *
     * @return ?CountriesUriEntityInterface
     */
    public function getByCountryAndUri(int $countryId, string $uri): ?CountriesUriEntityInterface
    {
        foreach ($this->countriesUrisTable->select(['Country_ID' => $countryId, 'URI' => $uri]) as $row) {
            return $row;
        }
        return null;
    }
}
