<?php

/**
 * Table Definition for Publishers_Addresses
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
use Laminas\Db\Sql\Select;

/**
 * Table Definition for Publishers_Addresses
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PublishersAddresses extends Gateway
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
        parent::__construct($adapter, $tm, $rowObj, 'Publishers_Addresses');
    }

    /**
     * Get a list of addresses for the specified publisher.
     *
     * @param int $pubID Publisher ID
     *
     * @return mixed
     */
    public function getAddressesForPublisher($pubID)
    {
        $callback = function ($select) use ($pubID) {
            $select->join(
                ['ci' => 'Cities'],
                'Publishers_Addresses.City_ID = ci.City_ID',
                Select::SQL_STAR,
                Select::JOIN_LEFT
            );
            $select->join(
                ['c' => 'Countries'],
                'Publishers_Addresses.Country_ID = c.Country_ID'
            );
            $select->order(['Country_Name', 'City_Name', 'Street']);
            $select->where->equalTo('Publisher_ID', $pubID);
        };
        return $this->select($callback);
    }
}
