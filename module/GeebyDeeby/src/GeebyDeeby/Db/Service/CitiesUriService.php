<?php

/**
 * Database service for the Cities_URIs table.
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

use GeebyDeeby\Db\Entity\CitiesUri;
use GeebyDeeby\Db\Entity\CitiesUriEntityInterface;
use GeebyDeeby\Db\Entity\City;
use GeebyDeeby\Db\Entity\CityEntityInterface;
use GeebyDeeby\Db\Entity\Predicate;

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
     * Create an empty entity.
     *
     * @return CitiesUriEntityInterface
     */
    public function createEntity(): CitiesUriEntityInterface
    {
        $entity = new CitiesUri();
        $entity->setEntityManager($this->entityManager);
        return $entity;
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
        $dql = 'SELECT cu.id AS Sequence_ID, cu.uri AS URI, '
            . 'p.id AS Predicate_ID, p.predicate AS Predicate, p.abbreviation AS Predicate_Abbrev, '
            . 'c.id AS City_ID, c.cityName as City_Name'
            . ' FROM ' . CitiesUri::class . ' cu INNER JOIN ' . Predicate::class . ' p ON cu.predicate = p.id'
            . ' INNER JOIN ' . City::class . ' c ON cu.city = c.id'
            . ' WHERE cu.uri = :uri';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('uri', $uri);
        return $query->getResult();
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
        $dql = 'SELECT cu.id AS Sequence_ID, cu.uri AS URI, '
            . 'p.id AS Predicate_ID, p.predicate AS Predicate, p.abbreviation AS Predicate_Abbrev '
            . ' FROM ' . CitiesUri::class . ' cu INNER JOIN ' . Predicate::class . ' p ON cu.predicate = p.id'
            . ' WHERE cu.city = :city ORDER BY cu.uri, p.abbreviation';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('city', $city instanceof CityEntityInterface ? $city->getId() : $city);
        return $query->getResult();
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
        $dql = 'SELECT cu FROM ' . CitiesUri::class . ' cu WHERE cu.city = :city AND cu.uri = :uri';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters(['city' => $cityId, 'uri' => $uri]);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
