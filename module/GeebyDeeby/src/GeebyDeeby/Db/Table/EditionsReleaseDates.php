<?php
/**
 * Table Definition for Editions_Release_Dates
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
use Zend\Db\Sql\Select;

/**
 * Table Definition for Editions_Release_Dates
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsReleaseDates extends Gateway
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
        parent::__construct($adapter, $tm, $rowObj, 'Editions_Release_Dates');
    }

    /**
     * Get a list of dates for the specified item.
     *
     * @var int $itemID Item ID
     *
     * @return mixed
     */
    public function getDatesForItem($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->join(
                array('n' => 'Notes'),
                'Editions_Release_Dates.Note_ID = n.Note_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $select->join(
                array('eds' => 'Editions'),
                'Editions_Release_Dates.Edition_ID = eds.Edition_ID'
            );
            $select->join(array('i' => 'Items'), 'eds.Item_ID = i.Item_ID');
            $select->order(array('Year', 'Month', 'Day', 'Edition_Name'));
            $select->where->equalTo('i.Item_ID', $itemID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of dates for the specified edition.
     *
     * @var int $editionID Edition ID
     *
     * @return mixed
     */
    public function getDatesForEdition($editionID)
    {
        $callback = function ($select) use ($editionID) {
            $select->join(
                array('n' => 'Notes'),
                'Editions_Release_Dates.Note_ID = n.Note_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $select->order(array('Year', 'Month', 'Day'));
            $select->where->equalTo('Edition_ID', $editionID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of items sorted by publication date.
     *
     * @return mixed
     */
    public function getItemsByYear()
    {
        $callback = function ($select) {
            $select->join(
                array('eds' => 'Editions'),
                'Editions_Release_Dates.Edition_ID = eds.Edition_ID'
            );
            $select->join(
                array('iat' => 'Items_AltTitles'),
                'eds.Preferred_Item_AltName_ID = iat.Sequence_ID',
                array('Item_AltName'), Select::JOIN_LEFT
            );
            $select->join(array('i' => 'Items'), 'eds.Item_ID = i.Item_ID');
            $select->join(
                array('n' => 'Notes'),
                'Editions_Release_Dates.Note_ID = n.Note_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $select->order(array('Year', 'Item_Name', 'Edition_Name'));
        };
        return $this->select($callback);
    }
}
