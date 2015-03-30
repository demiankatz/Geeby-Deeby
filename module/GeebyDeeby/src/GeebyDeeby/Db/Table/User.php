<?php
/**
 * Table Definition for Users
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
use Zend\Crypt\Password\Bcrypt;

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
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('Users', 'GeebyDeeby\Db\Row\User');
    }

    /**
     * Get a list of unapproved users.
     *
     * @return mixed
     */
    public function getUnapproved()
    {
        $callback = function ($select) {
            // Don't select all fields -- no need to risk exposing password data!
            $select->columns(
                array('User_ID', 'Username', 'Name', 'Address', 'Person_ID')
            );
            $select->where->equalTo('Person_ID', 0);
            $select->order('Username');
        };
        return $this->select($callback);
    }

    /**
     * Get a list of users.
     *
     * @return mixed
     */
    public function getList()
    {
        $callback = function ($select) {
            // Don't select all fields -- no need to risk exposing password data!
            $select->columns(
                array('User_ID', 'Username', 'Name', 'Address', 'Person_ID')
            );
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
        $bcrypt = new Bcrypt();
        return ($row && $bcrypt->verify($password, $row->Password_Hash))
            ? $row : null;
    }
}