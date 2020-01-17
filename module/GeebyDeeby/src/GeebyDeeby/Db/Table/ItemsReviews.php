<?php
/**
 * Table Definition for Items_Reviews
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
 * Table Definition for Items_Reviews
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsReviews extends Gateway
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
        parent::__construct($adapter, $tm, $rowObj, 'Items_Reviews');
    }

    /**
     * Get a list of reviews for the specified item.
     *
     * @param int    $itemID   Item ID
     * @param string $approved 'y' to get only approved items, 'n' for only
     * unapproved items, null for all items
     *
     * @return mixed
     */
    public function getReviewsForItem($itemID, $approved = 'y')
    {
        $callback = function ($select) use ($itemID, $approved) {
            $select->order('Username');
            $select->join(
                ['u' => 'Users'],
                'Items_Reviews.User_ID = u.User_ID'
            );
            if (null !== $approved) {
                $select->where->equalTo('Approved', $approved);
            }
            $select->where->equalTo('Item_ID', $itemID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of item IDs reviewed by the specified user.
     *
     * @param int    $userID   User ID
     * @param string $approved 'y' to get only approved items, 'n' for only
     * unapproved items, null for all items
     *
     * @return mixed
     */
    public function getReviewIDsByUser($userID, $approved = 'y')
    {
        $callback = function ($select) use ($userID, $approved) {
            if (null !== $approved) {
                $select->where->equalTo('Approved', $approved);
            }
            $select->where->equalTo('User_ID', $userID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of items reviewed by the specified user.
     *
     * @param int    $userID   User ID (null for all users)
     * @param string $approved 'y' to get only approved items, 'n' for only
     * unapproved items, null for all items
     * @param bool   $series   Include series information in result set?
     *
     * @return mixed
     */
    public function getReviewsByUser($userID, $approved = 'y', $series = true)
    {
        $callback = function ($select) use ($userID, $approved, $series) {
            $select->join(
                ['i' => 'Items'],
                'Items_Reviews.Item_ID = i.Item_ID'
            );
            if ($series) {
                $select->join(
                    ['eds' => 'Editions'],
                    'i.Item_ID = eds.Item_ID'
                );
                $select->join(
                    ['iat' => 'Items_AltTitles'],
                    'eds.Preferred_Item_AltName_ID = iat.Sequence_ID',
                    ['Item_AltName'], Select::JOIN_LEFT
                );
                $select->join(
                    ['s' => 'Series'],
                    'eds.Series_ID = s.Series_ID'
                );
                $select->group(
                    [
                        'Items_Reviews.Item_ID', 'Items_Reviews.User_ID',
                        's.Series_ID', 'eds.Volume', 'eds.Position',
                        'eds.Replacement_Number'
                    ]
                );
            }
            // If we don't already have a user in mind, let's pull in extra
            // user details in case we need them:
            if (null === $userID) {
                $select->join(
                    ['u' => 'Users'], 'Items_Reviews.User_ID = u.User_ID'
                );
            }
            // Different sort settings based on whether or not series are included:
            $all = [
                'Series_Name', 's.Series_ID', 'eds.Volume', 'eds.Position'
                 'eds.Replacement_Number', 'Item_Name'
            ];
            $select->order($series ? $all : ['Item_Name']);
            if (null !== $approved) {
                $select->where->equalTo('Approved', $approved);
            }
            if (null !== $userID) {
                $select->where->equalTo('User_ID', $userID);
            }
        };
        return $this->select($callback);
    }
}
