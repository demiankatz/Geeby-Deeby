<?php

/**
 * Database service for the People_Files table.
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

use GeebyDeeby\Db\Entity\File;
use GeebyDeeby\Db\Entity\FileEntityInterface;
use GeebyDeeby\Db\Entity\FileType;
use GeebyDeeby\Db\Entity\PeopleFile;
use GeebyDeeby\Db\Entity\PeopleFileEntityInterface;
use GeebyDeeby\Db\Entity\Person;
use GeebyDeeby\Db\Entity\PersonEntityInterface;

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
     * Create an empty entity.
     *
     * @return PeopleFileEntityInterface
     */
    public function createEntity(): PeopleFileEntityInterface
    {
        $entity = new PeopleFile();
        $entity->setEntityManager($this->entityManager);
        return $entity;
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
        $dql = 'SELECT p FROM ' . PeopleFile::class . ' pf INNER JOIN ' . Person::class . ' p ON pf.person=p.id '
            . 'WHERE pf.file = :file ORDER BY p.lastName, p.firstName, p.extraDetails';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('file', $fileID);
        return $query->getResult();
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
        $dql = 'SELECT ft.id AS File_Type_ID, ft.fileTypeName AS File_Type, '
            . 'f.id AS File_ID, f.fileName AS File_Name, f.path AS File_Path, f.description AS Description '
            . 'FROM ' . PeopleFile::class . ' pf INNER JOIN ' . File::class . ' f ON pf.file=f.id '
            . 'INNER JOIN ' . FileType::class . ' ft ON f.fileType=ft.id '
            . 'WHERE pf.person = :person ORDER BY ft.fileTypeName, f.fileName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('person', $personID);
        return $query->getResult();
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
        $params = [
            'file' => $file instanceof FileEntityInterface ? $file->getId() : $file,
            'person' => $person instanceof PersonEntityInterface ? $person->getId() : $person,
        ];
        $dql = 'SELECT pf FROM ' . PeopleFile::class . ' pf WHERE pf.person = :person AND pf.file = :file';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
