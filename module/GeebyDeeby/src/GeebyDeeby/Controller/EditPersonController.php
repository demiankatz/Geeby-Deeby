<?php
/**
 * Edit person controller
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
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\Controller;

/**
 * Edit person controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditPersonController extends AbstractBase
{
    /**
     * Display a list of people
     *
     * @return mixed
     */
    public function listAction()
    {
        $view = $this->getGenericList(
            'person', 'people', 'geeby-deeby/edit-person/render-people'
        );
        // If this is not an AJAX request, we also want to display roles:
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $view->roles = $this->rolelistAction()->roles;
        }
        return $view;
    }

    /**
     * Operate on a single person
     *
     * @return mixed
     */
    public function indexAction()
    {
        $assignMap = array(
            'first' => 'First_Name', 'middle' => 'Middle_Name',
            'last' => 'Last_Name', 'bio' => 'Biography'
        );
        return $this->handleGenericItem('person', $assignMap, 'person');
    }

    /**
     * Display a list of roles
     *
     * @return mixed
     */
    public function rolelistAction()
    {
        return $this->getGenericList(
            'role', 'roles', 'geeby-deeby/edit-person/render-roles'
        );
    }

    /**
     * Operate on a single role
     *
     * @return mixed
     */
    public function roleAction()
    {
        $assignMap = array('role' => 'Role_Name');
        return $this->handleGenericItem('role', $assignMap, 'role');
    }
}
