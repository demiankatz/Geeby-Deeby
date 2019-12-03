<?php
/**
 * Table Definition for Items_Files
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
 * Table Definition for Items_Files
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsFiles extends Gateway
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
        parent::__construct($adapter, $tm, $rowObj, 'Items_Files');
    }

    /**
     * Get a list of items for the specified file.
     *
     * @var int $fileID File ID
     *
     * @return mixed
     */
    public function getItemsForFile($fileID)
    {
        $callback = function ($select) use ($fileID) {
            $select->join(
                array('i' => 'Items'),
                'Items_Files.Item_ID = i.Item_ID'
            );
            $select->order('i.Item_Name');
            $select->where->equalTo('File_ID', $fileID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of files for the specified item.
     *
     * @var int $itemID Item ID
     *
     * @return mixed
     */
    public function getFilesForItem($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->join(
                array('f' => 'Files'),
                'Items_Files.File_ID = f.File_ID'
            );
            $select->join(
                array('ft' => 'File_Types'),
                'f.File_Type_ID = ft.File_Type_ID'
            );
            $select->order(array('File_Type', 'File_Name'));
            $select->where->equalTo('Item_ID', $itemID);
        };
        return $this->select($callback);
    }
}
