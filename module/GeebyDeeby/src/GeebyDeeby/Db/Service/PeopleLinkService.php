<?php

/**
 * Database service for the People_Links table.
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
use GeebyDeeby\Db\Entity\LinkEntityInterface;
use GeebyDeeby\Db\Entity\PeopleLinkEntityInterface;
use GeebyDeeby\Db\Entity\PersonEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\PeopleLinks;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the People_Links table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PeopleLinkService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param EntityManager      $entityManager      Entity manager
     * @param PersistenceManager $persistenceManager Persistence manager
     * @param PeopleLinks        $peopleLinksTable   PeopleLinks table
     */
    public function __construct(
        EntityManager $entityManager,
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected PeopleLinks $peopleLinksTable
    ) {
        parent::__construct($entityManager, $persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return PeopleLinkEntityInterface
     */
    public function createEntity(): PeopleLinkEntityInterface
    {
        return $this->peopleLinksTable->createRow();
    }

    /**
     * Get a list of people for the specified link.
     *
     * @param int $linkID Link ID
     *
     * @return array
     */
    public function getPeopleForLink(int $linkID): array
    {
        return iterator_to_array($this->peopleLinksTable->getPeopleForLink($linkID));
    }

    /**
     * Get a list of links for the specified person.
     *
     * @param int $personID Person ID
     *
     * @return array
     */
    public function getLinksForPerson(int $personID): array
    {
        return iterator_to_array($this->peopleLinksTable->getLinksForPerson($personID));
    }

    /**
     * Retrieve the record for the specified link and person.
     *
     * @param int|LinkEntityInterface   $link   Link ID or entity
     * @param int|PersonEntityInterface $person Person ID or entity
     *
     * @return ?PeopleLinkEntityInterface
     */
    public function getForLinkAndPerson(
        int|LinkEntityInterface $link,
        int|PersonEntityInterface $person
    ): ?PeopleLinkEntityInterface {
        $where = [
            'Link_ID' => $link instanceof LinkEntityInterface ? $link->getId() : $link,
            'Person_ID' => $person instanceof PersonEntityInterface ? $person->getId() : $person,
        ];
        foreach ($this->peopleLinksTable->select($where) as $row) {
            return $row;
        }
        return null;
    }
}
