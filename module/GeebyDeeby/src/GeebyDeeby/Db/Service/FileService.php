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

use GeebyDeeby\Db\Entity\FileEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\File;
use GeebyDeeby\ServiceManager\Factory\Autowire;

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
     * Constructor
     *
     * @param PersistenceManager $persistenceManager Persistence manager
     * @param File               $fileTable          File table
     */
    public function __construct(
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected File $fileTable
    ) {
        parent::__construct($persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return FileEntityInterface
     */
    public function createEntity(): FileEntityInterface
    {
        return $this->fileTable->createRow();
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
        return $this->fileTable->getByPrimaryKey($id);
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
        return iterator_to_array($this->fileTable->getList());
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
        return iterator_to_array($this->fileTable->getFilesByType($include, $exclude));
    }
}
