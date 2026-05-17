<?php

/**
 * Database service for the Pseudonyms table.
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

use GeebyDeeby\Db\Entity\Person;
use GeebyDeeby\Db\Entity\PersonEntityInterface;
use GeebyDeeby\Db\Entity\Pseudonym;
use GeebyDeeby\Db\Entity\PseudonymEntityInterface;

/**
 * Database service for the Pseudonyms table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PseudonymService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return PseudonymEntityInterface
     */
    public function createEntity(): PseudonymEntityInterface
    {
        $entity = new Pseudonym();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get a list of pseudonyms for a real name.
     *
     * @param int $personID Real person ID
     *
     * @return array
     */
    public function getPseudonyms(int $personID): array
    {
        $dql = 'SELECT p.id AS Pseudo_Person_ID, '
            . 'p.id AS Person_ID, p.firstName AS First_Name, p.lastName AS Last_Name, p.extraDetails AS Extra_Details '
            . 'FROM ' . Person::class . ' p INNER JOIN ' . Pseudonym::class . ' pseudo ON p.id=pseudo.pseudoPerson'
            . ' WHERE pseudo.realPerson=:real ORDER BY p.lastName, p.firstName, p.extraDetails, p.id';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('real', $personID);
        return $query->getResult();
    }

    /**
     * Get a list of real names for a pseudonym.
     *
     * @param int $personID Pseudonym ID
     *
     * @return array
     */
    public function getRealNames(int $personID): array
    {
        $dql = 'SELECT p.id AS Real_Person_ID, '
            . 'p.id AS Person_ID, p.firstName AS First_Name, p.lastName AS Last_Name, p.extraDetails AS Extra_Details '
            . 'FROM ' . Person::class . ' p INNER JOIN ' . Pseudonym::class . ' pseudo ON p.id=pseudo.realPerson'
            . ' WHERE pseudo.pseudoPerson=:pseudo ORDER BY p.lastName, p.firstName, p.extraDetails, p.id';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('pseudo', $personID);
        return $query->getResult();
    }

    /**
     * Get a batch of real name information keyed by ID.
     *
     * @param iterable<PersonEntityInterface> $people Collection of people to look up
     *
     * @return array
     */
    public function getRealNamesBatch(iterable $people): array
    {
        $retVal = [];
        foreach ($people as $person) {
            $id = $person->getId();
            if (!isset($retVal[$id])) {
                $retVal[$id] = $this->getRealNames($id);
            }
        }
        return $retVal;
    }

    /**
     * Retrieve a link using a real person and a pseudonym.
     *
     * @param int|PersonEntityInterface $real   ID or person entity for real person
     * @param int|PersonEntityInterface $pseudo ID or person entity for pseudonym
     *
     * @return ?PseudonymEntityInterface
     */
    public function getByRealPersonAndPseudonym(
        int|PersonEntityInterface $real,
        int|PersonEntityInterface $pseudo
    ): ?PseudonymEntityInterface {
        $params = [
            'real' => $real instanceof PersonEntityInterface ? $real->getId() : $real,
            'pseudo' => $pseudo instanceof PersonEntityInterface ? $pseudo->getId() : $pseudo,
        ];
        $dql = 'SELECT p FROM ' . Pseudonym::class . ' p WHERE p.realPerson=:real AND p.pseudoPerson=:pseudo';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
