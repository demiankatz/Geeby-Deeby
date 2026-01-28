<?php

/**
 * Database service for the People_Files table.
 *
 * PHP version 8
 *
 * Copyright (C) Villanova University 2026.
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

use GeebyDeeby\Db\Entity\FileEntityInterface;
use GeebyDeeby\Db\Entity\PeopleFileEntityInterface;
use GeebyDeeby\Db\Entity\PersonEntityInterface;
use GeebyDeeby\Db\Table\PeopleFiles;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the People_Files table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PeopleFileService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param PeopleFiles $peopleFilesTable PeopleFiles table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected PeopleFiles $peopleFilesTable
    ) {
    }

    /**
     * Create an empty entity.
     *
     * @return PeopleFileEntityInterface
     */
    public function createEntity(): PeopleFileEntityInterface
    {
        return $this->peopleFilesTable->createRow();
    }

    /**
     * Get a list of people for the specified file.
     *
     * @param int $fileID File ID
     *
     * @return array
     */
    public function getPeopleForFile(int $fileID): array
    {
        return iterator_to_array($this->peopleFilesTable->getPeopleForFile($fileID));
    }

    /**
     * Get a list of files for the specified person.
     *
     * @param int $personID Person ID
     *
     * @return array
     */
    public function getFilesForPerson(int $personID): array
    {
        return iterator_to_array($this->peopleFilesTable->getFilesForPerson($personID));
    }

    /**
     * Retrieve the record for the specified file and person.
     *
     * @param int|FileEntityInterface   $file   File ID or entity
     * @param int|PersonEntityInterface $person Person ID or entity
     *
     * @return ?PeopleFileEntityInterface
     */
    public function getForFileAndPerson(
        int|FileEntityInterface $file,
        int|PersonEntityInterface $person
    ): ?PeopleFileEntityInterface {
        $where = [
            'File_ID' => $file instanceof FileEntityInterface ? $file->getId() : $file,
            'Person_ID' => $person instanceof PersonEntityInterface ? $person->getId() : $person,
        ];
        foreach ($this->peopleFilesTable->select($where) as $row) {
            return $row;
        }
        return null;
    }
}
