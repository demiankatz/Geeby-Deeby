<?php
/**
 * Abstract Table Definition for Relationship Tables.
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2019.
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
 * Abstract Table Definition for Relationship Tables.
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
abstract class AbstractRelationship extends Gateway
{
    /**
     * Prefix to use in table/class names.
     *
     * @var string
     */
    protected $prefix;

    /**
     * Constructor
     *
     * @param Adapter       $adapter Database adapter
     * @param PluginManager $tm      Table manager
     * @param RowGateway    $rowObj  Row prototype object (null for default)
     * @param string        $prefix  Prefix to use in table/class names.
     */
    public function __construct(Adapter $adapter, PluginManager $tm,
        RowGateway $rowObj = null, $prefix
    ) {
        $this->prefix = $prefix;
        parent::__construct($adapter, $tm, $rowObj, $prefix . '_Relationships');
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
            $id = $current[$this->prefix . '_Relationship_ID'];
            $options[] = [
                $current['Display_Priority'],
                $id,
                $current[$this->prefix . '_Relationship_Name'],
                $current[$this->prefix . '_Relationship_RDF_Property']
            ];
            if (!empty($current[$this->prefix . '_Inverse_Relationship_Name'])) {
                $options[] = [
                    $current['Inverse_Display_Priority'],
                    'i' . $id,
                    $current[$this->prefix . '_Inverse_Relationship_Name'],
                    $current[$this->prefix . '_Inverse_Relationship_RDF_Property']
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
     * @param  mixed $where Where clause for list.
     * @return mixed
     */
    public function getList($where = null)
    {
        $callback = function ($select) use ($where) {
            if (null !== $where) {
                $select->where($where);
            }
            $select->order($this->prefix . '_Relationship_Name');
        };
        return $this->select($callback);
    }
}
