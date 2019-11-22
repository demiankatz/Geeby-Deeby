<?php
/**
 * City controller
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
 * City controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class CityController extends AbstractBase
{
    /**
     * 303 redirect page
     *
     * @return mixed
     */
    public function indexAction()
    {
        return $this->performRdfRedirect('city');
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
    protected function addPrimaryResourceToGraph($graph, $view,
        $class = 'skos:Concept'
    ) {
        $uri = $this->getServerUrl(
            'city', ['id' => $view->city['City_ID']]
        );
        $pub = $graph->resource($uri, $class);
        $pub->set('rdf:label', $view->city['City_Name']);
        return $pub;
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
        $view = $this->getCityViewModel();
        if (!is_object($view)) {
            $response = $this->getResponse();
            $response->setStatusCode(404);
            return $response;
        }

        return $this->getRdfResponse($this->getGraphFromView($view));
     }

    /**
     * "Show city" page
     *
     * @return mixed
     */
    public function showAction()
    {
        $view = $this->getCityViewModel();
        return $view ? $view : $this->forwardTo(__NAMESPACE__ . '\City', 'notfound');
    }

    /**
     * Get view model for city (or return false if not found).
     *
     * @return mixed
     */
    public function getCityViewModel()
    {
        $id = $this->params()->fromRoute('id');
        $table = $this->getDbTable('city');
        $rowObj = (null === $id) ? null : $table->getByPrimaryKey($id);
        if (!is_object($rowObj)) {
            return false;
        }
        $view = $this->createViewModel(
            array('city' => $rowObj->toArray())
        );
        $view->series = $this->getDbTable('seriespublishers')
            ->getSeriesForCity($id);
        return $view;
    }

    /**
     * City list
     *
     * @return mixed
     */
    public function listAction()
    {
        return $this->createViewModel(
            array('cities' => $this->getDbTable('city')->getList())
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
