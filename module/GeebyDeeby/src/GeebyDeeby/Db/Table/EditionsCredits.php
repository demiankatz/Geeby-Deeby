<?php
/**
 * Table Definition for Editions_Credits
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
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
 * Table Definition for Editions_Credits
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsCredits extends Gateway
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('Editions_Credits');
    }

    /**
     * Get a list of credits attached to the specified person.
     *
     * @var int $personID Person ID
     *
     * @return mixed
     */
    public function getCreditsForPerson($personID)
    {
        $callback = function ($select) use ($personID) {
            $select->join(
                array('eds' => 'Editions'),
                'Editions_Credits.Edition_ID = eds.Edition_ID',
                array('Edition_Name', 'Position')
            );
            $select->join(
                array('iat' => 'Items_AltTitles'),
                'eds.Preferred_Item_AltName_ID = iat.Sequence_ID',
                array('Item_AltName'), Select::JOIN_LEFT
            );
            $select->join(
                array('i' => 'Items'), 'eds.Item_ID = i.Item_ID'
            );
            $select->join(
                array('s' => 'Series'), 'eds.Series_ID = s.Series_ID'
            );
            $select->join(
                array('r' => 'Roles'),
                'Editions_Credits.Role_ID = r.Role_ID'
            );
            $select->join(
                array('n' => 'Notes'),
                'Editions_Credits.Note_ID = n.Note_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $fields = array(
                'Role_Name', 'Series_Name', 's.Series_ID', 'eds.Position',
                'Item_Name', 'Note'
            );
            $select->order($fields);
            $select->group($fields);
            $select->where->equalTo('Person_ID', $personID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of credits attached to the specified edition.
     *
     * @var int $editionID Edition ID
     *
     * @return mixed
     */
    public function getCreditsForEdition($editionID)
    {
        $callback = function ($select) use ($editionID) {
            $select->join(
                array('p' => 'People'),
                'Editions_Credits.Person_ID = p.Person_ID'
            );
            $select->join(
                array('r' => 'Roles'),
                'Editions_Credits.Role_ID = r.Role_ID'
            );
            $select->join(
                array('n' => 'Notes'),
                'Editions_Credits.Note_ID = n.Note_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $fields = array(
                'Role_Name', 'Position', 'Last_Name',
                'First_Name', 'Middle_Name'
            );
            $select->order($fields);
            $select->where->equalTo('Edition_ID', $editionID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of credits attached to the specified item.
     *
     * @var int  $itemID Item ID
     * @var bool $group  Should we group by person/role?
     *
     * @return mixed
     */
    public function getCreditsForItem($itemID, $group = false)
    {
        $callback = function ($select) use ($itemID, $group) {
            $select->join(
                array('eds' => 'Editions'),
                'Editions_Credits.Edition_ID = eds.Edition_ID',
                array('Item_ID', 'Edition_Name')
            );
            $select->join(
                array('p' => 'People'),
                'Editions_Credits.Person_ID = p.Person_ID'
            );
            $select->join(
                array('r' => 'Roles'),
                'Editions_Credits.Role_ID = r.Role_ID'
            );
            $select->join(
                array('n' => 'Notes'),
                'Editions_Credits.Note_ID = n.Note_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $fields = array(
                'Role_Name', 'Editions_Credits.Position', 'Last_Name',
                'First_Name', 'Middle_Name'
            );
            $select->order($fields);
            if ($group) {
                $select->group(array('r.Role_ID', 'p.Person_ID', 'n.Note_ID'));
            }
            $select->where->equalTo('Item_ID', $itemID);
        };
        return $this->select($callback);
    }

    /**
     * Delete credits for all editions of an item.
     *
     * @param int   $item  Item ID
     * @param array $where Fields to match
     *
     * @return void
     */
    public function deleteForItem($item, $where)
    {
        $table = $this->getDbTable('edition');
        $eds = $table->getEditionsForItem($item);
        foreach ($eds as $ed) {
            $this->delete(array('Edition_ID' => $ed->Edition_ID) + $where);
        }
    }

    /**
     * Insert credits for all editions of an item.
     *
     * @param int   $item   Item ID
     * @param array $fields Fields to insert
     *
     * @return void
     */
    public function insertForItem($item, $fields)
    {
        $table = $this->getDbTable('edition');
        $eds = $table->getEditionsForItem($item);
        foreach ($eds as $ed) {
            $this->insert(array('Edition_ID' => $ed->Edition_ID) + $fields);
        }
    }

    /**
     * Update credits for all editions of an item.
     *
     * @param int   $item   Item ID
     * @param array $fields Fields to change
     * @param array $where  Fields to match
     *
     * @return void
     */
    public function updateForItem($item, $fields, $where)
    {
        $table = $this->getDbTable('edition');
        $eds = $table->getEditionsForItem($item);
        foreach ($eds as $ed) {
            $this->update($fields, array('Edition_ID' => $ed->Edition_ID) + $where);
        }
    }
}
