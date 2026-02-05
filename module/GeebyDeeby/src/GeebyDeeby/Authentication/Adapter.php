<?php

/**
 * GeebyDeeby Authentication Adapter
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
 * @package  Authentication
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Authentication;

use GeebyDeeby\Db\Service\UserService;
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
     * Constructor
     *
     * @param UserService $service  Database service
     * @param string      $username Username
     * @param string      $password Password
     */
    public function __construct(protected UserService $service, protected string $username, protected string $password)
    {
    }

    /**
     * Performs an authentication attempt
     *
     * @return Result
     * @throws \Laminas\Authentication\Adapter\Exception\ExceptionInterface
     */
    public function authenticate()
    {
        $user = $this->service->passwordLogin($this->username, $this->password);
        if ($user && !$user->isApproved()) {
            throw new UnapprovedUserException('Unapproved User');
        }
        return new Result(
            $user ? Result::SUCCESS : Result::FAILURE,
            $user ? $user->getId() : null
        );
    }
}
