<?php
/**
 * Table Definition for Editions
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\Db\Table;

/**
 * Table Definition for Editions
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class Edition extends Gateway
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('Editions', 'GeebyDeeby\Db\Row\Edition');
    }

    /**
     * Get a list of editions.
     *
     * @return mixed
     */
    public function getList()
    {
        $callback = function ($select) {
            $select->order('Edition_Name');
        };
        return $this->select($callback);
    }

    /**
     * Get autocomplete suggestions.
     *
     * @param string $query The user query.
     * @param mixed  $limit Limit on returned rows (false for no limit).
     *
     * @return mixed
     */
    public function getSuggestions($query, $limit = false)
    {
        $callback = function ($select) use ($query, $limit) {
            if ($limit !== false) {
                $select->limit($limit);
            }
            $select->where->like('Edition_Name', $query . '%');
            $select->order('Edition_Name');
        };
        return $this->select($callback);
    }

    /**
     * Perform a keyword search.
     *
     * @param array $tokens Keywords.
     *
     * @return mixed
     */
    public function keywordSearch($tokens)
    {
        $callback = function ($select) use ($tokens) {
            foreach ($tokens as $token) {
                $select->where->like('Edition_Name', '%' . $token . '%');
            }
            $select->order('Edition_Name');
        };
        return $this->select($callback);
    }

    /**
     * Get image information for the specified series.
     *
     * @var int $seriesID Series ID
     *
     * @return mixed
     */
    public function getImagesForSeries($seriesID)
    {
        $callback = function ($select) use ($seriesID) {
            $select->join(
                array('i' => 'Items'), 'Editions.Item_ID = i.Item_ID'
            );
            $select->join(
                array('mt' => 'Material_Types'),
                'i.Material_Type_ID = mt.Material_Type_ID'
            );
            $select->join(array('ii' => 'Items_Images'), 'i.item_ID = ii.item_ID');
            $select->join(
                array('n' => 'Notes'), 'ii.Note_ID = n.Note_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $select->order(
                array(
                    'mt.Material_Type_Name', 'Editions.Position',
                    'i.Item_Name', 'ii.Position'
                )
            );
            $select->where->equalTo('Series_ID', $seriesID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of items for the specified series.
     *
     * @var int $seriesID Series ID
     *
     * @return mixed
     */
    public function getItemsForSeries($seriesID)
    {
        $callback = function ($select) use ($seriesID) {
            $select->join(
                array('i' => 'Items'), 'Editions.Item_ID = i.Item_ID'
            );
            $select->join(
                array('mt' => 'Material_Types'),
                'i.Material_Type_ID = mt.Material_Type_ID'
            );
            $select->order(
                array('mt.Material_Type_Name', 'Position', 'i.Item_Name')
            );
            $select->where->equalTo('Series_ID', $seriesID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of series for the specified item.
     *
     * @var int $itemID Item ID
     *
     * @return mixed
     */
    public function getSeriesForItem($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->join(
                array('s' => 'Series'), 'Editions.Series_ID = s.Series_ID'
            );
            $select->order(
                array('s.Series_Name', 's.Series_ID', 'Position')
            );
            $select->where->equalTo('Item_ID', $itemID);
        };
        return $this->select($callback);
    }
}
