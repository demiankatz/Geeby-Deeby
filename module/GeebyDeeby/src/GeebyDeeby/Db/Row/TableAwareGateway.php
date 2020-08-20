<?php
/**
 * Abstract base class for rows that need access to other tables.
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
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\Db\Row;

use GeebyDeeby\Db\Table\PluginManager as TableManager;

/**
 * Abstract base class for rows that need access to other tables.
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class TableAwareGateway extends RowGateway
{
    /**
     * Table manager
     *
     * @var TableManager
     */
    protected $tableManager = null;

    /**
     * Get access to another table.
     *
     * @param string $table Table name
     *
     * @return \GeebyDeeby\Db\Table\Gateway
     */
    public function getDbTable($table)
    {
        return $this->getTableManager()->get($table);
    }

    /**
     * Set the service locator.
     *
     * @param TableManager $tm Table manager
     *
     * @return TableAwareGateway
     */
    public function setTableManager(TableManager $tm)
    {
        $this->tableManager = $tm;
        return $this;
    }

    /**
     * Get the table manager
     *
     * @return TableManager
     */
    public function getTableManager()
    {
        if (null === $this->tableManager) {
            throw new \Exception('Expected table manager is missing.');
        }
        return $this->tableManager;
    }
}
