<?php

/**
 * Edit controller
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
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Controller;

/**
 * Edit controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditController extends AbstractBase
{
    /**
     * Default edit menu
     *
     * @return mixed
     */
    public function indexAction()
    {
        if (!($user = $this->getCurrentUser())) {
            return $this->forceLogin();
        }

        return $this->createViewModel(
            [
                'contentEditor' => $this->userHasPermission($user, 'Content_Editor'),
                'approver' => $this->userHasPermission($user, 'Approver'),
                'userEditor' => $this->userHasPermission($user, 'User_Editor'),
                'dataManager' => $this->userHasPermission($user, 'Data_Manager'),
            ]
        );
    }

    /**
     * Display access denied message
     *
     * @return mixed
     */
    public function deniedAction()
    {
        return $this->createViewModel();
    }
}
