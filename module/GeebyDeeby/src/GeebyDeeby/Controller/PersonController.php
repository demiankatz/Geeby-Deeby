<?php
/**
 * Person controller
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
 * Person controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PersonController extends AbstractBase
{
    /**
     * 303 redirect page
     *
     * @return mixed
     */
    public function indexAction()
    {
        return $this->performRdfRedirect('person');
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
        $class = 'foaf:Person'
    ) {
        $id = $view->person['Person_ID'];
        $uri = $this->getServerUrl('person', ['id' => $id]);
        $person = $graph->resource($uri, $class);
        $name = $view->person['First_Name']
            . ' ' . $view->person['Last_Name'];
        $person->set('foaf:name', trim(preg_replace('/\s+/', ' ', $name)));
        foreach ($view->uris as $uri) {
            $person->add($uri->Predicate, $graph->resource($uri->URI));
        }
        return $person;
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
        $id = $this->params()->fromRoute('id');
        $view = $this->getPersonViewModel($id);
        if (!is_object($view)) {
            $response = $this->getResponse();
            $response->setStatusCode(404);
            return $response;
        }
        return $this->getRdfResponse($this->getGraphFromView($view));
    }

    /**
     * "Show person" page
     *
     * @return mixed
     */
    public function showAction()
    {
        $id = $this->params()->fromRoute('id');
        if (null === $id) {
            return $this->forwardTo(__NAMESPACE__ . '\Person', 'list');
        }
        $view = $this->getPersonViewModel(
            $id, $this->params()->fromQuery('sort', 'series')
        );
        if (!is_object($view)) {
            return $this->forwardTo(__NAMESPACE__ . '\Person', 'notfound');
        }
        return $view;
    }

    /**
     * Person list
     *
     * @return mixed
     */
    public function listAction()
    {
        $extra = $this->params()->fromRoute('extra');
        $bios = (strtolower($extra) == 'bios');
        return $this->createViewModel(
            [
                'bioMode' => $bios,
                'people' => $this->getDbTable('person')->getList($bios)
            ]
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

    /**
     * Get the view model representing the specified person (or false if
     * invalid ID)
     *
     * @param int    $id   ID of person to load
     * @param string $sort Sort type
     *
     * @return \Laminas\View\Model\ViewModel|bool
     */
    protected function getPersonViewModel($id, $sort = 'title')
    {
        $table = $this->getDbTable('person');
        $rowObj = $table->getByPrimaryKey($id);
        if (!is_object($rowObj)) {
            return false;
        }
        $view = $this->createViewModel(
            ['person' => $rowObj->toArray()]
        );
        $view->sort = $sort;
        $view->citations = $this->getDbTable('itemscreators')
            ->getCitationsForPerson($id, $view->sort);
        $view->credits = $this->getDbTable('editionscredits')
            ->getCreditsForPerson($id, $view->sort);
        $pseudo = $this->getDbTable('pseudonyms');
        $view->pseudonyms = $pseudo->getPseudonyms($id);
        $view->realNames = $pseudo->getRealNames($id);
        $view->files = $this->getDbTable('peoplefiles')->getFilesForPerson($id);
        $view->bibliography = $this->getDbTable('peoplebibliography')
            ->getItemsDescribingPerson($id);
        $view->links = $this->getDbTable('peoplelinks')->getLinksForPerson($id);
        $view->uris = $this->getDbTable('peopleuris')->getURIsForPerson($id);
        return $view;
    }
}
