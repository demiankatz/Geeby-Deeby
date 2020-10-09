<?php
/**
 * Table Definition for Series_Material_Types
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2012.
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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\Db\Table;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\RowGateway\RowGateway;

/**
 * Table Definition for Series_Material_Types
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesMaterialTypes extends Gateway
{
    /**
     * Constructor
     *
     * @param Adapter       $adapter Database adapter
     * @param PluginManager $tm      Table manager
     * @param RowGateway    $rowObj  Row prototype object (null for default)
     */
    public function __construct(Adapter $adapter, PluginManager $tm,
        RowGateway $rowObj = null
    ) {
        parent::__construct($adapter, $tm, $rowObj, 'Series_Material_Types');
    }

    /**
     * Get a list of series for the specified material type.
     *
     * @param int $typeID Material Type ID
     *
     * @return mixed
     */
    public function getSeriesForMaterialType($typeID)
    {
        $callback = function ($select) use ($typeID) {
            $select->join(
                ['s' => 'Series'],
                'Series_Material_Types.Series_ID = s.Series_ID'
            );
            $select->order('s.Series_Name');
            $select->where->equalTo('Material_Type_ID', $typeID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of material types for the specified series.
     *
     * @param int $seriesID Series ID (null for all series)
     *
     * @return mixed
     */
    public function getMaterials($seriesID = null)
    {
        $callback = function ($select) use ($seriesID) {
            $select->join(
                ['mt' => 'Material_Types'],
                'Series_Material_Types.Material_Type_ID = mt.Material_Type_ID'
            );
            $select->columns([]);
            $select->quantifier(\Laminas\Db\Sql\Select::QUANTIFIER_DISTINCT);
            $select->order('mt.Material_Type_Name');
            if (null !== $seriesID) {
                $select->where->equalTo('Series_ID', $seriesID);
            }
        };
        return $this->select($callback);
    }
}
