<?php
/**
 * Table Definition for Items_Creators
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
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;

/**
 * Table Definition for Items_Creators
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsCreators extends Gateway
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('Items_Creators');
    }

    /**
     * Given an item identifier, return a list of creators.
     *
     * @param int $itemID Item ID
     *
     * @return mixed
     */
    public function getCreatorsForItem($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->join(
                array('p' => 'People'),
                'Items_Creators.Person_ID = p.Person_ID'
            );
            $select->join(
                array('r' => 'Roles'),
                'Items_Creators.Role_ID = r.Role_ID'
            );
            $fields = array(
                'Role_Name', 'Last_Name', 'First_Name', 'Middle_Name'
            );
            $select->order($fields);
            $select->where->equalTo('Item_ID', $itemID);
        };
        return $this->select($callback);
    }
}
