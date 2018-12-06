<?php
/**
 * Table Definition for Series_Bibliography
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

/**
 * Table Definition for Series_Bibliography
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesBibliography extends Gateway
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('Series_Bibliography');
    }

    /**
     * Get a list of items describing the specified series.
     *
     * @var int $seriesID Series ID
     *
     * @return mixed
     */
    public function getItemsDescribingSeries($seriesID)
    {
        $callback = function ($select) use ($seriesID) {
            $select->join(
                array('i' => 'Items'),
                'Series_Bibliography.Item_ID = i.Item_ID'
            );
            $select->join(
                array('mt' => 'Material_Types'),
                'i.Material_Type_ID = mt.Material_Type_ID'
            );
            $select->order(array('Material_Type_Name', 'Item_Name'));
            $select->where->equalTo('Series_ID', $seriesID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of series described by the specified item.
     *
     * @var int $itemID Item ID
     *
     * @return mixed
     */
    public function getSeriesDescribedByItem($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->join(
                array('s' => 'Series'),
                'Series_Bibliography.Series_ID = s.Series_ID'
            );
            $select->order('s.Series_Name');
            $select->where->equalTo('Item_ID', $itemID);
        };
        return $this->select($callback);
    }
}
