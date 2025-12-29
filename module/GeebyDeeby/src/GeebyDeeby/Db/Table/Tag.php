<?php

/**
 * Table Definition for Tags
 *
 * PHP version 8
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
 * along with this program; if not, see
 * <https://www.gnu.org/licenses/>.
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
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;

/**
 * Table Definition for Tags
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class Tag extends Gateway
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
        parent::__construct($adapter, $tm, $rowObj, 'Tags');
    }

    /**
     * Get a list of tags.
     *
     * @return mixed
     */
    public function getList()
    {
        $callback = function ($select): void {
            $select->order(['Tag']);
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
        $callback = function ($select) use ($query, $limit): void {
            if ($limit !== false) {
                $select->limit($limit);
            }
            $select->where->like('Tag', $query . '%');
            $select->order('Tag');
        };
        return $this->select($callback);
    }

    /**
     * Get a list of tags and related items for the specified series.
     *
     * @param int $seriesID Series ID
     *
     * @return mixed
     */
    public function getTagsForSeries($seriesID)
    {
        $callback = function ($select) use ($seriesID): void {
            $select->quantifier('DISTINCT');
            $select->columns(['Tag_ID', 'Tag']);
            $select->join(
                ['it' => 'Items_Tags'],
                'it.Tag_ID = Tags.Tag_ID',
                []
            );
            $select->join(
                ['i' => 'Items'],
                'it.Item_ID = i.Item_ID',
                ['Item_ID', 'Item_Name']
            );
            $select->join(
                ['eds' => 'Editions'],
                'eds.Item_ID = i.Item_ID',
                []
            );
            $select->join(
                ['iat' => 'Items_AltTitles'],
                'eds.Preferred_Item_AltName_ID = iat.Sequence_ID',
                ['Item_AltName'],
                Select::JOIN_LEFT
            );
            $bestTitle = new Expression(
                'COALESCE(?, ?)',
                ['Item_AltName', 'Item_Name'],
                [
                    Expression::TYPE_IDENTIFIER,
                    Expression::TYPE_IDENTIFIER,
                ]
            );
            $select->order(['Tag', $bestTitle]);
            $select->where->equalTo('eds.Series_ID', $seriesID);
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
        $callback = function ($select) use ($tokens): void {
            foreach ($tokens as $token) {
                $select->where->like('Tag', '%' . $token . '%');
            }
            $select->order('Tag');
        };
        return $this->select($callback);
    }
}
