<?php
/**
 * Table Definition for People_Bibliography
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

/**
 * Table Definition for People_Bibliography
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PeopleBibliography extends Gateway
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
        parent::__construct($adapter, $tm, $rowObj, 'People_Bibliography');
    }

    /**
     * Get a list of items describing the specified person.
     *
     * @param int $personID Person ID
     *
     * @return mixed
     */
    public function getItemsDescribingPerson($personID)
    {
        $callback = function ($select) use ($personID) {
            $select->join(
                ['i' => 'Items'],
                'People_Bibliography.Item_ID = i.Item_ID'
            );
            $select->join(
                ['mt' => 'Material_Types'],
                'i.Material_Type_ID = mt.Material_Type_ID'
            );
            $select->order(['Material_Type_Name', 'Item_Name']);
            $select->where->equalTo('Person_ID', $personID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of people described by the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return mixed
     */
    public function getPeopleDescribedByItem($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->join(
                ['p' => 'People'],
                'People_Bibliography.Person_ID = p.Person_ID'
            );
            $select->order(['Last_Name', 'First_Name']);
            $select->where->equalTo('Item_ID', $itemID);
        };
        return $this->select($callback);
    }
}
