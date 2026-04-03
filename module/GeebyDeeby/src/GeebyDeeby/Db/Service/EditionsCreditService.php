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
use GeebyDeeby\Db\Entity\Edition;
use GeebyDeeby\Db\Entity\EditionEntityInterface;
use GeebyDeeby\Db\Entity\EditionsCredit;
use GeebyDeeby\Db\Entity\EditionsCreditEntityInterface;
use GeebyDeeby\Db\Entity\EditionsReleaseDate;
use GeebyDeeby\Db\Entity\Item;
use GeebyDeeby\Db\Entity\ItemsAltTitle;
use GeebyDeeby\Db\Entity\ItemsCreator;
use GeebyDeeby\Db\Entity\Note;
use GeebyDeeby\Db\Entity\Person;
use GeebyDeeby\Db\Entity\PersonEntityInterface;
use GeebyDeeby\Db\Entity\Role;
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
        $omitYear = ($sort !== 'year' && !$includeYear);
        $extraSelect = $omitYear ? '' : 'MIN(erd.year) AS Earliest_Year, ';
        $yearJoin = $omitYear
            ? ''
            : (' LEFT JOIN ' . EditionsReleaseDate::class . ' erd ON e.id=erd.edition OR e.parentEdition=erd.edition ');
        if ($sort === 'year') {
            $sortFields = 'r.roleName, Earliest_Year, i.itemName';
        } else {
            $sortFields = 'r.roleName, i.itemName' . ($omitYear ? '' : ', Earliest_Year');
        }
        $dql = "SELECT {$extraSelect}COUNT(e.id) AS Edition_Count, i.itemName AS Item_Name, i.id AS Item_ID, "
            . 'r.id AS Role_ID, r.roleName AS Role_Name, r.itemCreatorPredicate AS Item_Creator_Predicate '
            . 'FROM ' . Edition::class . ' e '
            . 'INNER JOIN ' . EditionsCredit::class . ' ec ON ec.edition=e.id '
            . 'INNER JOIN ' . Item::class . ' i ON e.item=i.id ' . $yearJoin
            . 'INNER JOIN ' . Role::class . ' r ON ec.role=r.id '
            . 'WHERE ec.person = :person '
            . 'GROUP BY r.roleName, i.itemName '
            . 'ORDER BY ' . $sortFields;
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('person', $personID);
        return $query->getResult();
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
        $dql = 'SELECT i.id AS Item_ID, e.editionName AS Edition_Name, '
            . 'r.id AS Role_ID, r.roleName AS Role_Name, r.itemCreatorPredicate AS Item_Creator_Predicate, '
            . 'n.id AS Note_ID, n.note AS Note, '
            . 'p.id AS Person_ID, p.firstName AS First_Name, p.lastName AS Last_Name, p.extraDetails AS Extra_Details '
            . 'FROM ' . Edition::class . ' e '
            . 'INNER JOIN ' . Item::class . ' i ON e.item=i.id '
            . 'INNER JOIN ' . EditionsCredit::class . ' ec ON ec.edition=e.id '
            . 'INNER JOIN ' . Role::class . ' r ON ec.role=r.id '
            . 'LEFT JOIN ' . Note::class . ' n ON ec.note=n.id '
            . 'INNER JOIN ' . Person::class . ' p ON ec.person=p.id '
            . 'WHERE i.id = :item '
            . ($group ? 'GROUP BY r.id, p.id, n.id ' : '')
            . 'ORDER BY r.roleName, ec.position, p.lastName, p.firstName, p.extraDetails';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('item', $itemID);
        return $query->getResult();
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
        $dql = 'SELECT DISTINCT i.itemName AS Item_Name, i.id AS Item_ID, iat.altName AS Item_AltName, '
            . 'COALESCE(iat.altName, i.itemName) AS Best_Title, '
            . 'p.id AS Person_ID, p.firstName AS First_Name, p.lastName AS Last_Name, p.extraDetails AS Extra_Details '
            . 'FROM ' . Edition::class . ' e '
            . 'INNER JOIN ' . Item::class . ' i ON e.item=i.id '
            . 'LEFT JOIN ' . ItemsAltTitle::class . ' iat ON e.preferredItemAltName=iat.id '
            . 'LEFT JOIN ' . EditionsCredit::class . ' ec ON e.id=ec.edition '
            . 'LEFT JOIN ' . ItemsCreator::class . ' ic ON e.item = ic.item '
            . 'INNER JOIN ' . Person::class . ' p ON ec.person=p.id OR ic.person=p.id '
            . 'WHERE e.series = :series '
            . 'ORDER BY p.lastName, p.firstName, p.extraDetails, Best_Title';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('series', $seriesID);
        return $query->getResult();
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
        $params = [
            'edition' => $edition instanceof EditionEntityInterface ? $edition->getId() : $edition,
            'person' => $person instanceof PersonEntityInterface ? $person->getId() : $person,
            'role' => $role instanceof RoleEntityInterface ? $role->getId() : $role,
        ];
        $dql = 'SELECT c FROM ' . EditionsCredit::class
            . ' c WHERE c.edition = :edition AND c.person = :person AND c.role = :role';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
