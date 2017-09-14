<?php
/**
 * Edit user controller
 *
 * PHP version 5
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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\Controller;

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
            'user', 'users', 'geeby-deeby/edit-user/render-users', 'User_Editor'
        );
        return $view;
    }

    /**
     * Operate on a single user
     *
     * @return mixed
     */
    public function indexAction()
    {
        $assignMap = array(
            'username' => 'Username',
        );
        $view = $this->handleGenericItem('user', $assignMap, 'user', 'User_Editor');
        // Add extra fields/controls if outside of a lightbox:
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $view->setTemplate('geeby-deeby/edit-user/edit-full');
        }
        return $view;
    }
}
