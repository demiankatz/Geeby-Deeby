<?php

/**
 * Database service for the Series_Material_Types table.
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

use GeebyDeeby\Db\Entity\MaterialType;
use GeebyDeeby\Db\Entity\MaterialTypeEntityInterface;
use GeebyDeeby\Db\Entity\Series;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\Entity\SeriesMaterialType;
use GeebyDeeby\Db\Entity\SeriesMaterialTypeEntityInterface;

/**
 * Database service for the Series_Material_Types table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesMaterialTypeService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return SeriesMaterialTypeEntityInterface
     */
    public function createEntity(): SeriesMaterialTypeEntityInterface
    {
        $entity = new SeriesMaterialType();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get a list of series for the specified material type.
     *
     * @param int $materialTypeID MaterialType ID
     *
     * @return SeriesEntityInterface[]
     */
    public function getSeriesForMaterialType(int $materialTypeID): array
    {
        $dql = 'SELECT DISTINCT s FROM ' . Series::class . ' s INNER JOIN '
            . SeriesMaterialType::class . ' sm ON s.id=sm.series '
            . 'WHERE sm.materialType=:type ORDER BY s.seriesName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('type', $materialTypeID);
        return $query->getResult();
    }

    /**
     * Get a list of material types for the specified series.
     *
     * @param ?int $seriesID Series ID (null for all series)
     *
     * @return MaterialTypeEntityInterface[]
     */
    public function getMaterialTypesForSeries(?int $seriesID = null): array
    {
        $dql = 'SELECT DISTINCT m FROM ' . MaterialType::class . ' m INNER JOIN '
            . SeriesMaterialType::class . ' sm ON m.id=sm.materialType '
            . ($seriesID ? 'WHERE sm.series=:series' : '')
            . ' ORDER BY m.singularName';
        $query = $this->entityManager->createQuery($dql);
        if ($seriesID) {
            $query->setParameter('series', $seriesID);
        }
        return $query->getResult();
    }

    /**
     * Retrieve the record for the specified material type and series.
     *
     * @param int|SeriesEntityInterface       $series       Series ID or entity
     * @param int|MaterialTypeEntityInterface $materialType MaterialType ID or entity
     *
     * @return ?SeriesMaterialTypeEntityInterface
     */
    public function getBySeriesAndMaterialType(
        int|SeriesEntityInterface $series,
        int|MaterialTypeEntityInterface $materialType
    ): ?SeriesMaterialTypeEntityInterface {
        $dql = 'SELECT m FROM ' . SeriesMaterialType::class . ' m WHERE m.materialType = :type AND m.series = :series';
        $query = $this->entityManager->createQuery($dql);
        $query->setMaxResults(1);
        $query->setParameters(['type' => $materialType, 'series' => $series]);
        return $query->getOneOrNullResult();
    }
}
