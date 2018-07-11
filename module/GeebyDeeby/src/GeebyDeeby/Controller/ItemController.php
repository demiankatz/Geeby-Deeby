<?php
/**
 * Item controller
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
 * Item controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemController extends AbstractBase
{
    /**
     * Get a view model containing an item object (or return false if missing)
     *
     * @param array $extras Extra parameters to send to view model
     *
     * @return mixed
     */
    protected function getViewModelWithItem($extras = array())
    {
        $id = $this->params()->fromRoute('id');
        $table = $this->getDbTable('item');
        $rowObj = (null === $id) ? null : $table->getByPrimaryKey($id);
        if (!is_object($rowObj)) {
            return false;
        }
        $extras['editionAttributes'] = $this->getDbTable('editionsattributesvalues')
            ->getAttributesForItem($id);
        return $this->createViewModel(
            array('item' => $rowObj->toArray()) + $extras
        );
    }

    /**
     * "List items by year" page
     *
     * @return mixed
     */
    public function byyearAction()
    {
        $raw = $this->getDbTable('editionsreleasedates')->getItemsByYear();

        // Sort out information about editions:
        $editionsByItem = array();
        $sortedData = array();
        foreach ($raw as $current) {
            if (!isset($editionsByItem[$current->Item_ID])) {
                $editionsByItem[$current->Item_ID] = array();
            }
            $editionsByItem[$current->Item_ID][$current->Edition_ID] = 1;
            $dateKey = $current->Year . '|' . $current->Month . '|' . $current->Day
                . '|' . $current->Item_ID;
            if (!isset($sortedData[$dateKey])) {
                $sortedData[$dateKey] = array();
            }
            $sortedData[$dateKey][] = $current;
        }

        $callback = function ($i) {
            return count(array_keys($i));
        };
        $editionsByItem = array_map($callback, $editionsByItem);

        // Use the information collected above to decide what edition information
        // to display to the user:
        $items = array();
        foreach ($sortedData as $currentSet) {
            $editions = array();
            foreach ($currentSet as $current) {
                $editions[] = $current['Edition_ID'];
            }
            $editions = array_unique($editions);
            $prependEdition
                = (count($editions) != $editionsByItem[$current['Item_ID']]);
            $last = false;
            foreach ($currentSet as $current) {
                if ($prependEdition) {
                    $current['Note'] = empty($current['Note'])
                        ? $current['Edition_Name']
                        : $current['Edition_Name'] . ' - ' . $current['Note'];
                } else {
                    if ($last) {
                        if ($last['Note'] == $current['Note']) {
                            continue;
                        }
                    }
                }
                $items[] = $last = $current;
            }
        }

        return $this->createViewModel(array('items' => $items));
    }

    /**
     * 303 redirect page
     *
     * @return mixed
     */
    public function indexAction()
    {
        return $this->performRdfRedirect('item');
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
        $id = $view->item['Item_ID'];
        $uri = $this->getServerUrl('item', ['id' => $id]);
        $type = $this->getDbTable('materialtype')
            ->getByPrimaryKey($view->item['Material_Type_ID']);
        if (!empty($type->Material_Type_RDF_Class)) {
            $class = (array)$class;
            $class[] = $type->Material_Type_RDF_Class;
        }
        $item = $graph->resource($uri, $class);
        $name = $view->item['Item_Name'];
        $item->set('dcterms:title', $articleHelper->formatTrailingArticles($name));
        return $item;
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
        $view = $this->getViewModelWithItemAndDetails();
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
        return ($view = $this->getViewModelWithItemAndDetails())
            ? $view : $this->forwardTo(__NAMESPACE__ . '\Item', 'notfound');
    }

    /**
     * "Show editions of item" page
     *
     * @return mixed
     */
    public function editionsAction()
    {
        return ($view = $this->getViewModelWithItemAndDetails(false))
            ? $view : $this->forwardTo(__NAMESPACE__ . '\Item', 'notfound');
    }

    /**
     * Add edition-specific relationships to a view model.
     *
     * @param int    $id   Item id
     * @param object $view View model
     *
     * @return void
     */
    protected function addEditionRelationships($id, $view)
    {
        $view->credits = $this->getDbTable('editionscredits')->getCreditsForItem($id);
        $view->realNames = $this->getDbTable('pseudonyms')
            ->getRealNamesBatch($view->credits);
        $view->images = $this->getDbTable('editionsimages')->getImagesForItem($id);
        $view->series = $this->getDbTable('series')->getSeriesForItem($id, true, true);
        $view->platforms = $this->getDbTable('editionsplatforms')
            ->getPlatformsForItem($id);
        // Contains/containedIn are item-level relationships (see addItemRelationships
        // below), while children/parents are edition-level relationships. These are
        // very similar, but the edition relationships are preferred and more valuable.
        $itemTable = $this->getDbTable('item');
        $view->children = $itemTable->getItemChildren($id);
        $view->parents = $itemTable->getItemParents($id);

        $edTable = $this->getDbTable('edition');
        $view->publishers = $edTable->getPublishersForItem($id);
        $view->dates = $this->getDbTable('editionsreleasedates')->getDatesForItem($id);
        $view->isbns = $this->getDbTable('editionsisbns')->getISBNsForItem($id);
        $view->codes = $this->getDbTable('editionsproductcodes')
            ->getProductCodesForItem($id);
        $view->oclcNumbers = $this->getDbTable('editionsoclcnumbers')
            ->getOCLCNumbersForItem($id);
        $view->fullText = $this->getDbTable('editionsfulltext')
            ->getFullTextForItem($id);
    }

    /**
     * Add item-specific relationships to a view model.
     *
     * @param int    $id   Item id
     * @param object $view View model
     *
     * @return void
     */
    protected function addItemRelationships($id, $view)
    {
        $view->altTitles = $this->getDbTable('itemsalttitles')->getAltTitles($id);
        $view->tags = $this->getDbTable('itemstags')->getTags($id);
        $collections = $this->getDbTable('itemsincollections');
        $view->contains = $collections->getItemsForCollection($id);
        $view->containedIn = $collections->getCollectionsForItem($id);
        $trans = $this->getDbTable('itemstranslations');
        $adapt = $this->getDbTable('itemsadaptations');
        // The variable/function names are a bit unintuitive here --
        // $view->translatedInto is a list of books that $id was translated into;
        // we obtain these by calling $trans->getTranslatedFrom(), which gives
        // us a list of books that $id was translated from.
        $view->translatedInto = $trans->getTranslatedFrom($id, true);
        $view->translatedFrom = $trans->getTranslatedInto($id, true);
        $view->adaptedInto = $adapt->getAdaptedFrom($id);
        $view->adaptedFrom = $adapt->getAdaptedInto($id);
        $view->descriptions = $this->getDbTable('itemsdescriptions')
            ->getDescriptions($id);
        $reviews = $this->getDbTable('itemsreviews');
        $view->reviews = $reviews->getReviewsForItem($id);
        $user = $this->getCurrentUser();
        if ($user) {
            $view->userHasReview = (bool)count(
                $reviews->select(
                    array('User_ID' => $user->User_ID, 'Item_ID' => $id)
                )
            );
        } else {
            $view->userHasReview = false;
        }
        $collections = $this->getDbTable('collections');
        $view->buyers = $collections->getForItem($id, 'want');
        $view->owners = $collections->getForItem($id, 'have');
        $view->sellers = $collections->getForItem($id, 'extra');
        $view->files = $this->getDbTable('itemsfiles')->getFilesForItem($id);
        $view->bibliography = $this->getDbTable('itemsbibliography')
            ->getItemsDescribingItem($id);
        $view->links = $this->getDbTable('itemslinks')->getLinksForItem($id);
        $edTable = $this->getDbTable('edition');
        $view->editions = $edTable->getEditionsForItem($id, true);
    }

    /**
     * Get the view model representing the item and all relevant related details.
     *
     * @return \Zend\View\Model\ViewModel|bool
     */
    public function getViewModelWithItemAndDetails($includeEditionData = true)
    {
        $view = $this->getViewModelWithItem();
        if (!$view) {
            return false;
        }
        $id = $view->item['Item_ID'];
        if ($includeEditionData) {
            $this->addEditionRelationships($id, $view);
        }
        $this->addItemRelationships($id, $view);
        return $view;
    }

    /**
     * ISBN details
     *
     * @return mixed
     */
    public function isbndetailsAction()
    {
        $isbn = $this->params()->fromRoute('extra');
        $config = $this->getServiceLocator()->get('config');
        return $this->createViewModel(
            array(
                'isbn' => new \VuFindCode\ISBN($isbn),
                'config' => isset($config['geeby-deeby']['isbn_links'])
                    ? $config['geeby-deeby']['isbn_links'] : []
            )
        );
    }

    /**
     * Item list
     *
     * @return mixed
     */
    public function listAction()
    {
        // Special case: sort by year:
        if ($this->params()->fromRoute('extra') == 'ByYear') {
            return $this->forwardTo(__NAMESPACE__ . '\Item', 'byyear');
        }

        // Special case: with full text:
        if ($this->params()->fromRoute('extra') == 'FullText') {
            return $this->forwardTo(__NAMESPACE__ . '\Item', 'fulltext');
        }

        // Special case: with reviews:
        if ($this->params()->fromRoute('extra') == 'Reviews') {
            return $this->forwardTo(__NAMESPACE__ . '\Item', 'reviews');
        }

        // Standard case: all items:
        return $this->createViewModel(
            array('items' => $this->getDbTable('item')->getList())
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
     * "Submit review" page
     *
     * @return mixed
     */
    public function reviewAction()
    {
        // Make sure user is logged in.
        if (!($user = $this->getCurrentUser())) {
            return $this->forceLogin();
        }

        // Check for existing review.
        $table = $this->getDbTable('itemsreviews');
        $params = array(
            'Item_ID' => $this->params()->fromRoute('id'),
            'User_ID' => $user->User_ID
        );

        $existing = $table->select($params)->toArray();
        $existing = count($existing) > 0 ? $existing[0] : false;

        // Save comment if found.
        if ($this->getRequest()->isPost()) {
            $view = $this->createViewModel(
                array('noChange' => false, 'item' => $params['Item_ID'])
            );
            $params['Approved'] = 'n';
            $params['Review'] = $this->params()->fromPost('Review');
            if ($params['Review'] == $existing['Review']) {
                $view->noChange = true;
            } else {
                if ($existing) {
                    $table->delete(
                        [
                            'Item_ID' => $params['Item_ID'],
                            'User_ID' => $params['User_ID']
                        ]
                    );
                }
                $table->insert($params);
            }
            $view->setTemplate('geeby-deeby/item/review-submitted');
            return $view;
        }

        // Send review to the view.
        $review = $existing ? $existing['Review'] : '';

        $view = $this->getViewModelWithItem(array('review' => $review));
        if (!$view) {
            return $this->forwardTo(__NAMESPACE__ . '\Item', 'notfound');
        }
        return $view;
    }

    /**
     * Full text page
     *
     * @return mixed
     */
    public function fulltextAction()
    {
        $fuzzy = $this->params()->fromQuery('fuzzy', false);
        $source = $this->params()->fromQuery('source');
        $view = $this->createViewModel(compact('fuzzy', 'source'));
        $view->sources = $this->getDbTable('fulltextsource')->getList();
        $view->fulltext = $this->getDbTable('editionsfulltext')
            ->getItemsWithFullText(null, $fuzzy, $source);
        return $view;
    }

    /**
     * Reviews page
     *
     * @return mixed
     */
    public function reviewsAction()
    {
        $view = $this->createViewModel();
        $view->reviews = $this->getDbTable('itemsreviews')->getReviewsByUser(null);
        return $view;
    }

    /**
     * Edit have list action
     *
     * @return mixed
     */
    public function edithaveAction()
    {
        return $this->editList('have');
    }

    /**
     * Edit want list action
     *
     * @return mixed
     */
    public function editwantAction()
    {
        return $this->editList('want');
    }

    /**
     * Edit sale/trade list action
     *
     * @return mixed
     */
    public function editextraAction()
    {
        return $this->editList('extra');
    }

    /**
     * Edit a collection list
     *
     * @param string $list List to edit (have/want/extra)
     *
     * @return mixed
     */
    protected function editList($list)
    {
        // Make sure we are logged in:
        if (!($user = $this->getCurrentUser())) {
            return $this->forceLogin();
        }

        // Which item are we working with?
        $item = $this->params()->fromRoute('id');

        // Do we have a series ID?  If not, the user may need to pick one:
        $series = $this->params()->fromPost('series');
        if (null === $series) {
            $seriesOptions = $this->getDbTable('series')
                ->getSeriesForItem($item, false)->toArray();
            if (count($seriesOptions) > 1) {
                $view = $this->createViewModel(array('series' => $seriesOptions));
                $view->setTemplate('geeby-deeby/item/collection-pick-series');
                return $view;
            }
            $series = $seriesOptions[0]['Series_ID'];
        }

        // Check for an existing entry:
        $table = $this->getDbTable('collections');
        $where = array(
            'User_ID' => $user->User_ID, 'Item_ID' => $item,
            'Series_ID' => $series, 'Collection_Status' => $list
        );
        $existing = $table->select($where)->toArray();
        $existing = count($existing) > 0 ? $existing[0] : false;

        // Has a comment been posted?  If so, process the request:
        $comment = $this->params()->fromPost('comment');
        if (null !== $comment) {
            if (null !== $this->params()->fromPost('delete')) {
                $table->delete($where);
            } else {
                if ($existing) {
                    $table->update(array('Collection_Note' => $comment), $where);
                } else {
                    $table->insert(array('Collection_Note' => $comment) + $where);
                }
            }
            return $this->redirect()->toRoute('item', array('id' => $item));
        }

        // If we go this far, we need to prompt the user for more information:
        $view = $this->createViewModel(
            array('list' => $list, 'existing' => $existing, 'series' => $series)
        );
        $view->setTemplate('geeby-deeby/item/collection-add');
        return $view;
    }
}
