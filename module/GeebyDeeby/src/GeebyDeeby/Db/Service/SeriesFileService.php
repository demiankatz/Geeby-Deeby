<?php

/**
 * Database service for the Series_Files table.
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
use GeebyDeeby\Db\Entity\FileEntityInterface;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\Entity\SeriesFileEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\SeriesFiles;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Series_Files table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesFileService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param EntityManager      $entityManager      Entity manager
     * @param PersistenceManager $persistenceManager Persistence manager
     * @param SeriesFiles        $seriesFilesTable   SeriesFiles table
     */
    public function __construct(
        EntityManager $entityManager,
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected SeriesFiles $seriesFilesTable
    ) {
        parent::__construct($entityManager, $persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return SeriesFileEntityInterface
     */
    public function createEntity(): SeriesFileEntityInterface
    {
        return $this->seriesFilesTable->createRow();
    }

    /**
     * Get a list of series for the specified file.
     *
     * @param int $fileID File ID
     *
     * @return array
     */
    public function getSeriesForFile(int $fileID): array
    {
        return iterator_to_array($this->seriesFilesTable->getSeriesForFile($fileID));
    }

    /**
     * Get a list of files for the specified series.
     *
     * @param int $seriesID Series ID
     *
     * @return array
     */
    public function getFilesForSeries(int $seriesID): array
    {
        return iterator_to_array($this->seriesFilesTable->getFilesForSeries($seriesID));
    }

    /**
     * Retrieve the record for the specified file and series.
     *
     * @param int|FileEntityInterface   $file   File ID or entity
     * @param int|SeriesEntityInterface $series Series ID or entity
     *
     * @return ?SeriesFileEntityInterface
     */
    public function getForFileAndSeries(
        int|FileEntityInterface $file,
        int|SeriesEntityInterface $series
    ): ?SeriesFileEntityInterface {
        $where = [
            'File_ID' => $file instanceof FileEntityInterface ? $file->getId() : $file,
            'Series_ID' => $series instanceof SeriesEntityInterface ? $series->getId() : $series,
        ];
        foreach ($this->seriesFilesTable->select($where) as $row) {
            return $row;
        }
        return null;
    }
}
