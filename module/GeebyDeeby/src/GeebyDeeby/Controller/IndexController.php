<?php

/**
 * Index controller
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

use DateTime;
use GeebyDeeby\Db\Service\UserService;

/**
 * Index controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class IndexController extends AbstractBase
{
    /**
     * Default home page
     *
     * @return mixed
     */
    public function indexAction()
    {
        return $this->createViewModel();
    }

    /**
     * Login form
     *
     * @return mixed
     */
    public function loginAction()
    {
        $view = $this->createViewModel();
        if ($this->getRequest()->isPost()) {
            try {
                $result = $this->getAuth()->authenticate(
                    $this->getAuthenticationAdapter(
                        $this->params()->fromPost('user'),
                        $this->params()->fromPost('pass')
                    )
                );
            } catch (\GeebyDeeby\Authentication\UnapprovedUserException $e) {
                $view->msg = 'Your account has not been approved yet.';
                return $view;
            }
            if ($result->isValid()) {
                $userService = $this->getDbService(UserService::class);
                $user = $userService->getByPrimaryKey($result->getIdentity())->setLastLoginDate(new DateTime());
                $userService->persistEntity($user);
                $followup = $this->followup()->retrieve();
                if (isset($followup->url)) {
                    $url = $followup->url;
                    unset($followup->url);
                    return $this->redirect()->toUrl($url);
                }
                return $this->redirect()->toRoute('home');
            }
            $view->msg = 'Invalid credentials; please try again.';
        } else {
            $followup = $this->followup()->retrieve();

            // Never redirect back to the login screen after login!
            if ($followup->url == $this->getServerUrl()) {
                unset($followup->url);
            }

            // Set followup to referer if not already set:
            $referer = $this->getRequest()->getServer()->get('HTTP_REFERER');
            if (!empty($referer) && !isset($followup->url)) {
                $this->followup()->store([], $referer);
            }
        }
        return $view;
    }

    /**
     * Logout action
     *
     * @return mixed
     */
    public function logoutAction()
    {
        $this->getAuth()->clearIdentity();
        $referer = $this->getRequest()->getServer()->get('HTTP_REFERER');
        return empty($referer)
            ? $this->redirect()->toRoute('home')
            : $this->redirect()->toUrl($referer);
    }
}
