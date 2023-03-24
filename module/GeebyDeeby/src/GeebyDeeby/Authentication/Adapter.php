<?php

/**
 * GeebyDeeby Authentication Adapter
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
 * @package  Authentication
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Authentication;

use Laminas\Authentication\Result;

/**
 * GeebyDeeby Authentication Adapter
 *
 * @category GeebyDeeby
 * @package  Authentication
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class Adapter implements \Laminas\Authentication\Adapter\AdapterInterface
{
    /**
     * Database table gateway
     *
     * @var \GeebyDeeby\Db\Table\User
     */
    protected $table;

    /**
     * Username
     *
     * @var string
     */
    protected $username;

    /**
     * Password
     *
     * @var string
     */
    protected $password;

    /**
     * Constructor
     *
     * @param \GeebyDeeby\Db\Table\User $table Database table gateway
     * @param string                    $user  Username
     * @param string                    $pass  Password
     */
    public function __construct(\GeebyDeeby\Db\Table\User $table, $user, $pass)
    {
        $this->table = $table;
        $this->username = $user;
        $this->password = $pass;
    }

    /**
     * Performs an authentication attempt
     *
     * @return Result
     * @throws \Laminas\Authentication\Adapter\Exception\ExceptionInterface
     */
    public function authenticate()
    {
        $user = $this->table->passwordLogin($this->username, $this->password);
        if (is_object($user) && $user->Person_ID === 0) {
            throw new UnapprovedUserException('Unapproved User');
        }
        return new Result(
            is_object($user) ? Result::SUCCESS : Result::FAILURE,
            is_object($user) ? $user->User_ID : null
        );
    }
}
