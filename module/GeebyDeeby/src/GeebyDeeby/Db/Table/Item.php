<?php
/**
 * Table Definition for Items
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
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;

/**
 * Table Definition for Items
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class Item extends Gateway
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('Items', 'GeebyDeeby\Db\Row\Item');
    }

    /**
     * Get a list of items.
     *
     * @return mixed
     */
    public function getList()
    {
        $callback = function ($select) {
            $select->order('Item_Name');
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
            $select->where->like('Item_Name', $query . '%');
            $select->order('Item_Name');
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
                $select->where->like('Item_Name', '%' . $token . '%');
            }
            $select->order('Item_Name');
        };
        return $this->select($callback);
    }

    /**
     * Get a list of items for the specified series.
     *
     * @var int  $seriesID Series ID
     * @var bool $topOnly  Retrieve only top-level items?
     *
     * @return mixed
     */
    public function getItemsForSeries($seriesID, $topOnly = true)
    {
        $callback = function ($select) use ($seriesID, $topOnly) {
            $select->join(
                array('eds' => 'Editions'), 'eds.Item_ID = Items.Item_ID',
                array(
                    'Position',
                    'Edition_ID' => new Expression(
                        'min(?)', array('Edition_ID'),
                        array(Expression::TYPE_IDENTIFIER)
                    )
                )
            );
            $select->join(
                array('mt' => 'Material_Types'),
                'Items.Material_Type_ID = mt.Material_Type_ID'
            );
            $select->join(
                array('iat' => 'Items_AltTitles'),
                'eds.Preferred_Item_AltName_ID = iat.Sequence_ID',
                array('Item_AltName'), Select::JOIN_LEFT
            );
            $select->order(
                array('mt.Material_Type_Name', 'Position', 'Item_Name')
            );
            $select->group(
                array('Items.Item_ID', 'Position', 'Items.Material_Type_ID')
            );
            $select->where->equalTo('Series_ID', $seriesID);
            if ($topOnly) {
                $select->where->isNull('Parent_Edition_ID');
            }
        };
        return $this->select($callback);
    }

    /**
     * Get a list of children for the specified item.
     *
     * @var int $itemID Item ID
     *
     * @return mixed
     */
    public function getItemChildren($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->columns(array()); // no columns needed from non-parent Items table
            $select->join(
                array('eds' => 'Editions'), 'eds.Item_ID = Items.Item_ID',
                array('Edition_ID', 'Edition_Name')
            );
            $select->join(
                array('child_eds' => 'Editions'),
                'eds.Edition_ID = child_eds.Parent_Edition_ID',
                array(
                    'Extent_In_Parent', 'Position_In_Parent'
                )
            );
            $select->join(
                array('child_items' => 'Items'),
                'child_eds.Item_ID = child_items.Item_ID',
                array('Item_ID', 'Item_Name')
            );
            $select->join(
                array('mt' => 'Material_Types'),
                'child_items.Material_Type_ID = mt.Material_Type_ID',
                array('Material_Type_Name')
            );
            $select->join(
                array('iat' => 'Items_AltTitles'),
                'child_eds.Preferred_Item_AltName_ID = iat.Sequence_ID',
                array('Item_AltName'), Select::JOIN_LEFT
            );
            $select->order(
                array(
                    'eds.Edition_Name', 'eds.Edition_ID',
                    'child_eds.Position_In_Parent', 'child_items.Item_Name'
                )
            );
            $select->where->equalTo('Items.Item_ID', $itemID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of parents for the specified item.
     *
     * @var int $itemID Item ID
     *
     * @return mixed
     */
    public function getItemParents($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->columns(array()); // no columns needed from non-parent Items table
            $select->join(
                array('eds' => 'Editions'), 'eds.Item_ID = Items.Item_ID',
                array()
            );
            $select->join(
                array('parent_eds' => 'Editions'),
                'eds.Parent_Edition_ID = parent_eds.Edition_ID',
                array()
            );
            $select->join(
                array('parent_items' => 'Items'),
                'parent_eds.Item_ID = parent_items.Item_ID',
                array('Item_ID', 'Item_Name')
            );
            $select->join(
                array('mt' => 'Material_Types'),
                'parent_items.Material_Type_ID = mt.Material_Type_ID',
                array('Material_Type_Name')
            );
            $select->join(
                array('iat' => 'Items_AltTitles'),
                'parent_eds.Preferred_Item_AltName_ID = iat.Sequence_ID',
                array('Item_AltName'), Select::JOIN_LEFT
            );
            $select->order(
                array('parent_items.Item_Name', 'Material_Type_Name')
            );
            $select->group(
                array('parent_items.Item_ID', 'Material_Type_Name')
            );
            $select->where->equalTo('Items.Item_ID', $itemID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of items for the specified edition.
     *
     * @var int $editionID Edition ID
     *
     * @return mixed
     */
    public function getItemsForEdition($editionID)
    {
        $callback = function ($select) use ($editionID) {
            $select->join(
                array('eds' => 'Editions'), 'eds.Item_ID = Items.Item_ID',
                array('Extent_In_Parent', 'Position_in_Parent', 'Edition_ID')
            );
            $select->join(
                array('iat' => 'Items_AltTitles'),
                'eds.Preferred_Item_AltName_ID = iat.Sequence_ID',
                array('Item_AltName'), Select::JOIN_LEFT
            );
            $select->order(
                array('Position_in_Parent', 'Item_Name')
            );
            $select->where->equalTo('Parent_Edition_ID', $editionID);
        };
        return $this->select($callback);
    }
}
