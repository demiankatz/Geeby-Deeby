<?php
/**
 * Table Definition for Tags_Relationships
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

/**
 * Table Definition for Tags_Relationships
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class TagsRelationship extends Gateway
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('Tags_Relationships', 'GeebyDeeby\Db\Row\TagsRelationship');
    }

    /**
     * Get a list of relationships, formatted to populate a select control.
     *
     * @param bool $includePredicate Should we return labels only (false) or an
     * array with label and predicate (true)?
     *
     * @return array
     */
    public function getOptionList($includePredicate = false)
    {
        $options = [];
        foreach ($this->getList() as $current) {
            $id = $current['Tags_Relationship_ID'];
            $options[] = [
                $current['Display_Priority'],
                $id,
                $current['Tags_Relationship_Name'],
                $current['Tags_Relationship_RDF_Property']
            ];
            if (!empty($current['Tags_Inverse_Relationship_Name'])) {
                $options[] = [
                    $current['Inverse_Display_Priority'],
                    'i' . $id,
                    $current['Tags_Inverse_Relationship_Name'],
                    $current['Tags_Inverse_Relationship_RDF_Property']
                ];
            }
        }
        $callback = function ($a, $b) {
            return $a[0] - $b[0];
        };
        usort($options, $callback);
        $retval = [];
        foreach ($options as $current) {
            $value = $includePredicate
                ? ['label' => $current[2], 'predicate' => $current[3]]
                : $current[2];
            $retval[$current[1]] = $value;
        }
        return $retval;
    }

    /**
     * Get a list of relationships.
     *
     * @param mixed $where Where clause for list.
     * @return mixed
     */
    public function getList($where = null)
    {
        $callback = function ($select) use ($where) {
            if (null !== $where) {
                $select->where($where);
            }
            $select->order('Tags_Relationship_Name');
        };
        return $this->select($callback);
    }
}
