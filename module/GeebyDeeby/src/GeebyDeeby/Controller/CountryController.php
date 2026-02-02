<?php

/**
 * Country controller
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

use GeebyDeeby\Db\Service\CountriesUriService;
use GeebyDeeby\Db\Service\CountryService;
use GeebyDeeby\Db\Service\SeriesPublisherService;

use function is_object;

/**
 * Country controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class CountryController extends AbstractBase
{
    /**
     * 303 redirect page
     *
     * @return mixed
     */
    public function indexAction()
    {
        return $this->performRdfRedirect('country');
    }

    /**
     * Build the primary resource in an RDF graph.
     *
     * @param \EasyRdf\Graph $graph Graph to populate
     * @param object         $view  View model populated with information.
     * @param mixed          $class Class(es) for resource.
     *
     * @return \EasyRdf\Resource
     */
    protected function addPrimaryResourceToGraph(
        $graph,
        $view,
        $class = 'skos:Concept'
    ) {
        $uri = $this->getServerUrl(
            'country',
            ['id' => $view->country['Country_ID']]
        );
        $country = $graph->resource($uri, $class);
        $country->set('rdf:label', $view->country['Country_Name']);
        foreach ($view->uris as $uri) {
            $country->add($uri['Predicate'], $graph->resource($uri['URI']));
        }
        return $country;
    }

    /**
     * Build an RDF graph from the available data.
     *
     * @param object $view View model populated with information.
     *
     * @return \EasyRdf\Graph
     */
    protected function getGraphFromView($view)
    {
        $graph = new \EasyRdf\Graph();
        $this->addPrimaryResourceToGraph($graph, $view);
        return $graph;
    }

    /**
     * RDF representation page
     *
     * @return mixed
     */
    public function rdfAction()
    {
        $view = $this->getCountryViewModel();
        if (!is_object($view)) {
            $response = $this->getResponse();
            $response->setStatusCode(404);
            return $response;
        }

        return $this->getRdfResponse($this->getGraphFromView($view));
    }

    /**
     * "Show country" page
     *
     * @return mixed
     */
    public function showAction()
    {
        $view = $this->getCountryViewModel();
        return $view
            ? $view : $this->forwardTo(__NAMESPACE__ . '\Country', 'notfound');
    }

    /**
     * Get view model for country (or return false if not found).
     *
     * @return mixed
     */
    public function getCountryViewModel()
    {
        $id = $this->params()->fromRoute('id');
        $entity = (null === $id) ? null : $this->getDbService(CountryService::class)->getByPrimaryKey($id);
        if (!is_object($entity)) {
            return false;
        }
        $view = $this->createViewModel(
            ['country' => $entity->toArray()]
        );
        $view->series = $this->getDbService(SeriesPublisherService::class)->getSeriesForCountry($id);
        $view->uris = $this->getDbService(CountriesUriService::class)->getURIsForCountry($id);
        return $view;
    }

    /**
     * Country list
     *
     * @return mixed
     */
    public function listAction()
    {
        return $this->createViewModel(
            ['countries' => $this->getDbService(CountryService::class)->getList()]
        );
    }

    /**
     * Not found page
     *
     * @return mixed
     */
    public function notfoundAction()
    {
        return $this->createViewModel();
    }
}
