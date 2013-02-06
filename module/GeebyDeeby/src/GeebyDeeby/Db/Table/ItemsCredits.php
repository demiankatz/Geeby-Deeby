<?php
/**
 * Table Definition for Items_Credits
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
 * Table Definition for Items_Credits
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsCredits extends Gateway
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('Items_Credits');
    }

    /**
     * Get a list of credits attached to the specified item.
     *
     * @var int $itemID Item ID
     *
     * @return mixed
     */
    public function getCreditsForItem($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->join(
                array('p' => 'People'),
                'Items_Credits.Person_ID = p.Person_ID'
            );
            $select->join(
                array('r' => 'Roles'),
                'Items_Credits.Role_ID = r.Role_ID'
            );
            $select->join(
                array('n' => 'Notes'),
                'Items_Credits.Note_ID = n.Note_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $select->order(
                array(
                    'Role_Name', 'Position', 'Last_Name', 'First_Name', 'Middle_Name'
                )
            );
            $select->where->equalTo('Item_ID', $itemID);
        };
        return $this->select($callback);
    }
}
