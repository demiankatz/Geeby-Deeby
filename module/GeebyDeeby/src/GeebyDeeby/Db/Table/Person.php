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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\Db\Table;

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
     */
    public function __construct()
    {
        parent::__construct('People', 'GeebyDeeby\Db\Row\Person');
    }

    /**
     * Get a list of categories.
     *
     * @return mixed
     */
    public function getList()
    {
        $callback = function ($select) {
            $select->order(array('Last_Name', 'First_Name', 'Middle_Name'));
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
            $select->order(array('Last_Name', 'First_Name', 'Middle_Name'));
        };
        return $this->select($callback);
    }
}
