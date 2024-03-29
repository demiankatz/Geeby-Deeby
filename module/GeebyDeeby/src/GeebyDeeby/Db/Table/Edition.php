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
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;

use function count;

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
        parent::__construct($adapter, $tm, $rowObj, 'Editions');
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
     * Get parent item for the specified edition (false if none).
     *
     * @param int $editionID Edition ID
     *
     * @return mixed
     */
    public function getParentItemForEdition($editionID)
    {
        $ed = $this->getByPrimaryKey($editionID);
        if (empty($ed->Parent_Edition_ID)) {
            return false;
        }
        $parent = $ed->Parent_Edition_ID;
        $callback = function ($select) use ($parent) {
            $select->join(
                ['items' => 'Items'],
                'Editions.Item_ID = items.Item_ID'
            );
            $select->join(
                ['iat' => 'Items_AltTitles'],
                'Editions.Preferred_Item_AltName_ID = iat.Sequence_ID',
                ['Item_AltName'],
                Select::JOIN_LEFT
            );
            $select->where->equalTo('Edition_ID', $parent);
        };
        $results = $this->select($callback);
        foreach ($results as $current) {
            return $current;
        }
        return false;
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
        // Proxy item table (so handleGenericLink() can be used in
        // EditEditionController):
        return $this->getDbTable('item')->getItemsForEdition($editionID);
    }

    /**
     * Get a list of items for the specified series (not grouped by material type).
     *
     * @param int $seriesID Series ID
     *
     * @return mixed
     */
    public function getItemsForSeries($seriesID)
    {
        // Proxy item table (so handleGenericLink() can be used in
        // EditSeriesController):
        return $this->getDbTable('item')->getItemsForSeries($seriesID, true, false);
    }

    /**
     * Get a list of items for the specified series ( grouped by material type).
     *
     * @param int $seriesID Series ID
     *
     * @return mixed
     */
    public function getItemsForSeriesGroupedByMaterial($seriesID)
    {
        // Proxy item table (so handleGenericLink() can be used in
        // EditSeriesController):
        return $this->getDbTable('item')->getItemsForSeries($seriesID);
    }

    /**
     * Retrieve editions for the specified item.
     *
     * @param int  $itemID         Item ID.
     * @param bool $includeParents Should we include information on parent items?
     *
     * @return mixed
     */
    public function getEditionsForItem($itemID, $includeParents = false)
    {
        $callback = function ($select) use ($itemID, $includeParents) {
            $year = new Expression(
                'min(?)',
                ['erd.Year'],
                [Expression::TYPE_IDENTIFIER]
            );
            $select->join(
                ['erd' => 'Editions_Release_Dates'],
                'Editions.Edition_ID = erd.Edition_ID OR '
                . 'Editions.Parent_Edition_ID = erd.Edition_ID',
                ['Earliest_Year' => $year],
                Select::JOIN_LEFT
            );
            $order = ['Item_Display_Order', 'Earliest_Year', 'Edition_Name'];
            $fields = [
                'Edition_ID',
                'Edition_Name',
                'Item_ID',
                'Series_ID',
                'Volume',
                'Position',
                'Replacement_Number',
                'Preferred_Item_AltName_ID',
                'Preferred_Series_AltName_ID',
                'Edition_Length',
                'Edition_Endings',
                'Edition_Description',
                'Preferred_Series_Publisher_ID',
                'Parent_Edition_ID',
                'Position_In_Parent',
                'Extent_In_Parent',
                'Item_Display_Order',
            ];
            $select->columns($fields);
            $select->where->equalTo('Editions.Item_ID', $itemID);
            if ($includeParents) {
                $select->join(
                    ['pe' => 'Editions'],
                    'Editions.Parent_Edition_ID = pe.Edition_ID',
                    ['Parent_Item_ID' => 'Item_ID'],
                    Select::JOIN_LEFT
                );
                $select->join(
                    ['i' => 'Items'],
                    'pe.Item_ID = i.Item_ID',
                    ['Item_Name'],
                    Select::JOIN_LEFT
                );
                $select->join(
                    ['iat' => 'Items_AltTitles'],
                    'pe.Preferred_Item_AltName_ID = iat.Sequence_ID',
                    ['Item_AltName'],
                    Select::JOIN_LEFT
                );
                $fields[] = 'Item_Name';
                $fields[] = 'Item_AltName';
                $order = array_merge(
                    $order,
                    [
                        'pe.Series_ID', 'pe.Volume', 'pe.Position',
                        'pe.Replacement_Number',
                    ]
                );
            }
            $select->group($fields);
            $select->order($order);
        };
        return $this->select($callback);
    }

    /**
     * Retrieve publishers for the specified match.
     *
     * @param string $field Field to match
     * @param string $value Value to match
     *
     * @return mixed
     */
    public function getPublishersForWhereClause($field, $value)
    {
        $callback = function ($select) use ($field, $value) {
            $select->join(
                ['sp' => 'Series_Publishers'],
                'Editions.Preferred_Series_Publisher_ID = sp.Series_Publisher_ID'
            );
            $select->join(
                ['p' => 'Publishers'],
                'sp.Publisher_ID = p.Publisher_ID'
            );
            $select->join(
                ['pa' => 'Publishers_Addresses'],
                'sp.Address_ID = pa.Address_ID',
                ['Street'],
                Select::JOIN_LEFT
            );
            $select->join(
                ['c' => 'Countries'],
                'pa.Country_ID = c.Country_ID',
                Select::SQL_STAR,
                Select::JOIN_LEFT
            );
            $select->join(
                ['ci' => 'Cities'],
                'pa.City_ID = ci.City_ID',
                Select::SQL_STAR,
                Select::JOIN_LEFT
            );
            $select->join(
                ['n' => 'Notes'],
                'sp.Note_ID = n.Note_ID',
                Select::SQL_STAR,
                Select::JOIN_LEFT
            );
            $select->where->equalTo($field, $value);
            $select->order(
                [
                    'Edition_Name',
                    'Publisher_Name', 'Country_Name', 'City_Name', 'Street',
                ]
            );
        };
        return $this->select($callback);
    }

    /**
     * Retrieve publishers for the specified edition.
     *
     * @param int $id Edition ID.
     *
     * @return mixed
     */
    public function getPublishersForEdition($id)
    {
        return $this->getPublishersForWhereClause('Edition_ID', $id);
    }

    /**
     * Retrieve publishers for the specified item.
     *
     * @param int $itemID Item ID.
     *
     * @return mixed
     */
    public function getPublishersForItem($itemID)
    {
        return $this->getPublishersForWhereClause('Item_ID', $itemID);
    }

    /**
     * Delete an edition if there are no attached data items.
     *
     * @param int $id ID of edition to delete
     *
     * @throws \Exception
     * @return void
     */
    public function safeDelete($id)
    {
        $select = ['Edition_ID' => $id];
        if (count($this->getDbTable('editionscredits')->select($select)) > 0) {
            throw new \Exception('Cannot delete - attached credits.');
        }
        if (count($this->getDbTable('editionsreleasedates')->select($select)) > 0) {
            throw new \Exception('Cannot delete - attached dates.');
        }
        $children = $this->getDbTable('edition')
            ->select(['Parent_Edition_ID' => $id]);
        if (count($children) > 0) {
            throw new \Exception('Cannot delete - has child editions.');
        }
        $this->delete($select);
    }

    /**
     * Copy information associated with one edition into another.
     *
     * @param int|\GeebyDeeby\Db\Row\Edition $from Source item (object or ID)
     * @param int|\GeebyDeeby\Db\Row\Edition $to   Target item (object or ID)
     *
     * @return void
     */
    public function copyAssociatedInfo($from, $to)
    {
        if (!($from instanceof \GeebyDeeby\Db\Row\Edition)) {
            $from = $this->getByPrimaryKey($from);
        }
        if (!($to instanceof \GeebyDeeby\Db\Row\Edition)) {
            $to = $this->getByPrimaryKey($to);
        }
        foreach ($from->getChildren() as $child) {
            $child->copy(
                [
                    'Parent_Edition_ID' => $to->Edition_ID,
                    'Series_ID' => $to->Series_ID,
                    'Edition_Name' => $to->Edition_Name,
                ]
            );
        }
        $to->copyAttributes($from->Edition_ID);
        $to->copyCredits($from->Edition_ID);
    }
}
