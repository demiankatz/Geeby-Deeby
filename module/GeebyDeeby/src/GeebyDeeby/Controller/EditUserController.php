<?php

/**
 * Edit user controller
 *
 * PHP version 8
 *
 * Copyright (C) Demian Katz 2017.
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

use GeebyDeeby\Crypt\PasswordHasher;
use GeebyDeeby\Db\Service\PersonService;
use GeebyDeeby\Db\Service\UserGroupService;
use GeebyDeeby\Db\Service\UserService;

/**
 * Edit user controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditUserController extends AbstractBase
{
    /**
     * Display a list of users
     *
     * @return mixed
     */
    public function listAction()
    {
        $view = $this->getGenericList(
            UserService::class,
            'users',
            'geeby-deeby/edit-user/render-users',
            'User_Editor'
        );
        // If this is not an AJAX request, we also want to display groups:
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $view->usergroups = $this->usergrouplistAction()->usergroups;
        }
        return $view;
    }

    /**
     * Operate on a single user
     *
     * @return mixed
     */
    public function indexAction()
    {
        // Validate password:
        $password = $this->params()->fromPost('password');
        if ($this->params()->fromPost('password_confirm') !== $password) {
            return $this->jsonDie('Passwords must match!');
        }

        // Process standard values:
        $assignMap = [
            'username' => 'setUsername',
            'name' => 'setName',
            'address' => 'setAddress',
            'approved' => 'setIsApproved',
            'person_id' => 'setPerson',
            'group_id' => 'setUserGroup',
        ];
        [$view, $ok]
            = $this->handleGenericItem(UserService::class, $assignMap, 'user', 'User_Editor');
        if (!$ok) {
            return $view;
        }

        // Add associated person details, if necessary:
        if (isset($view->user) && $view->user['Person_ID'] > 0) {
            $person = $this->getDbService(PersonService::class)->getByPrimaryKey($view->user['Person_ID']);
            if ($person) {
                $view->person = $person->toArray();
            }
        }

        // Change password, if necessary:
        if ($password && isset($view->affectedEntity)) {
            $hasher = new PasswordHasher();
            $view->affectedEntity->setPasswordHash($hasher->create($password));
            $this->getDbService(UserService::class)->persistEntity($view->affectedEntity);
        }

        // Load group list:
        $view->usergroups = $this->usergrouplistAction()->usergroups;

        // Add extra fields/controls if outside of a lightbox:
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $view->setTemplate('geeby-deeby/edit-user/edit-full');
        }
        return $view;
    }

    /**
     * Display a list of groups
     *
     * @return mixed
     */
    public function usergrouplistAction()
    {
        return $this->getGenericList(
            UserGroupService::class,
            'usergroups',
            'geeby-deeby/edit-user/render-usergroups',
            'User_Editor'
        );
    }

    /**
     * Operate on a single group
     *
     * @return mixed
     */
    public function usergroupAction()
    {
        $assignMap = [
            'name' => 'setGroupName',
            'content_editor' => 'setIsContentEditor',
            'user_editor' => 'setIsUserEditor',
            'approver' => 'setIsApprover',
            'data_manager' => 'setIsDataManager',
        ];
        [$response] = $this->handleGenericItem(UserGroupService::class, $assignMap, 'usergroup', 'User_Editor');
        return $response;
    }
}
