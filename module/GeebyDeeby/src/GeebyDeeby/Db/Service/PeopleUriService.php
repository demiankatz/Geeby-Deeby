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

use Doctrine\ORM\EntityManager;
use GeebyDeeby\Db\Entity\PeopleUriEntityInterface;
use GeebyDeeby\Db\Entity\PersonEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\PeopleURIs;
use GeebyDeeby\ServiceManager\Factory\Autowire;

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
     * Constructor
     *
     * @param EntityManager      $entityManager      Entity manager
     * @param PersistenceManager $persistenceManager Persistence manager
     * @param PeopleURIs         $peopleUrisTable    PeopleURIs table
     */
    public function __construct(
        EntityManager $entityManager,
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected PeopleURIs $peopleUrisTable
    ) {
        parent::__construct($entityManager, $persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return PeopleUriEntityInterface
     */
    public function createEntity(): PeopleUriEntityInterface
    {
        return $this->peopleUrisTable->createRow();
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
        return iterator_to_array($this->peopleUrisTable->getPeopleForURI($uri));
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
        $personId = $person instanceof PersonEntityInterface ? $person->getId() : $person;
        return iterator_to_array($this->peopleUrisTable->getURIsForPerson($personId));
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
        foreach ($this->peopleUrisTable->select(['Person_ID' => $personId, 'URI' => $uri]) as $row) {
            return $row;
        }
        return null;
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
        return iterator_to_array($this->peopleUrisTable->getPeopleWithURIs($startFrom));
    }
}
