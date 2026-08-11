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

use GeebyDeeby\Db\Entity\Link;
use GeebyDeeby\Db\Entity\LinkEntityInterface;
use GeebyDeeby\Db\Entity\PeopleLink;
use GeebyDeeby\Db\Entity\PeopleLinkEntityInterface;
use GeebyDeeby\Db\Entity\Person;
use GeebyDeeby\Db\Entity\PersonEntityInterface;

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
     * Create an empty entity.
     *
     * @return PeopleLinkEntityInterface
     */
    public function createEntity(): PeopleLinkEntityInterface
    {
        $entity = new PeopleLink();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get a list of people for the specified link.
     *
     * @param int $linkID Link ID
     *
     * @return PersonEntityInterface[]
     */
    public function getPeopleForLink(int $linkID): array
    {
        $dql = 'SELECT p FROM ' . PeopleLink::class . ' pl '
            . 'INNER JOIN ' . Person::class . ' p ON pl.person=p.id '
            . 'WHERE pl.link = :link ORDER BY p.lastName, p.firstName, p.extraDetails';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('link', $linkID);
        return $query->getResult();
    }

    /**
     * Get a list of links for the specified person.
     *
     * @param int $personID Person ID
     *
     * @return LinkEntityInterface[]
     */
    public function getLinksForPerson(int $personID): array
    {
        $dql = 'SELECT l FROM ' . PeopleLink::class . ' pl '
            . 'INNER JOIN ' . Link::class . ' l ON pl.link=l.id '
            . 'WHERE pl.person = :person ORDER BY l.linkName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('person', $personID);
        return $query->getResult();
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
        $params = [
            'link' => $link instanceof LinkEntityInterface ? $link->getId() : $link,
            'person' => $person instanceof PersonEntityInterface ? $person->getId() : $person,
        ];
        $dql = 'SELECT pl FROM ' . PeopleLink::class . ' pl WHERE pl.person = :person AND pl.link = :link';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
