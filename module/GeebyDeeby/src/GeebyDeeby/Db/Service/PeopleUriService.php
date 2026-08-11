<?php

/**
 * Database service for the People_URIs table.
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

use GeebyDeeby\Db\Entity\PeopleUri;
use GeebyDeeby\Db\Entity\PeopleUriEntityInterface;
use GeebyDeeby\Db\Entity\Person;
use GeebyDeeby\Db\Entity\PersonEntityInterface;
use GeebyDeeby\Db\Entity\Predicate;

/**
 * Database service for the People_URIs table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PeopleUriService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return PeopleUriEntityInterface
     */
    public function createEntity(): PeopleUriEntityInterface
    {
        $entity = new PeopleUri();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get a list of people for the specified URI.
     *
     * @param string $uri URI
     *
     * @return array
     */
    public function getPeopleForURI(string $uri): array
    {
        $dql = 'SELECT pu.id AS Sequence_ID, pu.uri AS URI, person.id AS Person_ID, '
            . 'person.firstName AS First_Name, person.lastName AS Last_Name, person.extraDetails AS Extra_Details, '
            . 'p.id AS Predicate_ID, p.predicate AS Predicate, p.abbreviation AS Predicate_Abbrev'
            . ' FROM ' . PeopleUri::class . ' pu INNER JOIN ' . Predicate::class . ' p ON pu.predicate = p.id'
            . ' INNER JOIN ' . Person::class . ' person ON pu.person = person.id'
            . ' WHERE pu.uri = :uri ORDER BY person.lastName, person.firstName, person.extraDetails';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('uri', $uri);
        return $query->getResult();
    }

    /**
     * Get a list of URIs for the specified person.
     *
     * @param int|PersonEntityInterface $person Person entity or ID
     *
     * @return array
     */
    public function getURIsForPerson(int|PersonEntityInterface $person): array
    {
        $dql = 'SELECT pu.id AS Sequence_ID, pu.uri AS URI, '
            . 'p.id AS Predicate_ID, p.predicate AS Predicate, p.abbreviation AS Predicate_Abbrev '
            . ' FROM ' . PeopleUri::class . ' pu INNER JOIN ' . Predicate::class . ' p ON pu.predicate = p.id'
            . ' WHERE pu.person = :person ORDER BY pu.uri, p.abbreviation';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('person', $person instanceof PersonEntityInterface ? $person->getId() : $person);
        return $query->getResult();
    }

    /**
     * Retrieve an existing entry using a person ID and URI (null if not found).
     *
     * @param int    $personId Person ID
     * @param string $uri      URI
     *
     * @return ?PeopleUriEntityInterface
     */
    public function getByPersonAndUri(int $personId, string $uri): ?PeopleUriEntityInterface
    {
        $dql = 'SELECT pu FROM ' . PeopleUri::class . ' pu WHERE pu.person = :person AND pu.uri = :uri';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters(['person' => $personId, 'uri' => $uri]);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }

    /**
     * Get a list of people joined with URIs.
     *
     * @param ?string $startFrom Start retrieving with this last name (null to start at beginning)
     *
     * @return array
     */
    public function getPeopleWithURIs(?string $startFrom = null): array
    {
        $dql = 'SELECT pu.id AS Sequence_ID, pu.uri AS URI, p.id AS Person_ID, '
            . 'p.firstName AS First_Name, p.lastName AS Last_Name, p.extraDetails AS Extra_Details'
            . ' FROM ' . PeopleUri::class . ' pu'
            . ' INNER JOIN ' . Person::class . ' p ON pu.person = p.id';
        if ($startFrom) {
            $dql .= ' WHERE p.lastName > :startFrom';
        }
        $dql .= ' ORDER BY p.lastName, p.firstName, p.extraDetails';
        $query = $this->entityManager->createQuery($dql);
        if ($startFrom) {
            $query->setParameter('startFrom', $startFrom);
        }
        return $query->getResult();
    }
}
