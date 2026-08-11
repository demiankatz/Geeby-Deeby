<?php

/**
 * Database service for the Files table.
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

/**
 * Database service for the Files table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class FileService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return FileEntityInterface
     */
    public function createEntity(): FileEntityInterface
    {
        $file = new File();
        $file->setEntityManager($this->entityManager);
        return $file;
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?FileEntityInterface
     */
    public function getByPrimaryKey(int $id): ?FileEntityInterface
    {
        return $this->entityManager->find(File::class, $id);
    }

    /**
     * Validate a populated entity -- return error message if problem found, null otherwise.
     *
     * @param FileEntityInterface $entity Entity to validate
     *
     * @return ?string
     */
    public function getValidationError(FileEntityInterface $entity): ?string
    {
        if (!$entity->getFileName()) {
            return 'Name cannot be blank.';
        }
        $path = $entity->getFilePath();
        return empty($path) ? 'Path cannot be blank.' : null;
    }

    /**
     * Get a list of files.
     *
     * @return FileEntityInterface[]
     */
    public function getList(): array
    {
        $dql = 'SELECT f FROM ' . File::class . ' f ORDER BY f.fileName';
        $query = $this->entityManager->createQuery($dql);
        return $query->getResult();
    }

    /**
     * Get a list of files grouped by type.
     *
     * @param ?array $include Array of file type IDs to retrieve (null to retrieve
     * all except those in $exclude)
     * @param ?array $exclude Array of file type IDs to exclude from results (null
     * to retrieve everything in $include, or everything if $include is null)
     *
     * @return array
     */
    public function getFilesByType(?array $include = null, ?array $exclude = null): array
    {
        $where = $params = [];
        if ($include) {
            $where[] = 'f.fileType IN (:include)';
            $params['include'] = $include;
        }
        if ($exclude) {
            $where[] = 'f.fileType NOT IN (:exclude)';
            $params['exclude'] = $exclude;
        }
        $dql = 'SELECT f.id AS File_ID, f.fileName AS File_Name, f.path AS File_Path, f.description AS Description, '
            . 't.id AS File_Type_ID, t.fileTypeName AS File_Type '
            . 'FROM ' . File::class . ' f INNER JOIN ' . FileType::class . ' t ON f.fileType=t.id'
            . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
            . ' ORDER BY t.fileTypeName, f.fileName';
        $query = $this->entityManager->createQuery($dql);
        if ($params) {
            $query->setParameters($params);
        }
        return $query->getResult();
    }
}
