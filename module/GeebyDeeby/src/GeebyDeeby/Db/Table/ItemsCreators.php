<?php
/**
 * Table Definition for Items_Creators
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2018.
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
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;

/**
 * Table Definition for Items_Creators
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsCreators extends Gateway
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('Items_Creators');
    }

    /**
     * Get a list of credits attached to the specified person.
     *
     * @var int    $personID Person ID
     * @var string $sort     Type of sorting (series/title/year)
     *
     * @return mixed
     */
    public function getCitationsForPerson($personID, $sort = 'series')
    {
        // Special case: bringing series into the mix makes things more complex:
        if ($sort == 'series') {
            return $this->getSeriesCitationsForPerson($personID);
        }
        return $this->getItemCitationsForPerson($personID, $sort);
    }

    /**
     * Get a list of credits attached to the specified person, sorted by
     * item.
     *
     * @var int    $personID Person ID
     * @var string $sort     Type of sorting (title or year)
     *
     * @return mixed
     */
    public function getItemCitationsForPerson($personID, $sort = 'title')
    {
        $callback = function ($select) use ($personID, $sort) {
            $count = new Expression(
                'count(distinct(?))', array('icc.Citation_ID'),
                array(Expression::TYPE_IDENTIFIER)
            );
            $select->join(
                array('icc' => 'Items_Creators_Citations'),
                'Items_Creators.Item_Creator_ID = icc.Item_Creator_ID',
                array('Citation_Count' => $count), Select::JOIN_LEFT
            );
            $select->join(
                array('i' => 'Items'), 'Items_Creators.Item_ID = i.Item_ID'
            );
            $select->join(
                array('eds' => 'Editions'), 'eds.Item_ID = i.Item_ID'
            );
            $year = new Expression(
                'min(?)', array('erd.Year'),
                array(Expression::TYPE_IDENTIFIER)
            );
            $select->join(
                array('erd' => 'Editions_Release_Dates'),
                'eds.Edition_ID = erd.Edition_ID',
                array('Earliest_Year' => $year), Select::JOIN_LEFT
            );
            $select->join(
                array('r' => 'Roles'),
                'Items_Creators.Role_ID = r.Role_ID'
            );
            $sortFields = $sort === 'year'
                ? array('Role_Name', 'Earliest_Year', 'Item_Name')
                : array('Role_Name', 'Item_Name', 'Earliest_Year');
            $select->order($sortFields);
            $select->group(array('Role_Name', 'Item_Name'));
            $select->where->equalTo('Person_ID', $personID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of credits attached to the specified person, sorted by
     * series.
     *
     * @var int $personID Person ID
     *
     * @return mixed
     */
    public function getSeriesCitationsForPerson($personID)
    {
        $callback = function ($select) use ($personID) {
            $select->join(
                array('i' => 'Items'), 'Items_Creators.Item_ID = i.Item_ID'
            );
            $select->join(
                array('eds' => 'Editions'), 'eds.Item_ID = i.Item_ID',
                array('Edition_Name', 'Volume', 'Position', 'Replacement_Number')
            );
            $select->join(
                array('iat' => 'Items_AltTitles'),
                'eds.Preferred_Item_AltName_ID = iat.Sequence_ID',
                array('Item_AltName'), Select::JOIN_LEFT
            );
            $select->join(
                array('s' => 'Series'), 'eds.Series_ID = s.Series_ID'
            );
            $select->join(
                array('r' => 'Roles'),
                'Items_Creators.Role_ID = r.Role_ID'
            );
            $fields = array(
                'Role_Name', 'Series_Name', 's.Series_ID', 'eds.Volume',
                'eds.Position', 'eds.Replacement_Number', 'Item_Name'
            );
            $select->order($fields);
            $select->group($fields);
            $select->where->equalTo('Person_ID', $personID);
        };
        return $this->select($callback);
    }

    /**
     * Given an item identifier, return a list of creators.
     *
     * @param int $itemID Item ID
     *
     * @return mixed
     */
    public function getCreatorsForItem($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->join(
                array('p' => 'People'),
                'Items_Creators.Person_ID = p.Person_ID'
            );
            $select->join(
                array('r' => 'Roles'),
                'Items_Creators.Role_ID = r.Role_ID'
            );
            $fields = array(
                'Role_Name', 'Last_Name', 'First_Name', 'Middle_Name'
            );
            $select->order($fields);
            $select->where->equalTo('Item_ID', $itemID);
        };
        return $this->select($callback);
    }
}
