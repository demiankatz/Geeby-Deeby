<?php

/**
 * Table Definition for Full_Text_Sources
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
 * Table Definition for Full_Text_Sources
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class FullTextSource extends Gateway
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
        parent::__construct($adapter, $tm, $rowObj, 'Full_Text_Sources');
    }

    /**
     * Get a list of types.
     *
     * @param int $seriesID Series ID to limit (optional)
     *
     * @return mixed
     */
    public function getList($seriesID = null)
    {
        $filter = [];
        if (!empty($seriesID)) {
            $fulltext = $this->getDbTable('editionsfulltext');
            $filterCallback = function ($select) use ($seriesID) {
                $select->join(
                    ['e' => 'Editions'],
                    'Editions_Full_Text.Edition_ID = e.Edition_ID',
                    [],
                    \Laminas\Db\Sql\Select::JOIN_INNER
                );
                $select->columns(['Full_Text_Source_ID']);
                $select->quantifier(\Laminas\Db\Sql\Select::QUANTIFIER_DISTINCT);
                $select->where(['Series_ID' => $seriesID]);
            };
            foreach ($fulltext->select($filterCallback) as $current) {
                $filter[] = $current['Full_Text_Source_ID'];
            }
        }
        $callback = function ($select) use ($filter) {
            $select->order('Full_Text_Source_Name');
            if (!empty($filter)) {
                $select->where->in('Full_Text_Source_ID', $filter);
            }
        };
        return $this->select($callback);
    }
}
