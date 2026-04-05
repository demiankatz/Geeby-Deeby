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

use GeebyDeeby\Db\Entity\File;
use GeebyDeeby\Db\Entity\FileEntityInterface;
use GeebyDeeby\Db\Entity\FileType;
use GeebyDeeby\Db\Entity\Series;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\Entity\SeriesFile;
use GeebyDeeby\Db\Entity\SeriesFileEntityInterface;

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
     * Create an empty entity.
     *
     * @return SeriesFileEntityInterface
     */
    public function createEntity(): SeriesFileEntityInterface
    {
        $entity = new SeriesFile();
        $entity->setEntityManager($this->entityManager);
        return $entity;
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
        $dql = 'SELECT s FROM ' . SeriesFile::class . ' sf INNER JOIN ' . Series::class . ' s ON sf.series=s.id '
            . 'WHERE sf.file = :file ORDER BY s.seriesName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('file', $fileID);
        return $query->getResult();
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
        $dql = 'SELECT ft.id AS File_Type_ID, ft.fileTypeName AS File_Type, '
            . 'f.id AS File_ID, f.fileName AS File_Name, f.path AS File_Path, f.description AS Description '
            . 'FROM ' . SeriesFile::class . ' sf INNER JOIN ' . File::class . ' f ON sf.file=f.id '
            . 'INNER JOIN ' . FileType::class . ' ft ON f.fileType=ft.id '
            . 'WHERE sf.series = :series ORDER BY ft.fileTypeName, f.fileName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('series', $seriesID);
        return $query->getResult();
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
        $params = [
            'file' => $file instanceof FileEntityInterface ? $file->getId() : $file,
            'series' => $series instanceof SeriesEntityInterface ? $series->getId() : $series,
        ];
        $dql = 'SELECT sf FROM ' . SeriesFile::class . ' sf WHERE sf.series = :series AND sf.file = :file';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
