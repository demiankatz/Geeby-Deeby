<?php
/**
 * Table Definition for Files
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

use Zend\Db\Adapter\Adapter;
use Zend\Db\RowGateway\RowGateway;

/**
 * Table Definition for Files
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class File extends Gateway
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
        parent::__construct($adapter, $tm, $rowObj, 'Files');
    }

    /**
     * Get a list of files.
     *
     * @return mixed
     */
    public function getList()
    {
        $callback = function ($select) {
            $select->order(array('File_Name'));
        };
        return $this->select($callback);
    }

    /**
     * Get a list of files grouped by type.
     *
     * @param array $include Array of file type IDs to retrieve (null to retrieve
     * all except those in $exclude)
     * @param array $exclude Array of file type IDs to exclude from results (null
     * to retrieve everything in $include, or everything if $include is null)
     *
     * @return mixed
     */
    public function getFilesByType($include = null, $exclude = null)
    {
        $callback = function ($select) use ($include, $exclude) {
            $select->join(
                array('ft' => 'File_Types'), 'Files.File_Type_ID = ft.File_Type_ID'
            );
            if (null !== $include) {
                $select->where->in('ft.File_Type_ID', $include);
            }
            if (null !== $exclude) {
                foreach ($exclude as $x) {
                    $select->where->notEqualTo('ft.File_Type_ID', $x);
                }
            }
            $select->order(array('File_Type', 'File_Name'));
        };
        return $this->select($callback);
    }
}
