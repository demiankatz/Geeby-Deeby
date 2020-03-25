<?php
/**
 * Table Definition for Series_Reviews
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
 * Table Definition for Series_Reviews
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesReviews extends Gateway
{
    /**
     * Should we disable logging for this class?
     *
     * @var bool
     */
    protected static $doNotLog = true;

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
        parent::__construct($adapter, $tm, $rowObj, 'Series_Reviews');
    }

    /**
     * Get a list of reviews for the specified series.
     *
     * @param int    $seriesID Series ID
     * @param string $approved 'y' to get only approved items, 'n' for only
     * unapproved items, null for all items
     *
     * @return mixed
     */
    public function getReviewsForSeries($seriesID, $approved = 'y')
    {
        $callback = function ($select) use ($seriesID, $approved) {
            $select->order('Username');
            $select->join(
                ['u' => 'Users'],
                'Series_Reviews.User_ID = u.User_ID'
            );
            if (null !== $approved) {
                $select->where->equalTo('Approved', $approved);
            }
            $select->where->equalTo('Series_ID', $seriesID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of series reviewed by the specified user.
     *
     * @param int    $userID   User ID (null for all reviews)
     * @param string $approved 'y' to get only approved items, 'n' for only
     * unapproved items, null for all items
     *
     * @return mixed
     */
    public function getReviewsByUser($userID, $approved = 'y')
    {
        $callback = function ($select) use ($userID, $approved) {
            $select->join(
                ['s' => 'Series'],
                'Series_Reviews.Series_ID = s.Series_ID'
            );
            // If we don't already have a user in mind, let's pull in extra
            // user details in case we need them:
            if (null === $userID) {
                $select->join(
                    ['u' => 'Users'], 'Series_Reviews.User_ID = u.User_ID'
                );
            }
            $select->order(
                ['Series_Name', 's.Series_ID']
            );
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
