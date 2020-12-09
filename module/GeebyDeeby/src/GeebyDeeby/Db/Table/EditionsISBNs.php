<?php
/**
 * Table Definition for Editions_ISBNs
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
use Laminas\Db\Sql\Select;

/**
 * Table Definition for Editions_ISBNs
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsISBNs extends Gateway
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
        parent::__construct($adapter, $tm, $rowObj, 'Editions_ISBNs');
    }

    /**
     * Get a list of ISBNs for the specified edition.
     *
     * @param int $editionID Edition ID
     *
     * @return mixed
     */
    public function getISBNsForEdition($editionID)
    {
        $callback = function ($select) use ($editionID) {
            $select->join(
                ['n' => 'Notes'],
                'Editions_ISBNs.Note_ID = n.Note_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $select->order('ISBN13');
            $select->where->equalTo('Edition_ID', $editionID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of ISBNs for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return mixed
     */
    public function getISBNsForItem($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->join(
                ['n' => 'Notes'],
                'Editions_ISBNs.Note_ID = n.Note_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $select->join(
                ['eds' => 'Editions'],
                'Editions_ISBNs.Edition_ID = eds.Edition_ID'
            );
            $select->order('ISBN13');
            $select->where->equalTo('Item_ID', $itemID);
        };
        return $this->select($callback);
    }

    /**
     * Find items matching an ISBN search query.
     *
     * @param string $q Query
     *
     * @return mixed
     */
    public function searchForItems($q)
    {
        $callback = function ($select) use ($q) {
            $select->join(
                ['eds' => 'Editions'],
                'Editions_ISBNs.Edition_ID = eds.Edition_ID'
            );
            $select->join(
                ['i' => 'Items'], 'eds.Item_ID = i.Item_ID'
            );
            $select->order('Item_Name');
            $select->group('i.Item_ID');
            $select->where->like('ISBN', $q . '%');
            $select->where->OR->like('ISBN13', $q . '%');
        };
        return $this->select($callback);
    }
}
