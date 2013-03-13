<?php
/**
 * Table Definition for Collections
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

/**
 * Table Definition for Collections
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class Collections extends Gateway
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('Collections');
    }

    /**
     * Get a list of users owning/interested in an item.
     *
     * @param int    $itemID Item ID
     * @param string $type   List type ('extra', 'have', 'want' or null for all)
     *
     * @return mixed
     */
    public function getForItem($itemID, $type = null)
    {
        $callback = function ($select) use ($itemID, $type) {
            $select->join(
                array('u' => 'Users'),
                'Collections.User_ID = u.User_ID'
            );
            $select->order(array('Username'));
            $select->where->equalTo('Item_ID', $itemID);
            if (null !== $type) {
                $select->where->equalTo('Collection_Status', $type);
            }
        };
        return $this->select($callback);
    }

    /**
     * Get a list of items on a user's collection list(s).
     *
     * @param int    $userID User ID
     * @param string $type   List type ('extra', 'have', 'want' or null for all)
     *
     * @return mixed
     */
    public function getForUser($userID, $type = null)
    {
        $callback = function ($select) use ($userID, $type) {
            $select->join(
                array('i' => 'Items'),
                'Collections.Item_ID = i.Item_ID'
            );
            $select->join(
                array('iis' => 'Items_In_Series'),
                'i.Item_ID = iis.Item_ID'
            );
            $select->join(
                array('s' => 'Series'),
                'iis.Series_ID = s.Series_ID AND Collections.Series_ID = s.Series_ID'
            );
            $select->order(
                array('Series_Name', 's.Series_ID', 'Position', 'Item_Name')
            );
            $select->where->equalTo('Collections.User_ID', $userID);
            if (null !== $type) {
                $select->where->equalTo('Collection_Status', $type);
            }
        };
        return $this->select($callback);
    }
}
