<?php
/**
 * Table Definition for Tags_URIs
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
 * Table Definition for Tags_URIs
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class TagsURIs extends Gateway
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
        parent::__construct($adapter, $tm, $rowObj, 'Tags_URIs');
    }

    /**
     * Get a list of URIs for the specified tag.
     *
     * @var int $tagID Tag ID
     *
     * @return mixed
     */
    public function getURIsForTag($tagID)
    {
        $callback = function ($select) use ($tagID) {
            $select->join(
                array('pr' => 'Predicates'),
                'Tags_URIs.Predicate_ID = pr.Predicate_ID'
            );
            $select->where->equalTo('Tag_ID', $tagID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of tags for the specified URI.
     *
     * @var string $uri URI
     *
     * @return mixed
     */
    public function getTagsForURI($uri)
    {
        $callback = function ($select) use ($uri) {
            $select->join(
                array('t' => 'Tags'),
                'Tags_URIs.Tag_ID = t.Tag_ID'
            );
            $select->join(
                array('pr' => 'Predicates'),
                'Tags_URIs.Predicate_ID = pr.Predicate_ID'
            );
            $select->order(array('Tag'));
            $select->where->equalTo('URI', $uri);
        };
        return $this->select($callback);
    }
}
