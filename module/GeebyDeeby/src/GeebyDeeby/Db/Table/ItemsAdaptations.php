<?php

/**
 * Table Definition for Items_Adaptations
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
 * Table Definition for Items_Adaptations
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsAdaptations extends Gateway
{
    /**
     * Constructor
     *
     * @param Adapter       $adapter Database adapter
     * @param PluginManager $tm      Table manager
     * @param RowGateway    $rowObj  Row prototype object (null for default)
     */
    public function __construct(
        Adapter $adapter,
        PluginManager $tm,
        RowGateway $rowObj = null
    ) {
        parent::__construct($adapter, $tm, $rowObj, 'Items_Adaptations');
    }

    /**
     * Get a list of items adapted from the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return mixed
     */
    public function getAdaptedFrom($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->join(
                ['i' => 'Items'],
                'Items_Adaptations.Adapted_Item_ID = i.Item_ID'
            );
            $select->join(
                ['mt' => 'Material_Types'],
                'i.Material_Type_ID = mt.Material_Type_ID'
            );
            $select->where->equalTo('Source_Item_ID', $itemID);
            $select->order('i.Item_Name');
        };
        return $this->select($callback);
    }

    /**
     * Get a list of items adapted into the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return mixed
     */
    public function getAdaptedInto($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->join(
                ['i' => 'Items'],
                'Items_Adaptations.Source_Item_ID = i.Item_ID'
            );
            $select->join(
                ['mt' => 'Material_Types'],
                'i.Material_Type_ID = mt.Material_Type_ID'
            );
            $select->where->equalTo('Adapted_Item_ID', $itemID);
            $select->order('i.Item_Name');
        };
        return $this->select($callback);
    }
}
