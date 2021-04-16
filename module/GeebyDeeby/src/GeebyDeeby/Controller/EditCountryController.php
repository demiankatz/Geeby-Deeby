<?php
/**
 * Edit country controller
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
 * Edit country controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditCountryController extends AbstractBase
{
    /**
     * Display a list of countries
     *
     * @return mixed
     */
    public function listAction()
    {
        $view = $this->getGenericList(
            'country', 'countries', 'geeby-deeby/edit-country/render-countries'
        );
        // If this is not an AJAX request, we also want to display cities:
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $view->cities
                = $this->forwardTo(__NAMESPACE__ . '\EditCity', 'list')->cities;
        }
        return $view;
    }

    /**
     * Operate on a single category
     *
     * @return mixed
     */
    public function indexAction()
    {
        $assignMap = ['country' => 'Country_Name'];
        [$view, $ok] = $this->handleGenericItem('country', $assignMap, 'country');
        // Add extra fields/controls if outside of a lightbox:
        if ($ok && !$this->getRequest()->isXmlHttpRequest()) {
            $view->uris = $this->getDbTable('countriesuris')
                ->getURIsForCountry($view->countryObj->Country_ID);
            $view->setTemplate('geeby-deeby/edit-country/edit-full');
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
            'countriesuris', 'Country_ID', 'URI',
            'uris', 'getURIsForCountry',
            'geeby-deeby/edit-country/uri-list.phtml',
            $extras
        );
    }
}
