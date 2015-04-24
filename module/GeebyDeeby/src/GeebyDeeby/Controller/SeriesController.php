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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\Controller;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;

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
    protected function getViewModelWithSeries($extras = array())
    {
        $id = $this->params()->fromRoute('id');
        $table = $this->getDbTable('series');
        $rowObj = (null === $id) ? null : $table->getByPrimaryKey($id);
        if (!is_object($rowObj)) {
            return false;
        }
        $extras['seriesAttributes'] = $this->getDbTable('seriesattributesvalues')
            ->getAttributesForSeries($id);

        return $this->createViewModel(
            array('series' => $rowObj->toArray()) + $extras
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

        // Check for missing credits
        $editions = $this->getDbTable('edition');
        $callback = function ($select) use ($seriesId) {
            $select->join(
                array('ec' => 'Editions_Credits'),
                'Editions.Edition_ID = ec.Edition_ID',
                [], Select::JOIN_LEFT
            );
            $select->join(
                array('i' => 'Items'),
                'Editions.Item_ID = i.Item_ID',
                ['Item_Name'], Select::JOIN_LEFT
            );
            $select->where->isNull('ec.Person_ID');
            $select->where(['Series_ID' => $seriesId]);
            $select->order('Editions.Position');
        };
        $view->missingCredits = $editions->select($callback)->toArray();

        // Check for missing dates
        $callback = function ($select) use ($seriesId) {
            $select->join(
                array('d' => 'Editions_Release_Dates'),
                'Editions.Edition_ID = d.Edition_ID',
                [], Select::JOIN_LEFT
            );
            $select->join(
                array('i' => 'Items'),
                'Editions.Item_ID = i.Item_ID',
                ['Item_Name'], Select::JOIN_LEFT
            );
            $select->where->isNull('d.Year');
            $select->where(['Series_ID' => $seriesId]);
            $select->order('Editions.Position');
        };
        $view->missingDates = $editions->select($callback)->toArray();

        // Get date range stats
        $callback = function ($select) use ($seriesId) {
            $select->where(['Series_ID' => $seriesId]);
            $select->columns(
                [
                    'Edition_ID' => new Expression(
                        'min(?)', ['Editions.Edition_ID'],
                        [Expression::TYPE_IDENTIFIER]
                    ),
                ]
            );
            $select->join(
                array('d' => 'Editions_Release_Dates'),
                'Editions.Edition_ID = d.Edition_ID',
                [
                    'Start' => new Expression(
                        'min(?)', ['Year'], [Expression::TYPE_IDENTIFIER]
                    ),
                    'End' => new Expression(
                        'max(?)', ['Year'], [Expression::TYPE_IDENTIFIER]
                    ),
                ], Select::JOIN_LEFT
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
                        'min(?)', ['Edition_ID'], [Expression::TYPE_IDENTIFIER]
                    ),
                    'Pos' => new Expression(
                        'min(?)', ['Position'], [Expression::TYPE_IDENTIFIER]
                    ),
                    'Total' => new Expression(
                        'count(?)', ['Position'], [Expression::TYPE_IDENTIFIER]
                    ),
                ]
            );
            $select->group('Position');
            $select->order('Position');
        };
        $results = $editions->select($callback)->toArray();
        $last = $min = $max = $total = 0;
        $dupes = $missing = [];
        foreach ($results as $current) {
            $pos = $current['Pos'];
            $total += $current['Total'];
            if ($current['Total'] > 1) {
                $dupes[] = $pos;
            }
            if ($last > 0) {
                for ($i = $last + 1; $i < $pos; $i++) {
                    $missing[] = $i;
                }
            }
            if ($min == 0) {
                $min = $pos;
            }
            if ($pos > $max) {
                $max = $pos;
            }
            $last = $pos;
        }
        $view->itemStats = array(
            'Different' =>  count($results),
            'Start' => $min,
            'End' => $max,
            'Total' => $total,
            'Dupes' => $dupes,
            'Missing' => $missing,
        );

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
        $params = array(
            'Series_ID' => $this->params()->fromRoute('id'),
            'User_ID' => $user->User_ID
        );

        $existing = $table->select($params)->toArray();
        $existing = count($existing) > 0 ? $existing[0] : false;

        // Save comment if found.
        if ($this->getRequest()->isPost()) {
            $view = $this->createViewModel(
                array('noChange' => false, 'series' => $params['Series_ID'])
            );
            $params['Approved'] = 'n';
            $params['Review'] = $this->params()->fromPost('Review');
            if ($params['Review'] == $existing['Review']) {
                $view->noChange = true;
            } else {
                if ($existing) {
                    $table->update($params);
                } else {
                    $table->insert($params);
                }
            }
            $view->setTemplate('geeby-deeby/series/comment-submitted');
            return $view;
        }

        // Send review to the view.
        $review = $existing ? $existing['Review'] : '';

        $view = $this->getViewModelWithSeries(array('review' => $review));
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
        $view->fulltext = $this->getDbTable('editionsfulltext')
            ->getItemsWithFullText($view->series['Series_ID'], $fuzzy);
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
        $view->images = $this->getDbTable('editionsimages')
            ->getImagesForSeries($view->series['Series_ID']);
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
            return $this->forwardTo(__NAMESPACE__ . '\Series', 'list');
        }
        if ($id == 'Comments') {
            return $this->forwardTo(__NAMESPACE__ . '\Series', 'comments');
        }
        $view = $this->getViewModelWithSeries();
        if (!$view) {
            return $this->forwardTo(__NAMESPACE__ . '\Series', 'notfound');
        }
        $view->altTitles = $this->getDbTable('seriesalttitles')->getAltTitles($id);
        $view->categories = $this->getDbTable('seriescategories')
            ->getCategories($id);
        $view->items = $this->getDbTable('item')->getItemsForSeries($id);
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
                    array('User_ID' => $user->User_ID, 'Series_ID' => $id)
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
            array(
                'series' => $this->getDbTable('series')->getList()
            )
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
