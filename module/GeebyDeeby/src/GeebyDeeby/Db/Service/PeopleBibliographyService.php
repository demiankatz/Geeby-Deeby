<?php

/**
 * Database service for the People_Bibliography table.
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

use GeebyDeeby\Db\Entity\Item;
use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\MaterialType;
use GeebyDeeby\Db\Entity\PeopleBibliography;
use GeebyDeeby\Db\Entity\PeopleBibliographyEntityInterface;
use GeebyDeeby\Db\Entity\Person;
use GeebyDeeby\Db\Entity\PersonEntityInterface;

/**
 * Database service for the People_Bibliography table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PeopleBibliographyService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return PeopleBibliographyEntityInterface
     */
    public function createEntity(): PeopleBibliographyEntityInterface
    {
        $entity = new PeopleBibliography();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get a list of items describing the specified person.
     *
     * @param int $personID Person ID
     *
     * @return array
     */
    public function getItemsDescribingPerson(int $personID): array
    {
        $dql = 'SELECT i.id AS Item_ID, i.itemName AS Item_Name, mt.id AS Material_Type_ID, '
            . 'mt.singularName AS Material_Type_Name, mt.pluralName AS Material_Type_Plural_Name '
            . 'FROM ' . PeopleBibliography::class . ' b '
            . 'INNER JOIN ' . Item::class . ' i ON b.item=i.id '
            . 'INNER JOIN ' . MaterialType::class . ' mt ON i.materialType=mt.id '
            . 'WHERE b.person = :person ORDER BY mt.singularName, i.itemName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('person', $personID);
        return $query->getResult();
    }

    /**
     * Get a list of people described by the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return array
     */
    public function getPeopleDescribedByItem(int $itemID): array
    {
        $dql = 'SELECT p.id AS Person_ID, p.firstName AS First_Name, p.lastName AS Last_Name, '
            . 'p.extraDetails AS Extra_Details '
            . 'FROM ' . PeopleBibliography::class . ' b '
            . 'INNER JOIN ' . Person::class . ' p ON b.person=p.id '
            . 'WHERE b.item = :item ORDER BY p.lastName, p.firstName, p.extraDetails';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('item', $itemID);
        return $query->getResult();
    }

    /**
     * Retrieve the entity for a particular bibliography entry.
     *
     * @param int|ItemEntityInterface   $item   Item ID or entity
     * @param int|PersonEntityInterface $person Person ID or entity
     *
     * @return ?PeopleBibliographyEntityInterface
     */
    public function getByItemAndPerson(
        int|ItemEntityInterface $item,
        int|PersonEntityInterface $person
    ): ?PeopleBibliographyEntityInterface {
        $params = [
            'person' => $person instanceof PersonEntityInterface ? $person->getId() : $person,
            'item' => $item instanceof ItemEntityInterface ? $item->getId() : $item,
        ];
        $dql = 'SELECT b FROM ' . PeopleBibliography::class . ' b WHERE b.item = :item AND b.person = :person';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
