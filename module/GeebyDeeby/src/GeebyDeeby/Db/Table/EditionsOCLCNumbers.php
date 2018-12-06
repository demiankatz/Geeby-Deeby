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
use Zend\Db\Sql\Select;

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
     */
    public function __construct()
    {
        parent::__construct('Editions_OCLC_Numbers');
    }

    /**
     * Get a list of OCLC numbers for the specified edition.
     *
     * @var int $editionID Edition ID
     *
     * @return mixed
     */
    public function getOCLCNumbersForEdition($editionID)
    {
        $callback = function ($select) use ($editionID) {
            $select->join(
                array('n' => 'Notes'),
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
     * @var int $itemID Item ID
     *
     * @return mixed
     */
    public function getOCLCNumbersForItem($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->join(
                array('n' => 'Notes'),
                'Editions_OCLC_Numbers.Note_ID = n.Note_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $select->join(
                array('eds' => 'Editions'),
                'Editions_OCLC_Numbers.Edition_ID = eds.Edition_ID'
            );
            $select->order('OCLC_Number');
            $select->where->equalTo('Item_ID', $itemID);
        };
        return $this->select($callback);
    }
}
