<?php

/**
 * Edit person controller
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

use GeebyDeeby\Db\Service\AuthorityService;
use GeebyDeeby\Db\Service\PeopleUriService;
use GeebyDeeby\Db\Service\PersonService;
use GeebyDeeby\Db\Service\PredicateService;
use GeebyDeeby\Db\Service\PseudonymService;
use GeebyDeeby\Db\Service\RoleService;

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
            PersonService::class,
            'people',
            'geeby-deeby/edit-person/render-people'
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
        $assignMap = [
            'first' => 'setFirstName',
            'last' => 'setLastName',
            'extra' => 'setExtraDetails',
            'bio' => 'setBiography',
            'authority' => 'setAuthority',
        ];
        [$view, $ok] = $this->handleGenericItem(PersonService::class, $assignMap, 'person');
        if (!$ok) {
            return $view;
        }
        $view->authorities = $this->authoritylistAction()->authorities;
        // Add extra fields/controls if outside of a lightbox:
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $personId = $view->affectedEntity->getId();
            $pseudoService = $this->getDbService(PseudonymService::class);
            $view->pseudonyms = $pseudoService->getPseudonyms($personId);
            $view->realnames = $pseudoService->getRealNames($personId);
            $view->uris = $this->getDbService(PeopleUriService::class)->getURIsForPerson($view->affectedEntity);
            $view->setTemplate('geeby-deeby/edit-person/edit-full');
            $view->predicates = $this->getDbService(PredicateService::class)->getList();
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
        $extras = ($pid = $this->params()->fromPost('predicate_id')) ? ['setPredicate' => $pid] : [];
        return $this->handleGenericLink(
            PeopleUriService::class,
            'setPerson',
            'setUri',
            'uris',
            'getURIsForPerson',
            'geeby-deeby/edit-person/uri-list.phtml',
            $extras,
            retrieveLinkMethod: 'getByPersonAndUri'
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
            PseudonymService::class,
            'setRealPerson',
            'setPseudoPerson',
            'pseudonyms',
            'getPseudonyms',
            'geeby-deeby/edit-person/pseudonym-list.phtml',
            retrieveLinkMethod: 'getByRealPersonAndPseudonym'
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
            PseudonymService::class,
            'setPseudoPerson',
            'setRealPerson',
            'realnames',
            'getRealNames',
            'geeby-deeby/edit-person/realname-list.phtml',
            retrieveLinkMethod: 'getByRealPersonAndPseudonym',
            invertRetrieveLinkParams: true
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
            AuthorityService::class,
            'authorities',
            'geeby-deeby/edit-person/render-authorities'
        );
    }

    /**
     * Operate on a single authority
     *
     * @return mixed
     */
    public function authorityAction()
    {
        $assignMap = ['authority' => 'setAuthorityName'];
        [$response] = $this->handleGenericItem(AuthorityService::class, $assignMap, 'authority');
        return $response;
    }

    /**
     * Display a list of roles
     *
     * @return mixed
     */
    public function rolelistAction()
    {
        return $this->getGenericList(
            RoleService::class,
            'roles',
            'geeby-deeby/edit-person/render-roles'
        );
    }

    /**
     * Operate on a single role
     *
     * @return mixed
     */
    public function roleAction()
    {
        $assignMap = [
            'role' => 'setRoleName',
            'Item_Creator_Predicate' => 'setItemCreatorPredicate',
            'Edition_Credit_Predicate' => 'setEditionCreditPredicate',
        ];
        [$response] = $this->handleGenericItem(RoleService::class, $assignMap, 'role');
        return $response;
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
                'extra' => $this->params()->fromRoute('extra'),
            ]
        );
    }
}
