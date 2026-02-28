<?php

/**
 * Database service for the Editions_Credits table.
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
use GeebyDeeby\Db\Entity\EditionEntityInterface;
use GeebyDeeby\Db\Entity\EditionsCreditEntityInterface;
use GeebyDeeby\Db\Entity\PersonEntityInterface;
use GeebyDeeby\Db\Entity\RoleEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\EditionsCredits;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Editions_Credits table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsCreditService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param EntityManager      $entityManager        Entity manager
     * @param PersistenceManager $persistenceManager   Persistence manager
     * @param EditionsCredits    $editionsCreditsTable EditionsCredits table
     */
    public function __construct(
        EntityManager $entityManager,
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected EditionsCredits $editionsCreditsTable
    ) {
        parent::__construct($entityManager, $persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return EditionsCreditEntityInterface
     */
    public function createEntity(): EditionsCreditEntityInterface
    {
        return $this->editionsCreditsTable->createRow();
    }

    /**
     * Get a list of credits attached to the specified person.
     *
     * @param int    $personID Person ID
     * @param string $sort     Type of sorting (series/title/year)
     *
     * @return array
     */
    public function getCreditsForPerson(int $personID, string $sort = 'series'): array
    {
        // Special case: bringing series into the mix makes things more complex:
        if ($sort == 'series') {
            return $this->getSeriesCreditsForPerson($personID);
        }
        return $this->getItemCreditsForPerson($personID, $sort);
    }

    /**
     * Get a list of credits attached to the specified person, sorted by
     * item.
     *
     * @param int    $personID    Person ID
     * @param string $sort        Type of sorting (title or year)
     * @param bool   $includeYear Should we include the year in the response?
     *
     * @return array
     */
    public function getItemCreditsForPerson(
        int $personID,
        string $sort = 'title',
        bool $includeYear = true
    ): array {
        return iterator_to_array($this->editionsCreditsTable->getItemCreditsForPerson($personID, $sort, $includeYear));
    }

    /**
     * Get a list of credits attached to the specified person, sorted by
     * series.
     *
     * @param int $personID Person ID
     *
     * @return array
     */
    public function getSeriesCreditsForPerson(int $personID): array
    {
        return iterator_to_array($this->editionsCreditsTable->getSeriesCreditsForPerson($personID));
    }

    /**
     * Get a list of credits attached to the specified edition.
     *
     * @param int $editionID Edition ID
     *
     * @return array
     */
    public function getCreditsForEdition(int $editionID): array
    {
        return iterator_to_array($this->editionsCreditsTable->getCreditsForEdition($editionID));
    }

    /**
     * Get a list of credits attached to the specified item.
     *
     * @param int  $itemID Item ID
     * @param bool $group  Should we group by person/role?
     *
     * @return array
     */
    public function getCreditsForItem(int $itemID, bool $group = false): array
    {
        return iterator_to_array($this->editionsCreditsTable->getCreditsForItem($itemID, $group));
    }

    /**
     * Get a list of people associated with a particular series (not just
     * credits but also creators).
     *
     * @param int $seriesID Series ID
     *
     * @return array
     */
    public function getPeopleForSeries(int $seriesID): array
    {
        return iterator_to_array($this->editionsCreditsTable->getPeopleForSeries($seriesID));
    }

    /**
     * Get an entity by edition, person and role.
     *
     * @param int|EditionEntityInterface $edition Edition entity or ID
     * @param int|PersonEntityInterface  $person  Person entity or ID
     * @param int|RoleEntityInterface    $role    Role entity or ID
     *
     * @return ?EditionsCreditEntityInterface
     */
    public function getByEditionAndPersonAndRole(
        int|EditionEntityInterface $edition,
        int|PersonEntityInterface $person,
        int|RoleEntityInterface $role
    ): ?EditionsCreditEntityInterface {
        $where = [
            'Edition_ID' => $edition instanceof EditionEntityInterface ? $edition->getId() : $edition,
            'Person_ID' => $person instanceof PersonEntityInterface ? $person->getId() : $person,
            'Role_ID' => $role instanceof RoleEntityInterface ? $role->getId() : $role,
        ];
        foreach ($this->editionsCreditsTable->select($where) as $row) {
            return $row;
        }
        return null;
    }
}
