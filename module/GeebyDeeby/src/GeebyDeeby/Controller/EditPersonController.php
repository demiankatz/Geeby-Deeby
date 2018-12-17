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
        // If this is not an AJAX request, we also want to display roles
        // and authorities:
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $view->roles = $this->rolelistAction()->roles;
            $view->authorities = $this->authoritylistAction()->authorities;
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
            'last' => 'Last_Name', 'extra' => 'Extra_Details',
            'bio' => 'Biography', 'authority' => 'Authority_ID'
        );
        $view = $this->handleGenericItem('person', $assignMap, 'person');
        $view->authorities = $this->authoritylistAction()->authorities;
        // Add extra fields/controls if outside of a lightbox:
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $view->pseudonyms = $this->getDbTable('pseudonyms')
                ->getPseudonyms($view->personObj->Person_ID);
            $view->realnames = $this->getDbTable('pseudonyms')
                ->getRealNames($view->personObj->Person_ID);
            $view->uris = $this->getDbTable('peopleuris')
                ->getURIsForPerson($view->personObj->Person_ID);
            $view->setTemplate('geeby-deeby/edit-person/edit-full');
            $view->predicates = $this->getDbTable('predicate')->getList();
        }
        return $view;
    }

    /**
     * Deal with URIs
     *
     * @return mixed
     */
    public function uriAction()
    {
        $extras = ($pid = $this->params()->fromPost('predicate_id'))
            ? ['Predicate_ID' => $pid] : [];
        return $this->handleGenericLink(
            'peopleuris', 'Person_ID', 'URI',
            'uris', 'getURIsForPerson',
            'geeby-deeby/edit-person/uri-list.phtml',
            $extras
        );
    }

    /**
     * Deal with pseudonyms
     *
     * @return mixed
     */
    public function aliaspseudonymAction()
    {
        return $this->handleGenericLink(
            'pseudonyms', 'Real_Person_ID', 'Pseudo_Person_ID',
            'pseudonyms', 'getPseudonyms',
            'geeby-deeby/edit-person/pseudonym-list.phtml'
        );
    }

    /**
     * Deal with real names
     *
     * @return mixed
     */
    public function aliasrealnameAction()
    {
        return $this->handleGenericLink(
            'pseudonyms', 'Pseudo_Person_ID', 'Real_Person_ID',
            'realnames', 'getRealNames',
            'geeby-deeby/edit-person/realname-list.phtml'
        );
    }

    /**
     * Display a list of authorities
     *
     * @return mixed
     */
    public function authoritylistAction()
    {
        return $this->getGenericList(
            'authority', 'authorities', 'geeby-deeby/edit-person/render-authorities'
        );
    }

    /**
     * Operate on a single authority
     *
     * @return mixed
     */
    public function authorityAction()
    {
        $assignMap = array('authority' => 'Authority_Name');
        return $this->handleGenericItem('authority', $assignMap, 'authority');
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

    /**
     * Show action -- allows tolerance of URLs where the user has inserted 'edit'
     * into an existing front-end link.
     *
     * @return mixed
     */
    public function showAction()
    {
        return $this->redirect()->toRoute(
            'edit/person',
            [
                'action' => 'index',
                'id' => $this->params()->fromRoute('id'),
                'extra' => $this->params()->fromRoute('extra')
            ]
        );
    }
}
