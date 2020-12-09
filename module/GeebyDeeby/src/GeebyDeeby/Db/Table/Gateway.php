<?php
/**
 * Generic table gateway.
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
use Laminas\Db\TableGateway\AbstractTableGateway;

/**
 * Generic table gateway.
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class Gateway extends AbstractTableGateway
{
    use \GeebyDeeby\Db\ActivityLoggerTrait;

    /**
     * Table manager
     *
     * @var PluginManager
     */
    protected $tableManager;

    /**
     * Constructor
     *
     * @param Adapter       $adapter Database adapter
     * @param PluginManager $tm      Table manager
     * @param RowGateway    $rowObj  Row prototype object (null for default)
     * @param string        $table   Table name
     */
    public function __construct(Adapter $adapter, PluginManager $tm,
        RowGateway $rowObj = null, $table = null
    ) {
        $this->adapter = $adapter;
        $this->tableManager = $tm;
        $this->table = $table;
        $this->initialize();
        if (null !== $rowObj) {
            $resultSetPrototype = $this->getResultSetPrototype();
            $resultSetPrototype->setArrayObjectPrototype($rowObj);
        }
    }

    /**
     * Set database adapter
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     *
     * @return void
     */
    public function setAdapter(\Laminas\Db\Adapter\Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Create a new row.
     *
     * @return object
     */
    public function createRow()
    {
        return clone $this->getResultSetPrototype()->getArrayObjectPrototype();
    }

    /**
     * Retrieve a row by primary key.
     *
     * @param mixed $key Primary key (string or array)
     *
     * @return object
     */
    public function getByPrimaryKey($key)
    {
        $key = (array)$key;
        $keyCols = $this->getResultSetPrototype()->getArrayObjectPrototype()
            ->getPrimaryKeyColumn();
        if (count($key) != count($keyCols)) {
            throw new \Exception('Invalid key value passed in.');
        }
        $query = function ($select) use ($key, $keyCols) {
            foreach ($keyCols as $i => $col) {
                $select->where->equalTo($col, $key[$i]);
            }
        };
        return $this->select($query)->current();
    }

    /**
     * Get access to another table.
     *
     * @param string $table Table name
     *
     * @return Gateway
     */
    public function getDbTable($table)
    {
        return $this->tableManager->get($table);
    }

    /**
     * Laminas/DB doesn't do a good job of sorting or limiting UNIONs, so we have to
     * do it manually after the fact with this support method.
     *
     * @param mixed $rawResults Iterable result set.
     * @param mixed $limit      Result size limit (or false for none)
     *
     * @return array
     */
    protected function sortAndFilterUnion($rawResults, $limit)
    {
        $results = [];
        foreach ($rawResults as $current) {
            $results[] = $current;
        }
        $sort = function ($a, $b) {
            return strcasecmp($a->getDisplayName(), $b->getDisplayName());
        };
        usort($results, $sort);
        return $limit ? array_slice($results, 0, $limit) : $results;
    }

    /**
     * Delete
     *
     * @param mixed $where Where clause
     *
     * @return int
     */
    public function delete($where)
    {
        $this->logActivity('DELETE');
        return parent::delete($where);
    }

    /**
     * Insert
     *
     * @param array $set Data to insert
     *
     * @return int
     */
    public function insert($set)
    {
        $this->logActivity('INSERT');
        return parent::insert($set);
    }

    /**
     * Update
     *
     * @param array                 $set   Data to set
     * @param string|array|\Closure $where Where clause
     * @param null|array            $joins Joins
     *
     * @return int
     */
    public function update($set, $where = null, array $joins = null)
    {
        $this->logActivity('UPDATE');
        return parent::update($set, $where, $joins);
    }
}
