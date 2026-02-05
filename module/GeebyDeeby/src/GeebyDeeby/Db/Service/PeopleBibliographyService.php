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

use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\PeopleBibliographyEntityInterface;
use GeebyDeeby\Db\Entity\PersonEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\PeopleBibliography;
use GeebyDeeby\ServiceManager\Factory\Autowire;

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
     * Constructor
     *
     * @param PersistenceManager $persistenceManager      Persistence manager
     * @param PeopleBibliography $peopleBibliographyTable PeopleBibliography table
     */
    public function __construct(
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected PeopleBibliography $peopleBibliographyTable
    ) {
        parent::__construct($persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return PeopleBibliographyEntityInterface
     */
    public function createEntity(): PeopleBibliographyEntityInterface
    {
        return $this->peopleBibliographyTable->createRow();
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
        return iterator_to_array($this->peopleBibliographyTable->getItemsDescribingPerson($personID));
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
        return iterator_to_array($this->peopleBibliographyTable->getPeopleDescribedByItem($itemID));
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
        $where = [
            'Person_ID' => $person instanceof PersonEntityInterface ? $person->getId() : $person,
            'Item_ID' => $item instanceof ItemEntityInterface ? $item->getId() : $item,
        ];
        foreach ($this->peopleBibliographyTable->select($where) as $row) {
            return $row;
        }
        return null;
    }
}
