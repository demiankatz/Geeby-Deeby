<?php

/**
 * Database service for the Countries_URIs table.
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
use GeebyDeeby\Db\Entity\CountriesUri;
use GeebyDeeby\Db\Entity\CountriesUriEntityInterface;
use GeebyDeeby\Db\Entity\Country;
use GeebyDeeby\Db\Entity\CountryEntityInterface;
use GeebyDeeby\Db\Entity\Predicate;
use GeebyDeeby\Db\PersistenceManager;
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
     * @return CountriesUriEntityInterface
     */
    public function createEntity(): CountriesUriEntityInterface
    {
        $entity = new CountriesUri();
        $entity->setEntityManager($this->entityManager);
        return $entity;
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
        $dql = 'SELECT cu.id AS Sequence_ID, cu.uri AS URI, '
            . 'p.id AS Predicate_ID, p.predicate AS Predicate, p.abbreviation AS Predicate_Abbrev, '
            . 'c.id AS City_ID, c.cityName as City_Name'
            . ' FROM ' . CountriesUri::class . ' cu INNER JOIN ' . Predicate::class . ' p ON cu.predicate = p.id'
            . ' INNER JOIN ' . Country::class . ' c ON cu.city = c.id'
            . ' WHERE cu.uri = :uri';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('uri', $uri);
        return $query->getResult();
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
        $dql = 'SELECT cu.id AS Sequence_ID, cu.uri AS URI, '
            . 'p.id AS Predicate_ID, p.predicate AS Predicate, p.abbreviation AS Predicate_Abbrev '
            . ' FROM ' . CountriesUri::class . ' cu INNER JOIN ' . Predicate::class . ' p ON cu.predicate = p.id'
            . ' WHERE cu.country = :country';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('country', $country instanceof CountryEntityInterface ? $country->getId() : $country);
        return $query->getResult();
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
        $dql = 'SELECT cu FROM ' . CountriesUri::class . ' cu WHERE cu.country = :country AND cu.uri = :uri';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters(['country' => $countryId, 'uri' => $uri]);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
