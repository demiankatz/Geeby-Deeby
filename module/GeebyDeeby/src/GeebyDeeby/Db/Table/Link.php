<?php

/**
 * Table Definition for Links
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
 * Table Definition for Links
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class Link extends Gateway
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
        parent::__construct($adapter, $tm, $rowObj, 'Links');
    }

    /**
     * Get a list of links.
     *
     * @return mixed
     */
    public function getList()
    {
        $callback = function ($select) {
            $select->order(['Link_Name']);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of links grouped by type.
     *
     * @param string $typeFilter A filter for matching certain types (null for all)
     *
     * @return mixed
     */
    public function getListByType($typeFilter = null)
    {
        $callback = function ($select) use ($typeFilter) {
            $select->join(
                ['lt' => 'Link_Types'],
                'Links.Link_Type_ID = lt.Link_Type_ID'
            );
            if (null !== $typeFilter) {
                $select->where->like('Link_Type', $typeFilter . '%');
            }
            $select->order(['Link_Type', 'Link_Name']);
        };
        return $this->select($callback);
    }
}
