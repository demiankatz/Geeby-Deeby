<?php

/**
 * Series controller
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

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;

use function count;
use function is_object;

/**
 * Series controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesController extends AbstractBase
{
    /**
     * Get a view model containing a series object (or return false if missing)
     *
     * @param array $extras Extra parameters to send to view model
     *
     * @return mixed
     */
    protected function getViewModelWithSeries($extras = [])
    {
        $id = $this->params()->fromRoute('id');
        $table = $this->getDbTable('series');
        $rowObj = (null === $id) ? null : $table->getByPrimaryKey($id);
        if (!is_object($rowObj)) {
            return false;
        }
        $extras['seriesAttributes'] = $this->getDbTable('seriesattributesvalues')
            ->getAttributesForSeries($id);
        $extras['relationshipsValues']
            = $this->getDbTable('seriesrelationshipsvalues')
            ->getRelationshipsForSeries($id);
        return $this->createViewModel(
            ['series' => $rowObj->toArray()] + $extras
        );
    }

    /**
     * "Check for missing data" page
     *
     * @return mixed
     */
    public function checkAction()
    {
        $view = $this->getViewModelWithSeries();
        $seriesId = $view->series['Series_ID'];

        // Check for missing creators
        $editions = $this->getDbTable('edition');
        $callback = function ($select) use ($seriesId) {
            $select->join(
                ['ic' => 'Items_Creators'],
                'Editions.Item_ID = ic.Item_ID',
                [],
                Select::JOIN_LEFT
            );
            $select->join(
                ['i' => 'Items'],
                'Editions.Item_ID = i.Item_ID',
                ['Item_Name'],
                Select::JOIN_LEFT
            );
            $select->where->isNull('ic.Person_ID');
            $select->where(['Series_ID' => $seriesId]);
            $select->order(
                'Editions.Volume, Editions.Position, Editions.Replacement_Number'
            );
        };
        $view->missingCreators = $editions->select($callback)->toArray();

        // Check for missing credits
        $editions = $this->getDbTable('edition');
        $callback = function ($select) use ($seriesId) {
            $select->join(
                ['ec' => 'Editions_Credits'],
                'Editions.Edition_ID = ec.Edition_ID',
                [],
                Select::JOIN_LEFT
            );
            $select->join(
                ['i' => 'Items'],
                'Editions.Item_ID = i.Item_ID',
                ['Item_Name'],
                Select::JOIN_LEFT
            );
            $select->where->isNull('ec.Person_ID');
            $select->where(['Series_ID' => $seriesId]);
            $select->order(
                'Editions.Volume, Editions.Position, Editions.Replacement_Number'
            );
        };
        $view->missingCredits = $editions->select($callback)->toArray();

        // Check for missing dates
        $callback = function ($select) use ($seriesId) {
            $select->join(
                ['d' => 'Editions_Release_Dates'],
                'Editions.Edition_ID = d.Edition_ID',
                [],
                Select::JOIN_LEFT
            );
            $select->join(
                ['i' => 'Items'],
                'Editions.Item_ID = i.Item_ID',
                ['Item_Name'],
                Select::JOIN_LEFT
            );
            $select->where->isNull('d.Year');
            $select->where->isNull('Editions.Parent_Edition_ID');
            $select->where(['Series_ID' => $seriesId]);
            $select->order(
                'Editions.Volume, Editions.Position, Editions.Replacement_Number'
            );
        };
        $view->missingDates = $editions->select($callback)->toArray();

        // Get date range stats
        $callback = function ($select) use ($seriesId) {
            $select->where(['Series_ID' => $seriesId]);
            $select->columns(
                [
                    'Edition_ID' => new Expression(
                        'min(?)',
                        ['Editions.Edition_ID'],
                        [Expression::TYPE_IDENTIFIER]
                    ),
                ]
            );
            $select->join(
                ['d' => 'Editions_Release_Dates'],
                'Editions.Edition_ID = d.Edition_ID',
                [
                    'Start' => new Expression(
                        'min(?)',
                        ['Year'],
                        [Expression::TYPE_IDENTIFIER]
                    ),
                    'End' => new Expression(
                        'max(?)',
                        ['Year'],
                        [Expression::TYPE_IDENTIFIER]
                    ),
                ],
                Select::JOIN_LEFT
            );
            $select->group('Series_ID');
        };
        $view->dateStats = current($editions->select($callback)->toArray());

        // Check for missing items
        $callback = function ($select) use ($seriesId) {
            $select->where(['Series_ID' => $seriesId]);
            $select->columns(
                [
                    'Edition_ID' => new Expression(
                        'min(?)',
                        ['Edition_ID'],
                        [Expression::TYPE_IDENTIFIER]
                    ),
                    'Vol' => new Expression(
                        'min(?)',
                        ['Volume'],
                        [Expression::TYPE_IDENTIFIER]
                    ),
                    'Pos' => new Expression(
                        'min(?)',
                        ['Position'],
                        [Expression::TYPE_IDENTIFIER]
                    ),
                    'Rep' => new Expression(
                        'min(?)',
                        ['Replacement_Number'],
                        [Expression::TYPE_IDENTIFIER]
                    ),
                    'Total' => new Expression(
                        'count(?)',
                        ['Position'],
                        [Expression::TYPE_IDENTIFIER]
                    ),
                ]
            );
            $select->where->isNull('Parent_Edition_ID');
            $select->group(['Volume', 'Position', 'Replacement_Number']);
            $select->order(['Volume', 'Position', 'Replacement_Number']);
        };
        $results = $editions->select($callback)->toArray();
        $vol = $lastVol = $minVol = $maxVol = $lastPos = $overallTotal = 0;
        $dupes = $missing = $missingVol = $min = $max = $total = [];
        foreach ($results as $current) {
            if ($current['Vol'] > $vol) {
                $vol = $current['Vol'];
                $lastPos = 0;
                if ($lastVol > 0) {
                    for ($i = $lastVol + 1; $i < $vol; $i++) {
                        $missingVol[] = $i;
                    }
                }
            }
            $pos = $current['Pos'];
            $overallTotal += $current['Total'];
            $total[$vol] = isset($total[$vol])
                ? $total[$vol] + $current['Total'] : $current['Total'];
            if ($current['Total'] > 1) {
                $dupes[$vol][$pos][] = $current['Rep'];
            }
            if ($lastPos > 0) {
                for ($i = $lastPos + 1; $i < $pos; $i++) {
                    $missing[$vol][] = $i;
                }
            }
            if (!isset($min[$vol])) {
                $min[$vol] = $pos;
            }
            if (!isset($max[$vol]) || $pos > $max[$vol]) {
                $max[$vol] = $pos;
            }
            if ($minVol == 0) {
                $minVol = $vol;
            }
            if ($maxVol < $vol) {
                $maxVol = $vol;
            }
            $lastPos = $pos;
            $lastVol = $vol;
        }
        $view->itemStats = [
            'Different' =>  count($results),
            'Start' => $min,
            'End' => $max,
            'StartVol' => $minVol,
            'EndVol' => $maxVol,
            'Total' => $overallTotal,
            'TotalByVol' => $total,
            'Dupes' => $dupes,
            'Missing' => $missing,
            'MissingVol' => $missingVol,
        ];

        return $view;
    }

    /**
     * "Submit comment" page
     *
     * @return mixed
     */
    public function commentAction()
    {
        // Make sure user is logged in.
        if (!($user = $this->getCurrentUser())) {
            return $this->forceLogin();
        }

        // Check for existing review.
        $table = $this->getDbTable('seriesreviews');
        $params = [
            'Series_ID' => $this->params()->fromRoute('id'),
            'User_ID' => $user->User_ID,
        ];

        $existing = $table->select($params)->toArray();
        $existing = count($existing) > 0 ? $existing[0] : false;

        // Save comment if found.
        if ($this->getRequest()->isPost()) {
            $view = $this->createViewModel(
                ['noChange' => false, 'series' => $params['Series_ID']]
            );
            $params['Approved'] = 'n';
            $params['Review'] = $this->params()->fromPost('Review');
            if ($params['Review'] == $existing['Review']) {
                $view->noChange = true;
            } else {
                if ($existing) {
                    $table->delete(
                        [
                            'Series_ID' => $params['Series_ID'],
                            'User_ID' => $params['User_ID'],
                        ]
                    );
                }
                $table->insert($params);
            }
            $view->setTemplate('geeby-deeby/series/comment-submitted');
            return $view;
        }

        // Send review to the view.
        $review = $existing ? $existing['Review'] : '';

        $view = $this->getViewModelWithSeries(['review' => $review]);
        if (!$view) {
            return $this->forwardTo(__NAMESPACE__ . '\Series', 'notfound');
        }
        return $view;
    }

    /**
     * Comments page
     *
     * @return mixed
     */
    public function commentsAction()
    {
        $view = $this->createViewModel();
        $view->comments = $this->getDbTable('seriesreviews')->getReviewsByUser(null);
        return $view;
    }

    /**
     * "Show series full text" page
     *
     * @return mixed
     */
    public function fulltextAction()
    {
        $view = $this->getViewModelWithSeries();
        if (!$view) {
            return $this->forwardTo(__NAMESPACE__ . '\Series', 'notfound');
        }
        $fuzzy = $this->params()->fromQuery('fuzzy', false);
        $view->fuzzy = $fuzzy;
        $rawSource = $this->params()->fromQuery('source');
        $view->source = $source = empty($rawSource) ? null : $rawSource;
        $view->sources = $this->getDbTable('fulltextsource')
            ->getList($view->series['Series_ID']);
        $view->fulltext = $this->getDbTable('editionsfulltext')
            ->getItemsWithFullText($view->series['Series_ID'], $fuzzy, $source);
        $view->setTemplate('geeby-deeby/item/fulltext');
        return $view;
    }

    /**
     * "Show series images" page
     *
     * @return mixed
     */
    public function imagesAction()
    {
        $view = $this->getViewModelWithSeries();
        if (!$view) {
            return $this->forwardTo(__NAMESPACE__ . '\Series', 'notfound');
        }
        $config = $this->serviceLocator->get('config');
        $groupByMaterial = $config['geeby-deeby']['groupSeriesByMaterialType']
            ?? true;
        $view->images = $this->getDbTable('editionsimages')
            ->getImagesForSeries($view->series['Series_ID'], $groupByMaterial);
        return $view;
    }

    /**
     * "Show series" page
     *
     * @return mixed
     */
    public function indexAction()
    {
        $id = $this->params()->fromRoute('id');
        if (null === $id) {
            $action = $this->rdfRequested() ? 'RDF' : 'List';
            $response = $this->redirect()->toRoute(
                'series',
                ['id' => $action],
                ['query' => $this->params()->fromQuery()]
            );
            $response->setStatusCode(303);
            return $response;
        }
        if ($id == 'List') {
            return $this->forwardTo(__NAMESPACE__ . '\Series', 'list');
        }
        if ($id == 'RDF') {
            return $this->forwardTo(__NAMESPACE__ . '\Series', 'rdf');
        }
        if ($id == 'Comments') {
            return $this->forwardTo(__NAMESPACE__ . '\Series', 'comments');
        }
        if ($id == 'New') {
            return $this->forwardTo(__NAMESPACE__ . '\Series', 'new');
        }
        return $this->performRdfRedirect('series');
    }

    /**
     * "Show series people" page
     *
     * @return mixed
     */
    public function peopleAction()
    {
        $view = $this->getViewModelWithSeries();
        if (!$view) {
            return $this->forwardTo(__NAMESPACE__ . '\Series', 'notfound');
        }
        $view->people = $this->getDbTable('editionscredits')
            ->getPeopleForSeries($view->series['Series_ID']);
        return $view;
    }

    /**
     * "Show series tags" page
     *
     * @return mixed
     */
    public function tagsAction()
    {
        $view = $this->getViewModelWithSeries();
        if (!$view) {
            return $this->forwardTo(__NAMESPACE__ . '\Series', 'notfound');
        }
        $view->tags = $this->getDbTable('tag')
            ->getTagsForSeries($view->series['Series_ID']);
        return $view;
    }

    /**
     * Return the RDF class(es) used for series, if any.
     *
     * @return array
     */
    protected function getSeriesRdfClass()
    {
        return [];
    }

    /**
     * Create a series resource for inclusion in the list.
     *
     * @param \EasyRdf\Graph $graph  Graph to update
     * @param object         $series Series object to update graph with
     *
     * @return \EasyRdf\Resource
     */
    protected function addSeriesToGraph($graph, $series)
    {
        $articleHelper = $this->serviceLocator->get('GeebyDeeby\Articles');
        $id = $series->Series_ID;
        $uri = $this->getServerUrl('series', ['id' => $id]);
        $seriesResource = $graph->resource($uri, $this->getSeriesRdfClass());
        $name = $series->Series_Name;
        $seriesResource->set(
            'dcterms:title',
            $articleHelper->formatTrailingArticles($name)
        );
        return $seriesResource;
    }

    /**
     * Get an RDF graph of all series.
     *
     * @return \EasyRdf\Graph
     */
    protected function getRdfList()
    {
        $list = $this->getDbTable('series')->getList();
        $graph = new \EasyRdf\Graph();
        foreach ($list as $series) {
            $this->addSeriesToGraph($graph, $series);
        }
        return $graph;
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
        $id = $view->series['Series_ID'];
        $articleHelper = $this->serviceLocator->get('GeebyDeeby\Articles');
        $uri = $this->getServerUrl('series', ['id' => $id]);
        $series = $graph->resource($uri, (array)$class + $this->getSeriesRdfClass());
        foreach ($view->seriesAttributes as $current) {
            if (!empty($current['Series_Attribute_RDF_Property'])) {
                $series->set(
                    $current['Series_Attribute_RDF_Property'],
                    $current['Series_Attribute_Value']
                );
            }
        }
        foreach ($view->relationshipsValues as $current) {
            if (!empty($current['predicate'])) {
                foreach ($current['values'] as $value) {
                    $series->add(
                        $current['predicate'],
                        $this->getServerUrl('series', ['id' => $value['Series_ID']])
                    );
                }
            }
        }
        $name = $view->series['Series_Name'];
        $series->set('dcterms:title', $articleHelper->formatTrailingArticles($name));
        return $series;
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
        // Special case -- no ID means show series list:
        $id = $this->params()->fromRoute('id');
        if (null === $id) {
            return $this->getRdfResponse($this->getRdfList());
        }

        $view = $this->getViewModelWithSeriesAndDetails();
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
        return ($view = $this->getViewModelWithSeriesAndDetails())
            ? $view : $this->forwardTo(__NAMESPACE__ . '\Series', 'notfound');
    }

    /**
     * Get the view model representing the series and all relevant related details.
     *
     * @return \Laminas\View\Model\ViewModel|bool
     */
    public function getViewModelWithSeriesAndDetails()
    {
        $view = $this->getViewModelWithSeries();
        if (!$view) {
            return false;
        }
        $id = $view->series['Series_ID'];
        $view->altTitles = $this->getDbTable('seriesalttitles')->getAltTitles($id);
        $view->categories = $this->getDbTable('seriescategories')
            ->getCategories($id);
        $config = $this->serviceLocator->get('config');
        $view->groupByMaterial = $config['geeby-deeby']['groupSeriesByMaterialType']
            ?? true;
        $view->items = $this->getDbTable('item')
            ->getItemsForSeries($id, true, $view->groupByMaterial);
        $view->language = $this->getDbTable('language')
            ->getByPrimaryKey($view->series['Language_ID']);
        $view->publishers = $this->getDbTable('seriespublishers')
            ->getPublishers($id);
        $trans = $this->getDbTable('seriestranslations');
        // The variable/function names are a bit unintuitive here --
        // $view->translatedInto is a list of books that $id was translated into;
        // we obtain these by calling $trans->getTranslatedFrom(), which gives
        // us a list of books that $id was translated from.
        $view->translatedInto = $trans->getTranslatedFrom($id, true);
        $view->translatedFrom = $trans->getTranslatedInto($id, true);
        $view->files = $this->getDbTable('seriesfiles')->getFilesForSeries($id);
        $view->bibliography = $this->getDbTable('seriesbibliography')
            ->getItemsDescribingSeries($id);
        $view->links = $this->getDbTable('serieslinks')->getLinksForSeries($id);
        $reviews = $this->getDbTable('seriesreviews');
        $view->comments = $reviews->getReviewsForSeries($id);
        $user = $this->getCurrentUser();
        if ($user) {
            $view->userHasComment = (bool)count(
                $reviews->select(
                    ['User_ID' => $user->User_ID, 'Series_ID' => $id]
                )
            );
        } else {
            $view->userHasComment = false;
        }
        return $view;
    }

    /**
     * List series action
     *
     * @return mixed
     */
    public function listAction()
    {
        return $this->createViewModel(
            [
                'series' => $this->getDbTable('series')->getList(),
            ]
        );
    }

    /**
     * New series action
     *
     * @return mixed
     */
    public function newAction()
    {
        $table = $this->getDbTable('series');
        $adapter = $table->getAdapter();
        $query = new \Laminas\Db\Sql\Select($table->getTable());
        $query->order('Series_ID DESC');
        $paginator = new \Laminas\Paginator\Paginator(
            new \Laminas\Paginator\Adapter\DbSelect(
                $query,
                $adapter
            )
        );
        $paginator->setItemCountPerPage(50);
        $paginator->setCurrentPageNumber($this->params()->fromQuery('page', 1));
        return $this->createViewModel(compact('paginator'));
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
