<?php

/**
 * Table Definition for Editions_Platforms
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
 * Table Definition for Editions_Platforms
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsPlatforms extends Gateway
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
        parent::__construct($adapter, $tm, $rowObj, 'Editions_Platforms');
    }

    /**
     * Get editions for the specified platform.
     *
     * @param int $platformID Platform ID
     *
     * @return mixed
     */
    public function getItemsForPlatform($platformID)
    {
        $callback = function ($select) use ($platformID) {
            $select->join(
                ['eds' => 'Editions'],
                'Editions_Platforms.Edition_ID = eds.Edition_ID',
                ['Volume', 'Position', 'Replacement_Number']
            );
            $select->join(
                ['i' => 'Items'],
                'eds.Item_ID = i.Item_ID'
            );
            $select->join(
                ['s' => 'Series'],
                'eds.Series_ID = s.Series_ID'
            );
            $select->group(
                [
                    'i.Item_ID', 's.Series_ID', 'eds.Volume', 'eds.Position',
                    'eds.Replacement_Number',
                ]
            );
            $select->order(
                [
                    'Series_Name', 's.Series_ID', 'eds.Volume', 'eds.Position',
                    'eds.Replacement_Number', 'Item_Name',
                ]
            );
            $select->where->equalTo('Platform_ID', $platformID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of platforms for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return mixed
     */
    public function getPlatformsForItem($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->join(
                ['p' => 'Platforms'],
                'Editions_Platforms.Platform_ID = p.Platform_ID'
            );
            $select->join(
                ['eds' => 'Editions'],
                'Editions_Platforms.Edition_ID = eds.Edition_ID'
            );
            $select->order(['Platform']);
            $select->where->equalTo('Item_ID', $itemID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of platforms for the specified edition.
     *
     * @param int $editionID Edition ID
     *
     * @return mixed
     */
    public function getPlatformsForEdition($editionID)
    {
        $callback = function ($select) use ($editionID) {
            $select->join(
                ['p' => 'Platforms'],
                'Editions_Platforms.Platform_ID = p.Platform_ID'
            );
            $select->order(['Platform']);
            $select->where->equalTo('Edition_ID', $editionID);
        };
        return $this->select($callback);
    }
}
