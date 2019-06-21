<?php
/**
 * Table Definition for Items_In_Collections
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

use Zend\Db\Adapter\Adapter;
use Zend\Db\RowGateway\RowGateway;
use Zend\Db\Sql\Select;

/**
 * Table Definition for Items_In_Collections
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsInCollections extends Gateway
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
        parent::__construct($adapter, $tm, $rowObj, 'Items_In_Collections');
    }

    /**
     * Get a list of all collections.
     *
     * @return mixed
     */
    public function getAllCollections()
    {
        $callback = function ($select) {
            $select->columns(array());
            $select->join(
                array('i' => 'Items'),
                'Items_In_Collections.Collection_Item_ID = i.Item_ID'
            );
            $select->order('Item_Name');
            $select->group('Item_ID');
        };
        return $this->select($callback);
    }

    /**
     * Get a list of collections for the specified item.
     *
     * @var int $itemID Item ID
     *
     * @return mixed
     */
    public function getCollectionsForItem($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->join(
                array('i' => 'Items'),
                'Items_In_Collections.Collection_Item_ID = i.Item_ID'
            );
            $select->join(
                array('mt' => 'Material_Types'),
                'i.Material_Type_ID = mt.Material_Type_ID'
            );
            $select->join(
                array('n' => 'Notes'),
                'Items_In_Collections.Note_ID = n.Note_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $select->order(
                array('mt.Material_Type_Name', 'i.Item_Name')
            );
            $select->where->equalTo('Items_In_Collections.Item_ID', $itemID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of items for the specified collection.
     *
     * @var int $collectionID Item ID
     *
     * @return mixed
     */
    public function getItemsForCollection($collectionID)
    {
        $callback = function ($select) use ($collectionID) {
            $select->join(
                array('i' => 'Items'),
                'Items_In_Collections.Item_ID = i.Item_ID'
            );
            $select->join(
                array('mt' => 'Material_Types'),
                'i.Material_Type_ID = mt.Material_Type_ID'
            );
            $select->join(
                array('n' => 'Notes'),
                'Items_In_Collections.Note_ID = n.Note_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $select->order(
                array('mt.Material_Type_Name', 'Position', 'i.Item_Name')
            );
            $select->where->equalTo('Collection_Item_ID', $collectionID);
        };
        return $this->select($callback);
    }
}
