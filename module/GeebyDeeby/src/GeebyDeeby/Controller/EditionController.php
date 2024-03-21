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

use function is_object;

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
    use FullTextAttributesTrait;

    /**
     * RDF class for representing copies of editions (null to omit).
     *
     * @var string
     */
    protected $copyRdfClass = null;

    /*
     * Default predicate to use for credits, if no specific predicate is included
     * in the role data. (Null to omit predicate-free credits in RDF output).
     *
     * @var string
     */
    protected $defaultCreditPredicate = null;

    /**
     * RDF predicate for linking editions to copies (null to omit).
     *
     * @var string
     */
    protected $hasCopyPredicate = null;

    /**
     * RDF predicate for linking full text URIs to copies (null to omit).
     *
     * @var string
     */
    protected $fullTextPredicate = null;

    /**
     * Add credits to an edition graph.
     *
     * @param \EasyRdf\Graph $graph   Graph to populate
     * @param object         $edition Edition graph to populate
     * @param object         $view    View model populated with information.
     *
     * @return void
     */
    protected function addCreditsToGraph($graph, $edition, $view)
    {
        foreach ($view->credits as $credit) {
            $personUri = $this
                ->getServerUrl('person', ['id' => $credit['Person_ID']]);
            $predicate = empty($credit['Edition_Credit_Predicate'])
                ? $this->defaultCreditPredicate
                : $credit['Edition_Credit_Predicate'];
            if (!empty($predicate)) {
                $edition->add($predicate, $graph->resource($personUri . '#name'));
            }
        }
    }

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
                    ['Sequence_ID' => $rowObj->Preferred_Item_AltName_ID]
                )->current();
                $item['Item_AltName'] = $tmpRow['Item_AltName'];
            }
        } else {
            $item = [];
        }
        if (!empty($rowObj->Series_ID)) {
            $seriesTable = $this->getDbTable('series');
            $seriesObj = $seriesTable->getByPrimaryKey($rowObj->Series_ID);
            $series = $seriesObj->toArray();
            if (!empty($rowObj->Preferred_Series_AltName_ID)) {
                $ian = $this->getDbTable('seriesalttitles');
                $tmpSeriesRow = $ian->select(
                    ['Sequence_ID' => $rowObj->Preferred_Series_AltName_ID]
                )->current();
                $series['Series_AltName'] = $tmpSeriesRow['Series_AltName'];
            }
        } else {
            $series = [];
        }
        $extras['editionAttributes'] = $this->getDbTable('editionsattributesvalues')
            ->getAttributesForEdition($id);
        return $this->createViewModel(
            ['edition' => $rowObj->toArray(), 'item' => $item, 'series' => $series]
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
    protected function addPrimaryResourceToGraph($graph, $view, $class = [])
    {
        $articleHelper = $this->serviceLocator->get('GeebyDeeby\Articles');
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
        foreach ($view->oclcNumbers as $oclc) {
            $edition->add(
                'owl:sameAs',
                'http://www.worldcat.org/oclc/' . $oclc['OCLC_Number']
            );
        }
        if (!empty($this->copyRdfClass) && !empty($this->hasCopyPredicate)) {
            if (!empty($this->fullTextPredicate)) {
                foreach ($view->fullText as $i => $fullText) {
                    $copyUri = $uri . '#copy' . $i;
                    $copy = $graph->resource($copyUri, $this->copyRdfClass);
                    $edition->add($this->hasCopyPredicate, $copy);
                    $copy->set($this->fullTextPredicate, $fullText['Full_Text_URL']);
                    $currentAttribs
                        = $view->fullTextAttributes[$fullText->Sequence_ID] ?? [];
                    foreach ($currentAttribs as $attr) {
                        $prop = $attr['Editions_Full_Text_Attribute_RDF_Property'];
                        if (!empty($prop)) {
                            $copy->set(
                                $prop,
                                $attr['Editions_Full_Text_Attribute_Value']
                            );
                        }
                    }
                }
            }
        }
        $this->addCreditsToGraph($graph, $edition, $view);
        return $edition;
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
        $this->addFullTextAttributesToView($view);
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
