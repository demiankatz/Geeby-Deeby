<?php
/**
 * Generic row gateway
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

/**
 * Generic row gateway
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class RowGateway extends \Zend\Db\RowGateway\RowGateway
{
    use \GeebyDeeby\Db\ActivityLoggerTrait;

    /**
     * Validate the fields in the current object.  Return error message if problem
     * found, boolean false if no errors were found.
     *
     * @return string|bool
     */
    public function validate()
    {
        // Assume valid row by default:
        return false;
    }

    /**
     * Get primary key for the table.
     *
     * @return array
     */
    public function getPrimaryKeyColumn()
    {
        return $this->primaryKeyColumn;
    }

    /**
     * Get primary key value for the table
     *
     * @return string
     */
    public function getPrimaryKeyValue()
    {
        if (count($this->primaryKeyColumn) != 1) {
            throw new \Exception('Unsupported for multi-key tables');
        }
        $key = $this->primaryKeyColumn[0];
        return $this->$key;
    }

    /**
     * Save
     *
     * @return void
     */
    public function save()
    {
        $this->logActivity();
        parent::save();
    }
}
