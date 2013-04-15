<?php
/**
 * Table Definition for Recent_Reviews
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
 * Table Definition for Recent_Reviews
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class RecentReviews extends Gateway
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('Recent_Reviews');
    }

    /**
     * Get recent item reviews
     *
     * @return mixed
     */
    public function getRecentItemReviews()
    {
        $callback = function ($select) {
            $select->join(
                array('u' => 'Users'),
                'Recent_Reviews.User_ID = u.User_ID'
            );
            $select->join(
                array('i' => 'Items'),
                'Recent_Reviews.Item_ID = i.Item_ID'
            );
            $select->where->equalTo('Type', 'item');
            $select->order(array('Added desc', 'Username'));
        };
        return $this->select($callback);
    }

    /**
     * Get recent series comments
     *
     * @return mixed
     */
    public function getRecentSeriesComments()
    {
        $callback = function ($select) {
            $select->join(
                array('u' => 'Users'),
                'Recent_Reviews.User_ID = u.User_ID'
            );
            $select->join(
                array('s' => 'Series'),
                'Recent_Reviews.Item_ID = s.Series_ID'
            );
            $select->where->equalTo('Type', 'series');
            $select->order(array('Added desc', 'Username'));
        };
        return $this->select($callback);
    }
}
