<?php

/**
 * Table Definition for People
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

use function count;
use function intval;

/**
 * Table Definition for People
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class Person extends Gateway
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
        parent::__construct($adapter, $tm, $rowObj, 'People');
    }

    /**
     * Get a list of people.
     *
     * @param bool $biosOnly Should we filter to only people with biographies?
     *
     * @return mixed
     */
    public function getList($biosOnly = false)
    {
        $callback = function ($select) use ($biosOnly) {
            if ($biosOnly) {
                $select->where->notEqualTo('Biography', '');
            }
            $select->order(['Last_Name', 'First_Name']);
        };
        return $this->select($callback);
    }

    /**
     * Get people for item IDs.
     *
     * @param array $itemIds Item IDs to match.
     *
     * @return mixed
     */
    public function getListForItemIds($itemIds)
    {
        $callback = function ($select) use ($itemIds) {
            $select->quantifier('DISTINCT');
            $select->join(
                ['ec' => 'Editions_Credits'],
                'ec.Person_ID = People.Person_ID',
                []
            );
            $select->join(
                ['e' => 'Editions'],
                'ec.Edition_ID = e.Edition_ID',
                ['Item_ID']
            );
            $select->where->in('Item_ID', $itemIds);
            $select->order(['Last_Name', 'First_Name']);
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
        $parts = preg_split("/[\s,]+/", $query);
        $first = $parts[0];
        $c = count($parts);
        $last = ($c > 1) ? $parts[$c - 1] : false;
        $callback = function ($select) use ($first, $last, $limit) {
            if ($limit !== false) {
                $select->limit($limit);
            }
            $nest = $select->where->NEST;
            $nest->like('First_Name', $first . '%')
                ->OR->like('Last_Name', $first . '%');
            if (intval($first) > 0) {
                $nest->OR->equalTo('Person_ID', intval($first));
            }
            $nest->UNNEST;
            if ($last) {
                $select->where->AND->NEST->like('First_Name', $last . '%')
                    ->OR->like('Last_Name', $last . '%')->UNNEST;
            }
            $select->order(['Last_Name', 'First_Name']);
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
                $nest = $select->where->NEST;
                $nest->like('First_Name', '%' . $token . '%');
                $nest->OR->like('Last_Name', '%' . $token . '%');
                $nest->OR->like('Extra_Details', '%' . $token . '%');
                $nest->UNNEST;
            }
            $select->order(['Last_Name', 'First_Name']);
        };
        return $this->select($callback);
    }
}
