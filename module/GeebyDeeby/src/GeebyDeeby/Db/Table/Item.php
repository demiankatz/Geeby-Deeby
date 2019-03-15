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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
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
        $callback = function ($select) use ($query) {
            $select2 = clone($select);
            $select2->columns(
                [
                    'Item_ID',
                    'Item_Name' => new Expression(
                        "Concat(Item_AltName, ' [alt. title for ', Item_Name, ']')"
                    )
                ]
            );
            $select2->join(
                array('iat' => 'Items_AltTitles'),
                'Items.Item_ID = iat.Item_ID',
                [], Select::JOIN_LEFT
            );
            $select2->where->like('Item_AltName', $query . '%');
            $select->columns(
                [
                    'Item_ID',
                    'Item_Name',
                ]
            );
            $select->where->like('Item_Name', $query . '%');
            $select->combine($select2);
        };
        return $this->sortAndFilterUnion($this->select($callback), $limit);
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
     * @var int  $seriesID        Series ID
     * @var bool $topOnly         Retrieve only top-level items?
     * @var bool $groupByMaterial Should we group results by material type?
     *
     * @return mixed
     */
    public function getItemsForSeries($seriesID, $topOnly = true,
        $groupByMaterial = true
    ) {
        $callback = function ($select) use ($seriesID, $topOnly, $groupByMaterial) {
            $select->join(
                array('eds' => 'Editions'), 'eds.Item_ID = Items.Item_ID',
                array(
                    'Volume', 'Position', 'Replacement_Number',
                    'Edition_ID' => new Expression(
                        'min(?)', array('eds.Edition_ID'),
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
                $groupByMaterial
                    ? array('mt.Material_Type_Name', 'Volume', 'Position', 'Replacement_Number', 'Item_Name')
                    : array('Volume', 'Position', 'Replacement_Number', 'Item_Name')
            );
            $select->group(
                array('Items.Item_ID', 'Volume', 'Position', 'Replacement_Number', 'Items.Material_Type_ID')
            );
            $select->where->equalTo('eds.Series_ID', $seriesID);
            if ($topOnly) {
                $select->join(
                    array('childEds' => 'Editions'),
                    'eds.Edition_ID = childEds.Parent_Edition_ID',
                    array(),
                    Select::JOIN_LEFT
                );
                $select->join(
                    array('childItems' => 'Items'),
                    'childEds.Item_ID = childItems.Item_ID',
                    array(
                        'Child_Items' => new Expression(
                            'GROUP_CONCAT('
                                . 'COALESCE(?, ?) ORDER BY ? SEPARATOR \'||\')',
                            array(
                                'childIat.Item_AltName', 'childItems.Item_Name',
                                'childEds.Position_In_Parent'),
                            array(
                                Expression::TYPE_IDENTIFIER,
                                Expression::TYPE_IDENTIFIER,
                                Expression::TYPE_IDENTIFIER
                            )
                        )
                    ),
                    Select::JOIN_LEFT
                );
                $select->join(
                    array('childIat' => 'Items_AltTitles'),
                    'childEds.Preferred_Item_AltName_ID = childIat.Sequence_ID',
                    array(), Select::JOIN_LEFT
                );
                $select->where->isNull('eds.Parent_Edition_ID');
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
