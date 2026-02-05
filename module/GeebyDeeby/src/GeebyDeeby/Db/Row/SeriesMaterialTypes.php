<?php

/**
 * Row Definition for Series_Material_Types
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
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Db\Row;

use GeebyDeeby\Db\Entity\MaterialTypeEntityInterface;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\Entity\SeriesMaterialTypeEntityInterface;

/**
 * Row Definition for Series_Material_Types
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesMaterialTypes extends TableAwareGateway implements SeriesMaterialTypeEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct(['Series_ID', 'Material_Type_ID'], 'Series_Material_Types', $adapter);
    }

    /**
     * Get associated series.
     *
     * @return SeriesEntityInterface
     */
    public function getSeries(): SeriesEntityInterface
    {
        return $this->Series_ID ? $this->getTableManager()->get('series')->getByPrimaryKey($this->Series_ID) : null;
    }

    /**
     * Set associated series.
     *
     * @param int|SeriesEntityInterface $series Associated series entity or ID
     *
     * @return static
     */
    public function setSeries(int|SeriesEntityInterface $series): static
    {
        $this->Series_ID = $series instanceof SeriesEntityInterface ? $series->getId() : $series;
        return $this;
    }

    /**
     * Get associated material type.
     *
     * @return MaterialTypeEntityInterface
     */
    public function getMaterialType(): MaterialTypeEntityInterface
    {
        return $this->getTableManager()->get('materialtype')->getByPrimaryKey($this->Material_Type_ID);
    }

    /**
     * Set associated material type.
     *
     * @param int|MaterialTypeEntityInterface $materialType Associated language entity or ID
     *
     * @return static
     */
    public function setMaterialType(int|MaterialTypeEntityInterface $materialType): static
    {
        $this->Material_Type_ID = $materialType instanceof MaterialTypeEntityInterface
            ? $materialType->getId() : $materialType;
        return $this;
    }
}
