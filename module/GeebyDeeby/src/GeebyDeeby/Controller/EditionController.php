<?php
/**
 * Edition controller
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
 * Edition controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionController extends AbstractBase
{
    /**
     * Get a view model containing an edition object (or return false if missing)
     *
     * @param array $extras     Extra parameters to send to view model
     * @param int   $overrideId ID to use instead of the route match (optional)
     *
     * @return mixed
     */
    protected function getViewModelWithEdition($extras = [], $overrideId = null)
    {
        $id = ($overrideId === null)
            ? $this->params()->fromRoute('id') : $overrideId;
        $table = $this->getDbTable('edition');
        $rowObj = (null === $id) ? null : $table->getByPrimaryKey($id);
        if (!is_object($rowObj)) {
            return false;
        }
        if (!empty($rowObj->Item_ID)) {
            $itemTable = $this->getDbTable('item');
            $itemObj = $itemTable->getByPrimaryKey($rowObj->Item_ID);
            $item = $itemObj->toArray();
            if (!empty($rowObj->Preferred_Item_AltName_ID)) {
                $ian = $this->getDbTable('itemsalttitles');
                $tmpRow = $ian->select(
                    array('Sequence_ID' => $rowObj->Preferred_Item_AltName_ID)
                )->current();
                $item['Item_AltName'] = $tmpRow['Item_AltName'];
            }
        } else {
            $item = array();
        }
        if (!empty($rowObj->Series_ID)) {
            $seriesTable = $this->getDbTable('series');
            $seriesObj = $seriesTable->getByPrimaryKey($rowObj->Series_ID);
            $series = $seriesObj->toArray();
            if (!empty($rowObj->Preferred_Series_AltName_ID)) {
                $ian = $this->getDbTable('seriesalttitles');
                $tmpSeriesRow = $ian->select(
                    array('Sequence_ID' => $rowObj->Preferred_Series_AltName_ID)
                )->current();
                $series['Series_AltName'] = $tmpSeriesRow['Series_AltName'];
            }
        } else {
            $series = array();
        }
        $extras['editionAttributes'] = $this->getDbTable('editionsattributesvalues')
            ->getAttributesForEdition($id);
        return $this->createViewModel(
            array('edition' => $rowObj->toArray(), 'item' => $item, 'series' => $series)
            + $extras
        );
    }

    /**
     * 303 redirect page
     *
     * @return mixed
     */
    public function indexAction()
    {
        return $this->performRdfRedirect('edition');
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
    protected function addPrimaryResourceToGraph($graph, $view, $class = array())
    {
        $articleHelper = $this->getServiceLocator()->get('GeebyDeeby\Articles');
        $id = $view->edition['Edition_ID'];
        $uri = $this->getServerUrl('edition', ['id' => $id]);
        $edition = $graph->resource($uri, $class);
        $name = $view->edition['Edition_Name'];
        $edition->set('rdf:label', $articleHelper->formatTrailingArticles($name));
        foreach ($view->editionAttributes as $current) {
            if (!empty($current['Editions_Attribute_RDF_Property'])) {
                $edition->set(
                    $current['Editions_Attribute_RDF_Property'],
                    $current['Editions_Attribute_Value']
                );
            }
        }
        return $edition;
    }

    /**
     * Build an RDF graph from the available data.
     *
     * @param string $id   ID of primary resource in graph.
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
        $view = $this->getViewModelWithEditionAndDetails();
        if (!is_object($view)) {
            $response = $this->getResponse();
            $response->setStatusCode(404);
            return $response;
        }
        return $this->getRdfResponse($this->getGraphFromView($view));
    }

    /**
     * "Show item" page
     *
     * @return mixed
     */
    public function showAction()
    {
        $view = $this->getViewModelWithEditionAndDetails();
        if (!$view) {
            return $this->forwardTo(__NAMESPACE__ . '\Edition', 'notfound');
        }
        return $view;
    }

    /**
     * Get the view model populated with edition-specific details (or return
     * false if the edition is not found).
     *
     * @param int $overrideId ID to use instead of the route match (optional)
     *
     * @return mixed
     */
    public function getViewModelWithEditionAndDetails($overrideId = null)
    {
        $view = $this->getViewModelWithEdition([], $overrideId);
        if (!$view) {
            return false;
        }
        $id = $view->edition['Edition_ID'];
        $view->creators = $this->getDbTable('itemscreators')
            ->getCreatorsForItem($view->edition['Item_ID']);
        $view->credits = $this->getDbTable('editionscredits')
            ->getCreditsForEdition($id);
        $view->images = $this->getDbTable('editionsimages')
            ->getImagesForEditionOrParentEdition($id);
        $view->platforms = $this->getDbTable('editionsplatforms')
            ->getPlatformsForEdition($id);
        $view->dates = $this->getDbTable('editionsreleasedates')
            ->getDatesForEditionOrParentEdition($id);
        $view->isbns = $this->getDbTable('editionsisbns')->getISBNsForEdition($id);
        $view->codes = $this->getDbTable('editionsproductcodes')
            ->getProductCodesForEdition($id);
        $view->oclcNumbers = $this->getDbTable('editionsoclcnumbers')
            ->getOCLCNumbersForEdition($id);
        $view->fullText = $this->getDbTable('editionsfulltext')
            ->getFullTextForEditionOrParentEdition($id);
        $edTable = $this->getDbTable('edition');
        $view->publishers = $edTable->getPublishersForEdition($id);
        $view->parent = $edTable->getParentItemForEdition($id);
        $view->children = $edTable->getItemsForEdition($id);
        return $view;
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
