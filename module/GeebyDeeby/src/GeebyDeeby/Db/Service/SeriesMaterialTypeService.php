<?php

/**
 * Database service for the Series_Material_Types table.
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

use GeebyDeeby\Db\Entity\MaterialTypeEntityInterface;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\Entity\SeriesMaterialTypeEntityInterface;
use GeebyDeeby\Db\Table\SeriesMaterialTypes;
use GeebyDeeby\ServiceManager\Factory\Autowire;

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
     * Constructor
     *
     * @param SeriesMaterialTypes $seriesMaterialTypesTable SeriesMaterialTypes table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected SeriesMaterialTypes $seriesMaterialTypesTable
    ) {
    }

    /**
     * Create an empty entity.
     *
     * @return SeriesMaterialTypeEntityInterface
     */
    public function createEntity(): SeriesMaterialTypeEntityInterface
    {
        return $this->seriesMaterialTypesTable->createRow();
    }

    /**
     * Get a list of series for the specified material type.
     *
     * @param int $materialTypeID MaterialType ID
     *
     * @return array
     */
    public function getSeriesForMaterialType(int $materialTypeID): array
    {
        return iterator_to_array($this->seriesMaterialTypesTable->getSeriesForMaterialType($materialTypeID));
    }

    /**
     * Get a list of material types for the specified series.
     *
     * @param ?int $seriesID Series ID (null for all series)
     *
     * @return array
     */
    public function getMaterialTypesForSeries(?int $seriesID = null): array
    {
        return iterator_to_array($this->seriesMaterialTypesTable->getMaterials($seriesID));
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
        $where = [
            'Series_ID' => $series instanceof SeriesEntityInterface ? $series->getId() : $series,
            'Material_Type_ID' => $materialType instanceof MaterialTypeEntityInterface
                ? $materialType->getId() : $materialType,
        ];
        foreach ($this->seriesMaterialTypesTable->select($where) as $row) {
            return $row;
        }
        return null;
    }
}
