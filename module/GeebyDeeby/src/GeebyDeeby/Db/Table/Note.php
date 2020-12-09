<?php
/**
 * Table Definition for Notes
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

/**
 * Table Definition for Notes
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class Note extends Gateway
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
        parent::__construct($adapter, $tm, $rowObj, 'Notes');
    }

    /**
     * Get a list of notes.
     *
     * @return mixed
     */
    public function getList()
    {
        $callback = function ($select) {
            $select->order('Note');
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
            $select->where->like('Note', $query . '%');
            $select->order('Note');
        };
        return $this->select($callback);
    }
}
