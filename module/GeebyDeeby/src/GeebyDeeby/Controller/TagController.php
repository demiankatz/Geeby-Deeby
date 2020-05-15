<?php
/**
 * Tag controller
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
 * Tag controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class TagController extends AbstractBase
{
    /**
     * 303 redirect page
     *
     * @return mixed
     */
    public function indexAction()
    {
        return $this->performRdfRedirect('tag');
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
    protected function addPrimaryResourceToGraph($graph, $view, $class = [])
    {
        $id = $view->tag['Tag_ID'];
        $uri = $this->getServerUrl('tag', ['id' => $id]);
        $tag = $graph->resource($uri, $class);
        $name = $view->tag['Tag'];
        $tag->set('rdf:label', $name);
        foreach ($view->tagAttributes as $current) {
            if (!empty($current['Tags_Attribute_RDF_Property'])) {
                $tag->set(
                    $current['Tags_Attribute_RDF_Property'],
                    $current['Tags_Attribute_Value']
                );
            }
        }
        foreach ($view->relationshipsValues as $current) {
            if (!empty($current['predicate'])) {
                foreach ($current['values'] as $value) {
                    $tag->add(
                        $current['predicate'],
                        $this->getServerUrl('tag', ['id' => $value['Tag_ID']])
                    );
                }
            }
        }
        foreach ($view->uris as $uri) {
            $tag->add($uri->Predicate, $graph->resource($uri->URI));
        }
        return tag;
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
        $view = $this->getViewModelWithTag();
        if (!is_object($view)) {
            $response = $this->getResponse();
            $response->setStatusCode(404);
            return $response;
        }
        return $this->getRdfResponse($this->getGraphFromView($view));
    }

    /**
     * Get a view model containing a tag object (or return false if missing)
     *
     * @param array $extras Extra parameters to send to view model
     *
     * @return mixed
     */
    protected function getViewModelWithTag($extras = [])
    {
        $id = $this->params()->fromRoute('id');
        $table = $this->getDbTable('tag');
        $rowObj = (null === $id) ? null : $table->getByPrimaryKey($id);
        if (!is_object($rowObj)) {
            return false;
        }
        $view = $this->createViewModel(
            ['tag' => $rowObj->toArray()]
        );
        $view->items = $this->getDbTable('itemstags')
            ->getItemsForTag($id, $extras['sort'] ?? 'series');
        $view->tagAttributes = $this->getDbTable('tagsattributesvalues')
            ->getAttributesForTag($id);
        $view->relationshipsValues = $this->getDbTable('tagsrelationshipsvalues')
            ->getRelationshipsForTag($id);
        $view->uris = $this->getDbTable('tagsuris')->getURIsForTag($id);
        return $view;
    }

    /**
     * "Show tag" page
     *
     * @return mixed
     */
    public function showAction()
    {
        $view = $this->getViewModelWithTag(
            ['sort' => $this->params()->fromQuery('sort', 'series')]
        );
        return $view ? $view : $this->forwardTo(__NAMESPACE__ . '\Tag', 'notfound');
    }

    /**
     * Tag list
     *
     * @return mixed
     */
    public function listAction()
    {
        return $this->createViewModel(
            ['tags' => $this->getDbTable('tag')->getList()]
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
