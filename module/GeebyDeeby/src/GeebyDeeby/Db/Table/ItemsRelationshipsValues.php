<?php
/**
 * Table Definition for Items_Relationships_Values
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2018.
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

use Zend\Db\Adapter\Adapter;
use Zend\Db\RowGateway\RowGateway;

/**
 * Table Definition for Items_Relationships_Values
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsRelationshipsValues extends Gateway
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
        parent::__construct($adapter, $tm, $rowObj, 'Items_Relationships_Values');
    }

    /**
     * Get a list of items related to the provided subject item ID.
     *
     * @param int $itemID Item ID
     *
     * @return mixed
     */
    public function getItemsRelatedtoObjectItem($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->join(
                ['t' => 'Items'],
                'Items_Relationships_Values.Subject_Item_ID = t.Item_ID'
            );
            $select->where(['Object_Item_ID' => $itemID]);
            $select->order(['Item_Name']);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of items related to the provided subject item ID.
     *
     * @param int $itemID Item ID
     *
     * @return mixed
     */
    public function getItemsRelatedtoSubjectItem($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->join(
                ['t' => 'Items'],
                'Items_Relationships_Values.Object_Item_ID = t.Item_ID'
            );
            $select->where(['Subject_Item_ID' => $itemID]);
            $select->order(['Item_Name']);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of relationships for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return mixed
     */
    public function getRelationshipsForItem($itemID)
    {
        // Collect forward and inverse relationships in an index:
        $index = [];
        $subjectList = $this->getItemsRelatedtoSubjectItem($itemID);
        foreach ($subjectList->toArray() as $current) {
            $index[$current['Items_Relationship_ID']][] = $current;
        }
        $objectList = $this->getItemsRelatedtoObjectItem($itemID);
        foreach ($objectList->toArray() as $current) {
            $index['i' . $current['Items_Relationship_ID']][] = $current;
        }

        // Look up all options on the option list in the index to build return value:
        $retVal = [];
        $optionList = $this->getDbTable('itemsrelationship')->getOptionList(true);
        foreach ($optionList as $id => $relationship) {
            if (isset($index[$id])) {
                $retVal[] = $relationship + [
                    'relationship_id' => $id,
                    'values' => $index[$id],
                ];
            }
        }
        return $retVal;
    }
}
