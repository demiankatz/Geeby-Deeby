<?php

/**
 * Table Definition for Tags_Relationships_Values
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2018.
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
 * Table Definition for Tags_Relationships_Values
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class TagsRelationshipsValues extends Gateway
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
        parent::__construct($adapter, $tm, $rowObj, 'Tags_Relationships_Values');
    }

    /**
     * Get a list of tags related to the provided subject tag ID.
     *
     * @param int $tagID Tag ID
     *
     * @return mixed
     */
    public function getTagsRelatedtoObjectTag($tagID)
    {
        $callback = function ($select) use ($tagID) {
            $select->join(
                ['t' => 'Tags'],
                'Tags_Relationships_Values.Subject_Tag_ID = t.Tag_ID'
            );
            $select->where(['Object_Tag_ID' => $tagID]);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of tags related to the provided subject tag ID.
     *
     * @param int $tagID Tag ID
     *
     * @return mixed
     */
    public function getTagsRelatedtoSubjectTag($tagID)
    {
        $callback = function ($select) use ($tagID) {
            $select->join(
                ['t' => 'Tags'],
                'Tags_Relationships_Values.Object_Tag_ID = t.Tag_ID'
            );
            $select->where(['Subject_Tag_ID' => $tagID]);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of relationships for the specified tag.
     *
     * @param int $tagID Tag ID
     *
     * @return mixed
     */
    public function getRelationshipsForTag($tagID)
    {
        // Collect forward and inverse relationships in an index:
        $index = [];
        $subjectList = $this->getTagsRelatedtoSubjectTag($tagID);
        foreach ($subjectList->toArray() as $current) {
            $index[$current['Tags_Relationship_ID']][] = $current;
        }
        $objectList = $this->getTagsRelatedtoObjectTag($tagID);
        foreach ($objectList->toArray() as $current) {
            $index['i' . $current['Tags_Relationship_ID']][] = $current;
        }

        // Look up all options on the option list in the index to build return value:
        $retVal = [];
        $optionList = $this->getDbTable('tagsrelationship')->getOptionList(true);
        foreach ($optionList as $id => $relationship) {
            if (isset($index[$id])) {
                $retVal[] = $relationship + [
                    'relationship_id' => $id,
                    'values' => $index[$id],
                ];
            }
        }
        return $retVal;
    }
}
