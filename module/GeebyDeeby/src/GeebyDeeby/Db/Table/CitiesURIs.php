<?php
/**
 * Table Definition for Cities_URIs
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2015.
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
 * Table Definition for Cities_URIs
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class CitiesURIs extends Gateway
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
        parent::__construct($adapter, $tm, $rowObj, 'Cities_URIs');
    }

    /**
     * Get a list of URIs for the specified city.
     *
     * @param int $cityID City ID
     *
     * @return mixed
     */
    public function getURIsForCity($cityID)
    {
        $callback = function ($select) use ($cityID) {
            $select->join(
                ['pr' => 'Predicates'],
                'Cities_URIs.Predicate_ID = pr.Predicate_ID'
            );
            $select->where->equalTo('City_ID', $cityID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of cities for the specified URI.
     *
     * @param string $uri URI
     *
     * @return mixed
     */
    public function getCitiesForURI($uri)
    {
        $callback = function ($select) use ($uri) {
            $select->join(
                ['c' => 'Cities'],
                'Cities_URIs.City_ID = c.City_ID'
            );
            $select->join(
                ['pr' => 'Predicates'],
                'Cities_URIs.Predicate_ID = pr.Predicate_ID'
            );
            $select->order(['City_Name']);
            $select->where->equalTo('URI', $uri);
        };
        return $this->select($callback);
    }
}
