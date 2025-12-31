<?php

/**
 * Table Definition for Items
 *
 * PHP version 8
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
 * along with this program; if not, see
 * <https://www.gnu.org/licenses/>.
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
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;

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
        parent::__construct($adapter, $tm, $rowObj, 'Items');
    }

    /**
     * Get a list of items.
     *
     * @return mixed
     */
    public function getList()
    {
        $callback = function ($select): void {
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
        $callback = function ($select) use ($query): void {
            $select2 = clone $select;
            $select2->columns(
                [
                    'Item_ID',
                    'Item_Name' => new Expression(
                        "Concat(Item_AltName, ' [alt. title for ', Item_Name, ']')"
                    ),
                ]
            );
            $select2->join(
                ['iat' => 'Items_AltTitles'],
                'Items.Item_ID = iat.Item_ID',
                [],
                Select::JOIN_LEFT
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
        $callback = function ($select) use ($tokens): void {
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
     * @param int  $seriesID        Series ID
     * @param bool $topOnly         Retrieve only top-level items?
     * @param bool $groupByMaterial Should we group results by material type?
     *
     * @return mixed
     */
    public function getItemsForSeries(
        $seriesID,
        $topOnly = true,
        $groupByMaterial = true
    ) {
        $callback = function ($select) use ($seriesID, $topOnly, $groupByMaterial): void {
            $select->join(
                ['eds' => 'Editions'],
                'eds.Item_ID = Items.Item_ID',
                [
                    'Volume', 'Position', 'Replacement_Number',
                    'Edition_ID' => new Expression(
                        'min(?)',
                        ['eds.Edition_ID'],
                        [Expression::TYPE_IDENTIFIER]
                    ),
                ]
            );
            $select->join(
                ['mt' => 'Material_Types'],
                'Items.Material_Type_ID = mt.Material_Type_ID'
            );
            $select->join(
                ['iat' => 'Items_AltTitles'],
                'eds.Preferred_Item_AltName_ID = iat.Sequence_ID',
                ['Item_AltName'],
                Select::JOIN_LEFT
            );
            $itemName = new Expression(
                'COALESCE(?, ?)',
                ['iat.Item_AltName', 'Items.Item_Name'],
                [
                    Expression::TYPE_IDENTIFIER,
                    Expression::TYPE_IDENTIFIER,
                ]
            );
            $select->order(
                $groupByMaterial
                    ? [
                        'mt.Material_Type_Name', 'Volume', 'Position',
                        'Replacement_Number', $itemName,
                    ] : ['Volume', 'Position', 'Replacement_Number', $itemName]
            );
            $select->group(
                [
                    'Items.Item_ID', 'Volume', 'Position', 'Replacement_Number',
                    'Items.Material_Type_ID',
                ]
            );
            $select->where->equalTo('eds.Series_ID', $seriesID);
            if ($topOnly) {
                $select->join(
                    ['childEds' => 'Editions'],
                    'eds.Edition_ID = childEds.Parent_Edition_ID',
                    [],
                    Select::JOIN_LEFT
                );
                $select->join(
                    ['childItems' => 'Items'],
                    'childEds.Item_ID = childItems.Item_ID',
                    [
                        'Child_Items' => new Expression(
                            'GROUP_CONCAT('
                                . 'COALESCE(?, ?) ORDER BY ? SEPARATOR \'||\')',
                            [
                                'childIat.Item_AltName', 'childItems.Item_Name',
                                'childEds.Position_In_Parent'],
                            [
                                Expression::TYPE_IDENTIFIER,
                                Expression::TYPE_IDENTIFIER,
                                Expression::TYPE_IDENTIFIER,
                            ]
                        ),
                    ],
                    Select::JOIN_LEFT
                );
                $select->join(
                    ['childIat' => 'Items_AltTitles'],
                    'childEds.Preferred_Item_AltName_ID = childIat.Sequence_ID',
                    [],
                    Select::JOIN_LEFT
                );
                $select->where->isNull('eds.Parent_Edition_ID');
            }
        };
        return $this->select($callback);
    }

    /**
     * Get a list of children for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return mixed
     */
    public function getItemChildren($itemID)
    {
        $callback = function ($select) use ($itemID): void {
            $select->columns([]); // no columns needed from non-parent Items table
            $select->join(
                ['eds' => 'Editions'],
                'eds.Item_ID = Items.Item_ID',
                ['Edition_ID', 'Edition_Name']
            );
            $select->join(
                ['child_eds' => 'Editions'],
                'eds.Edition_ID = child_eds.Parent_Edition_ID',
                [
                    'Extent_In_Parent', 'Position_In_Parent',
                ]
            );
            $select->join(
                ['child_items' => 'Items'],
                'child_eds.Item_ID = child_items.Item_ID',
                ['Item_ID', 'Item_Name']
            );
            $select->join(
                ['mt' => 'Material_Types'],
                'child_items.Material_Type_ID = mt.Material_Type_ID',
                ['Material_Type_Name']
            );
            $select->join(
                ['iat' => 'Items_AltTitles'],
                'child_eds.Preferred_Item_AltName_ID = iat.Sequence_ID',
                ['Item_AltName'],
                Select::JOIN_LEFT
            );
            $select->order(
                [
                    'eds.Edition_Name', 'eds.Edition_ID',
                    'child_eds.Position_In_Parent', 'child_items.Item_Name',
                ]
            );
            $select->where->equalTo('Items.Item_ID', $itemID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of parents for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return mixed
     */
    public function getItemParents($itemID)
    {
        $callback = function ($select) use ($itemID): void {
            $select->columns([]); // no columns needed from non-parent Items table
            $select->join(
                ['eds' => 'Editions'],
                'eds.Item_ID = Items.Item_ID',
                []
            );
            $select->join(
                ['parent_eds' => 'Editions'],
                'eds.Parent_Edition_ID = parent_eds.Edition_ID',
                []
            );
            $select->join(
                ['parent_items' => 'Items'],
                'parent_eds.Item_ID = parent_items.Item_ID',
                ['Item_ID', 'Item_Name']
            );
            $select->join(
                ['mt' => 'Material_Types'],
                'parent_items.Material_Type_ID = mt.Material_Type_ID',
                ['Material_Type_Name']
            );
            $select->join(
                ['iat' => 'Items_AltTitles'],
                'parent_eds.Preferred_Item_AltName_ID = iat.Sequence_ID',
                ['Item_AltName'],
                Select::JOIN_LEFT
            );
            $select->order(
                ['parent_items.Item_Name', 'Material_Type_Name']
            );
            $select->group(
                ['parent_items.Item_ID', 'Material_Type_Name']
            );
            $select->where->equalTo('Items.Item_ID', $itemID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of items for the specified edition.
     *
     * @param int $editionID Edition ID
     *
     * @return mixed
     */
    public function getItemsForEdition($editionID)
    {
        $callback = function ($select) use ($editionID): void {
            $select->join(
                ['eds' => 'Editions'],
                'eds.Item_ID = Items.Item_ID',
                ['Extent_In_Parent', 'Position_in_Parent', 'Edition_ID']
            );
            $select->join(
                ['iat' => 'Items_AltTitles'],
                'eds.Preferred_Item_AltName_ID = iat.Sequence_ID',
                ['Item_AltName'],
                Select::JOIN_LEFT
            );
            $select->order(
                ['Position_in_Parent', 'Item_Name']
            );
            $select->where->equalTo('Parent_Edition_ID', $editionID);
        };
        return $this->select($callback);
    }

    /**
     * Get items with online full text associated with a person
     *
     * @param int $personId Person_ID to filter by
     *
     * @return \Laminas\Db\ResultSet\ResultSet
     */
    public function getItemsWithFullTextByPerson(int $personId)
    {
        $callback = function ($select) use ($personId): void {

            // Base table is already "Items" via TableGateway

            // Item-level creators
            $select->join(
                ['ic' => 'Items_Creators'],
                'Items.Item_ID = ic.Item_ID',
                [],
                $select::JOIN_LEFT
            );

            // Editions
            $select->join(
                ['eds' => 'Editions'],
                'eds.Item_ID = Items.Item_ID'
            );

            // Full text
            $select->join(
                ['eft' => 'Editions_Full_Text'],
                'eft.Edition_ID = eds.Edition_ID'
            );

            // Edition-level credits
            $select->join(
                ['ec' => 'Editions_Credits'],
                'eds.Edition_ID = ec.Edition_ID',
                [],
                $select::JOIN_LEFT
            );

            // Filter by person (item OR edition credit)
            $select->where->nest()
                ->equalTo('ic.Person_ID', $personId)
                ->or
                ->equalTo('ec.Person_ID', $personId)
            ->unnest();

            // Prevent duplicates
            $select->group([
                'eds.Item_ID',
                'eds.Series_ID',
                'eds.Volume',
                'eds.Position',
                'eds.Replacement_Number',
            ]);
        };

        return $this->select($callback);
    }
}
