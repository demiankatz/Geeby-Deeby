<?php
/**
 * Edit city controller
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
 * Edit city controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditCityController extends AbstractBase
{
    /**
     * Display a list of cities
     *
     * @return mixed
     */
    public function listAction()
    {
        return $this->getGenericList(
            'city', 'cities', 'geeby-deeby/edit-city/render-cities'
        );
    }

    /**
     * Operate on a single city
     *
     * @return mixed
     */
    public function indexAction()
    {
        $assignMap = ['city' => 'City_Name'];
        [$view, $ok] = $this->handleGenericItem('city', $assignMap, 'city');
        if ($ok && !$this->getRequest()->isXmlHttpRequest()) {
            $view->uris = $this->getDbTable('citiesuris')
                ->getURIsForCity($view->cityObj->City_ID);
            $view->setTemplate('geeby-deeby/edit-city/edit-full');
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
            'citiesuris', 'City_ID', 'URI',
            'uris', 'getURIsForCity',
            'geeby-deeby/edit-city/uri-list.phtml',
            $extras
        );
    }
}
