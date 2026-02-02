<?php

/**
 * Item controller
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

use DateTime;
use GeebyDeeby\Db\Service\CollectionService;
use GeebyDeeby\Db\Service\EditionsAttributesValueService;
use GeebyDeeby\Db\Service\EditionsCreditService;
use GeebyDeeby\Db\Service\EditionService;
use GeebyDeeby\Db\Service\EditionsFullTextService;
use GeebyDeeby\Db\Service\EditionsImageService;
use GeebyDeeby\Db\Service\EditionsIsbnService;
use GeebyDeeby\Db\Service\EditionsOclcNumberService;
use GeebyDeeby\Db\Service\EditionsPlatformService;
use GeebyDeeby\Db\Service\EditionsProductCodeService;
use GeebyDeeby\Db\Service\EditionsReleaseDateService;
use GeebyDeeby\Db\Service\FullTextSourceService;
use GeebyDeeby\Db\Service\ItemsAdaptationService;
use GeebyDeeby\Db\Service\ItemsAltTitleService;
use GeebyDeeby\Db\Service\ItemsAttributesValueService;
use GeebyDeeby\Db\Service\ItemsBibliographyService;
use GeebyDeeby\Db\Service\ItemsCreatorService;
use GeebyDeeby\Db\Service\ItemsDescriptionService;
use GeebyDeeby\Db\Service\ItemService;
use GeebyDeeby\Db\Service\ItemsFileService;
use GeebyDeeby\Db\Service\ItemsInCollectionService;
use GeebyDeeby\Db\Service\ItemsLinkService;
use GeebyDeeby\Db\Service\ItemsRelationshipsValueService;
use GeebyDeeby\Db\Service\ItemsReviewService;
use GeebyDeeby\Db\Service\ItemsTagService;
use GeebyDeeby\Db\Service\MaterialTypeService;
use GeebyDeeby\Db\Service\SeriesService;

use function count;
use function is_object;

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
    use FullTextAttributesTrait;

    /**
     * Default predicate to use for creators, if no specific predicate is included
     * in the role data. (Null to omit predicate-free creators in RDF output).
     *
     * @var string
     */
    protected $defaultCreatorPredicate = null;

    /**
     * Add creators to an item graph.
     *
     * @param \EasyRdf\Graph $graph Graph to populate
     * @param object         $item  Item graph to populate
     * @param object         $view  View model populated with information.
     *
     * @return void
     */
    protected function addCreatorsToGraph($graph, $item, $view)
    {
        foreach ($view->creators as $creator) {
            $personUri = $this
                ->getServerUrl('person', ['id' => $creator['Person_ID']]);
            $predicate = empty($creator['Item_Creator_Predicate'])
                ? $this->defaultCreatorPredicate
                : $creator['Item_Creator_Predicate'];
            if (!empty($predicate)) {
                $item->add($predicate, $graph->resource($personUri));
            }
        }
    }

    /**
     * Get a view model containing an item object (or return false if missing)
     *
     * @param array $extras Extra parameters to send to view model
     *
     * @return mixed
     */
    protected function getViewModelWithItem($extras = [])
    {
        $id = $this->params()->fromRoute('id');
        $entity = (null === $id) ? null : $this->getDbService(ItemService::class)->getByPrimaryKey($id);
        if (!is_object($entity)) {
            return false;
        }
        $extras['editionAttributes'] = $this->getDbService(EditionsAttributesValueService::class)
            ->getAttributesForItem($id);
        $extras['itemAttributes'] = $this->getDbService(ItemsAttributesValueService::class)->getAttributesForItem($id);
        $extras['relationshipsValues'] = $this->getDbService(ItemsRelationshipsValueService::class)
            ->getRelationshipsForItem($id);
        return $this->createViewModel(
            ['item' => $entity->toArray()] + $extras
        );
    }

    /**
     * "List items by year" page
     *
     * @return mixed
     */
    public function byyearAction()
    {
        $raw = $this->getDbService(EditionsReleaseDateService::class)->getItemsByYear();

        // Sort out information about editions:
        $editionsByItem = [];
        $sortedData = [];
        foreach ($raw as $current) {
            if (!isset($editionsByItem[$current->Item_ID])) {
                $editionsByItem[$current->Item_ID] = [];
            }
            $editionsByItem[$current->Item_ID][$current->Edition_ID] = 1;
            $dateKey = $current->Year . '|' . $current->Month . '|' . $current->Day
                . '|' . $current->Item_ID;
            if (!isset($sortedData[$dateKey])) {
                $sortedData[$dateKey] = [];
            }
            $sortedData[$dateKey][] = $current;
        }

        $callback = function ($i) {
            return count(array_keys($i));
        };
        $editionsByItem = array_map($callback, $editionsByItem);

        // Use the information collected above to decide what edition information
        // to display to the user:
        $items = [];
        foreach ($sortedData as $currentSet) {
            $editions = [];
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

        return $this->createViewModel(['items' => $items]);
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
    protected function addPrimaryResourceToGraph($graph, $view, $class = [])
    {
        $articleHelper = $this->serviceLocator->get('GeebyDeeby\Articles');
        $id = $view->item['Item_ID'];
        $uri = $this->getServerUrl('item', ['id' => $id]);
        $type = $this->getDbService(MaterialTypeService::class)
            ->getByPrimaryKey($view->item['Material_Type_ID']);
        if (!empty($type->Material_Type_RDF_Class)) {
            $class = (array)$class;
            $class[] = $type->Material_Type_RDF_Class;
        }
        $item = $graph->resource($uri, $class);
        $name = $view->item['Item_Name'];
        $item->set('dcterms:title', $articleHelper->formatTrailingArticles($name));
        foreach ($view->itemAttributes as $current) {
            if (!empty($current['Items_Attribute_RDF_Property'])) {
                $item->set(
                    $current['Items_Attribute_RDF_Property'],
                    $current['Items_Attribute_Value']
                );
            }
        }
        foreach ($view->relationshipsValues as $current) {
            if (!empty($current['predicate'])) {
                foreach ($current['values'] as $value) {
                    $item->add(
                        $current['predicate'],
                        $this->getServerUrl('item', ['id' => $value['Item_ID']])
                    );
                }
            }
        }
        $this->addCreatorsToGraph($graph, $item, $view);
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
        $view->creators = $this->getDbService(ItemsCreatorService::class)->getCreatorsForItem($id);
        $view->credits = $this->getDbService(EditionsCreditService::class)->getCreditsForItem($id);
        $view->images = $this->getDbService(EditionsImageService::class)->getImagesForItem($id);
        $view->series = $this->getDbService(SeriesService::class)->getSeriesForItem($id, true, true);
        $view->platforms = $this->getDbService(EditionsPlatformService::class)->getPlatformsForItem($id);
        // Contains/containedIn are item-level relationships (see
        // addItemRelationships below), while children/parents are edition-level
        // relationships. These are very similar, but the edition relationships
        // are preferred and more valuable.
        $itemService = $this->getDbService(ItemService::class);
        $view->children = $itemService->getItemChildren($id);
        $view->parents = $itemService->getItemParents($id);

        $view->publishers = $this->getDbService(EditionService::class)->getPublishersForItem($id);
        $view->dates = $this->getDbService(EditionsReleaseDateService::class)->getDatesForItem($id);
        $view->isbns = $this->getDbService(EditionsIsbnService::class)->getISBNsForItem($id);
        $view->codes = $this->getDbService(EditionsProductCodeService::class)->getProductCodesForItem($id);
        $view->oclcNumbers = $this->getDbService(EditionsOclcNumberService::class)
            ->getOCLCNumbersForItem($id);
        $view->fullText = $this->getDbService(EditionsFullTextService::class)
            ->getFullTextForItem($id);
        $this->addFullTextAttributesToView($view);
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
        $view->altTitles = $this->getDbService(ItemsAltTitleService::class)->getAltTitles($id);
        $view->tags = $this->getDbService(ItemsTagService::class)->getTagsForItem($id);
        $collections = $this->getDbService(ItemsInCollectionService::class);
        $view->contains = $collections->getItemsForCollection($id);
        $view->containedIn = $collections->getCollectionsForItem($id);
        $trans = $this->getDbTable('itemstranslations');
        $adapt = $this->getDbService(ItemsAdaptationService::class);
        // The variable/function names are a bit unintuitive here --
        // $view->translatedInto is a list of books that $id was translated into;
        // we obtain these by calling $trans->getTranslatedFrom(), which gives
        // us a list of books that $id was translated from.
        $view->translatedInto = $trans->getTranslatedFrom($id, true);
        $view->translatedFrom = $trans->getTranslatedInto($id, true);
        $view->adaptedInto = $adapt->getAdaptedFrom($id);
        $view->adaptedFrom = $adapt->getAdaptedInto($id);
        $view->descriptions = $this->getDbService(ItemsDescriptionService::class)->getDescriptions($id);
        $reviews = $this->getDbService(ItemsReviewService::class);
        $view->reviews = $reviews->getReviewsForItem($id);
        $user = $this->getCurrentUser();
        $view->userHasReview = $user ? (bool)$reviews->getByUserAndItem($user, $id) : false;
        $collections = $this->getDbService(CollectionService::class);
        $view->buyers = $collections->getForItem($id, 'want');
        $view->owners = $collections->getForItem($id, 'have');
        $view->sellers = $collections->getForItem($id, 'extra');
        $view->files = $this->getDbService(ItemsFileService::class)->getFilesForItem($id);
        $view->bibliography = $this->getDbService(ItemsBibliographyService::class)
            ->getItemsDescribingItem($id);
        $view->links = $this->getDbService(ItemsLinkService::class)->getLinksForItem($id);
        $view->editions = $this->getDbService(EditionService::class)->getEditionsForItem($id, true);
    }

    /**
     * Get the view model representing the item and all relevant related details.
     *
     * @param bool $includeEditionData Include edition data?
     *
     * @return \Laminas\View\Model\ViewModel|bool
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
        $config = $this->serviceLocator->get('config');
        return $this->createViewModel(
            [
                'isbn' => new \VuFindCode\ISBN($isbn),
                'config' => $config['geeby-deeby']['isbn_links'] ?? [],
            ]
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

        // Special case: new items:
        if ($this->params()->fromRoute('extra') == 'New') {
            return $this->forwardTo(__NAMESPACE__ . '\Item', 'new');
        }

        // Special case: with reviews:
        if ($this->params()->fromRoute('extra') == 'Reviews') {
            return $this->forwardTo(__NAMESPACE__ . '\Item', 'reviews');
        }

        // Standard case: all items:
        return $this->createViewModel(
            ['items' => $this->getDbService(ItemService::class)->getList()]
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
     * New items action
     *
     * @return mixed
     */
    public function newAction()
    {
        $paginator = $this->getDbService(ItemService::class)->getNewItemsPaginator(
            $this->params()->fromQuery('page', 1)
        );
        return $this->createViewModel(compact('paginator'));
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
        $service = $this->getDbService(ItemsReviewService::class);
        $itemId = $this->params()->fromRoute('id');
        $existing = $service->getByUserAndItem($user, $itemId);

        // Save comment if found.
        if ($this->getRequest()->isPost()) {
            $view = $this->createViewModel(
                ['noChange' => false, 'item' => $itemId]
            );
            $review = $this->params()->fromPost('Review');
            if ($existing && $review == $existing->getReview()) {
                $view->noChange = true;
            } else {
                if (!$existing) {
                    $existing = $service->createEntity()
                        ->setUser($user)
                        ->setItem($itemId)
                        ->setAddedDate(new DateTime());
                }
                $existing->setReview($review)->setIsApproved(false);
                $service->persistEntity($existing);
            }
            $view->setTemplate('geeby-deeby/item/review-submitted');
            return $view;
        }

        // Send review to the view.
        $review = $existing ? $existing->getReview() : '';

        $view = $this->getViewModelWithItem(['review' => $review]);
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
        $rawSource = $this->params()->fromQuery('source');
        $source = empty($rawSource) ? null : $rawSource;
        $view = $this->createViewModel(compact('fuzzy', 'source'));
        $view->sources = $this->getDbService(FullTextSourceService::class)->getList();
        $view->fulltext = $this->getDbService(EditionsFullTextService::class)
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
        $view->reviews = $this->getDbService(ItemsReviewService::class)->getReviewsByUser(null);
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
            $seriesOptions = $this->getDbService(SeriesService::class)->getSeriesForItem($item, false);
            if (count($seriesOptions) > 1) {
                $view = $this->createViewModel(['series' => $seriesOptions]);
                $view->setTemplate('geeby-deeby/item/collection-pick-series');
                return $view;
            }
            $series = $seriesOptions[0]['Series_ID'];
        }

        // Check for an existing entry:
        $service = $this->getDbService(CollectionService::class);

        // Has a comment been posted?  If so, process the request:
        $comment = $this->params()->fromPost('comment');
        if (null !== $comment) {
            if (null !== $this->params()->fromPost('delete')) {
                $service->deleteEntry($user, $item, $series, $list);
            } else {
                $service->updateEntry($user, $item, $series, $list, $comment);
            }
            return $this->redirect()->toRoute('item', ['id' => $item]);
        }

        // If we go this far, we need to prompt the user for more information:
        $existing = $service->getExistingEntry($user, $item, $series, $list);
        $view = $this->createViewModel(
            ['list' => $list, 'existing' => $existing?->toArray(), 'series' => $series]
        );
        $view->setTemplate('geeby-deeby/item/collection-add');
        return $view;
    }
}
