<?php
/**
 * Table Definition for Editions_OCLC_Numbers
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
 * Table Definition for Editions_OCLC_Numbers
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsOCLCNumbers extends Gateway
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
        parent::__construct($adapter, $tm, $rowObj, 'Editions_OCLC_Numbers');
    }

    /**
     * Get a list of OCLC numbers for the specified edition.
     *
     * @param int $editionID Edition ID
     *
     * @return mixed
     */
    public function getOCLCNumbersForEdition($editionID)
    {
        $callback = function ($select) use ($editionID) {
            $select->join(
                ['n' => 'Notes'],
                'Editions_OCLC_Numbers.Note_ID = n.Note_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $select->order('OCLC_Number');
            $select->where->equalTo('Edition_ID', $editionID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of OCLC numbers for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return mixed
     */
    public function getOCLCNumbersForItem($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->join(
                ['n' => 'Notes'],
                'Editions_OCLC_Numbers.Note_ID = n.Note_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $select->join(
                ['eds' => 'Editions'],
                'Editions_OCLC_Numbers.Edition_ID = eds.Edition_ID'
            );
            $select->order('OCLC_Number');
            $select->where->equalTo('Item_ID', $itemID);
        };
        return $this->select($callback);
    }
}
