<?php
/**
 * Publisher controller
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
 * Publisher controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PublisherController extends AbstractBase
{
    /**
     * 303 redirect page
     *
     * @return mixed
     */
    public function indexAction()
    {
        return $this->performRdfRedirect('publisher');
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
        $class = 'foaf:Organization'
    ) {
        $uri = $this->getServerUrl(
            'publisher', ['id' => $view->publisher['Publisher_ID']]
        );
        $pub = $graph->resource($uri, $class);
        foreach ($view->uris as $uri) {
            $pub->add($uri->Predicate, $graph->resource($uri->URI));
        }
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
        $view = $this->getPublisherViewModel();
        if (!is_object($view)) {
            $response = $this->getResponse();
            $response->setStatusCode(404);
            return $response;
        }

        return $this->getRdfResponse($this->getGraphFromView($view));
    }

    /**
     * "Show publisher" page
     *
     * @return mixed
     */
    public function showAction()
    {
        $view = $this->getPublisherViewModel();
        if (!$view) {
            return $this->forwardTo(__NAMESPACE__ . '\Publisher', 'notfound');
        }
        return $view;
    }

    /**
     * Get view model for publisher (or return false if not found).
     *
     * @return mixed
     */
    protected function getPublisherViewModel()
    {
        $id = $this->params()->fromRoute('id');
        $table = $this->getDbTable('publisher');
        $rowObj = (null === $id) ? null : $table->getByPrimaryKey($id);
        if (!is_object($rowObj)) {
            return false;
        }
        $view = $this->createViewModel(
            ['publisher' => $rowObj->toArray()]
        );
        $view->series = $this->getDbTable('seriespublishers')
            ->getSeriesForPublisher($id);
        return $view;
    }

    /**
     * Publisher list
     *
     * @return mixed
     */
    public function listAction()
    {
        return $this->createViewModel(
            ['publishers' => $this->getDbTable('publisher')->getList()]
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
