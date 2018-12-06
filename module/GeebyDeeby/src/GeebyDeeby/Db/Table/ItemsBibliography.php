<?php
/**
 * Table Definition for Items_Bibliography
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
 * Table Definition for Items_Bibliography
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsBibliography extends Gateway
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('Items_Bibliography');
    }

    /**
     * Get a list of items describing the specified item.
     *
     * @var int $itemID Item ID
     *
     * @return mixed
     */
    public function getItemsDescribingItem($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->join(
                array('i' => 'Items'),
                'Items_Bibliography.Bib_Item_ID = i.Item_ID'
            );
            $select->join(
                array('mt' => 'Material_Types'),
                'i.Material_Type_ID = mt.Material_Type_ID'
            );
            $select->order(array('Material_Type_Name', 'Item_Name'));
            $select->where->equalTo('Items_Bibliography.Item_ID', $itemID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of items described by the specified item.
     *
     * @var int $itemID Item ID
     *
     * @return mixed
     */
    public function getItemsDescribedByItem($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->join(
                array('i' => 'Items'),
                'Items_Bibliography.Item_ID = i.Item_ID'
            );
            $select->order('i.Item_Name');
            $select->where->equalTo('Bib_Item_ID', $itemID);
        };
        return $this->select($callback);
    }
}
