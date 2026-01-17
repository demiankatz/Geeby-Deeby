<?php

/**
 * Table Definition for Users
 *
 * PHP version 8
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
 * along with this program; if not, see
 * <https://www.gnu.org/licenses/>.
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Db\Table;

use GeebyDeeby\Crypt\PasswordHasher;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\RowGateway\RowGateway;

/**
 * Table Definition for Users
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class User extends Gateway
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
     * @param ?RowGateway   $rowObj  Row prototype object (null for default)
     */
    public function __construct(
        Adapter $adapter,
        PluginManager $tm,
        ?RowGateway $rowObj = null
    ) {
        parent::__construct($adapter, $tm, $rowObj, 'Users');
    }

    /**
     * Get a list of unapproved users.
     *
     * @return mixed
     */
    public function getUnapproved()
    {
        $callback = function ($select): void {
            // Don't select all fields -- no need to risk exposing password data!
            $select->columns(
                [
                    'User_ID', 'Username', 'Name', 'Address', 'Person_ID',
                    'Join_Reason',
                ]
            );
            $select->where->equalTo('Approved', 'n');
            $select->order('Username');
        };
        return $this->select($callback);
    }

    /**
     * Get a list of users.
     *
     * @param bool $approved Limit to approved users?
     *
     * @return mixed
     */
    public function getList($approved = true)
    {
        $callback = function ($select) use ($approved): void {
            // Don't select all fields -- no need to risk exposing password data!
            $select->columns(
                ['User_ID', 'Username', 'Name', 'Address', 'Person_ID']
            );
            if ($approved) {
                $select->where->equalTo('Approved', 'y');
            }
            $select->order('Username');
        };
        return $this->select($callback);
    }

    /**
     * Attempt to log in using the specified username and password.  On successful
     * login, the specified user's row will be returned.
     *
     * @param string $username Username for login.
     * @param string $password Password for login.
     *
     * @return mixed           Row on successful login, null otherwise.
     */
    public function passwordLogin($username, $password)
    {
        $row = $this->select(['Username' => $username])->current();
        $hasher = new PasswordHasher();
        return ($row && $hasher->verify($password, $row->Password_Hash))
            ? $row : null;
    }
}
