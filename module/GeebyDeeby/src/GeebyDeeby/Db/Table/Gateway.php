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
use Zend\Db\TableGateway\AbstractTableGateway,
    Zend\ServiceManager\ServiceLocatorAwareInterface,
    Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Generic table gateway.
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class Gateway extends AbstractTableGateway implements ServiceLocatorAwareInterface
{
    protected $rowClass = null;
    
    /**
     * Service locator
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Constructor
     *
     * @param string $table    Name of database table to interface with
     * @param string $rowClass Name of class used to represent rows (null for
     * default)
     */
    public function __construct($table, $rowClass = null)
    {
        $this->table = $table;
        $this->rowClass = $rowClass;
    }

    /**
     * Set database adapter
     *
     * @param \Zend\Db\Adapter\Adapter $adapter Database adapter
     *
     * @return void
     */
    public function setAdapter(\Zend\Db\Adapter\Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Initialize
     *
     * @return void
     */
    public function initialize()
    {
        if ($this->isInitialized) {
            return;
        }
        parent::initialize();
        if (null !== $this->rowClass) {
            $resultSetPrototype = $this->getResultSetPrototype();
            $prototype = new $this->rowClass($this->getAdapter());
            if ($prototype instanceof ServiceLocatorAwareInterface) {
                $prototype->setServiceLocator($this->getServiceLocator());
            }
            $resultSetPrototype->setArrayObjectPrototype($prototype);
        }
    }

    /**
     * Create a new row.
     *
     * @return object
     */
    public function createRow()
    {
        return clone($this->getResultSetPrototype()->getArrayObjectPrototype());
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
        $key = (array) $key;
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
        return $this->getServiceLocator()->get($table);
    }

    /**
     * Set the service locator.
     *
     * @param ServiceLocatorInterface $serviceLocator Locator to register
     *
     * @return Gateway
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Get the service locator.
     *
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}
