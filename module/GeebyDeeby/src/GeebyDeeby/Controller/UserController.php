<?php
/**
 * User controller
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

/**
 * User controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class UserController extends AbstractBase
{
    /**
     * Get a view model containing a user object (or return false if user missing)
     *
     * @return mixed
     */
    protected function getViewModelWithUser()
    {
        $id = $this->params()->fromRoute('id');
        $table = $this->getDbTable('user');
        $rowObj = (null === $id) ? null : $table->getByPrimaryKey($id);
        if (!is_object($rowObj)) {
            return false;
        }
        return $this->createViewModel(
            array('user' => $rowObj->toArray())
        );
    }

    /**
     * Collection have/want page
     *
     * @return mixed
     */
    public function collectionAction()
    {
        $view = $this->getViewModelWithUser();
        if (!$view) {
            return $this->forwardTo(__NAMESPACE__ . '\User', 'notfound');
        }
        $collection = $this->getDbTable('collections')
            ->getForUser($view->user['User_ID'], array('have', 'want'), true);
        // Format the data for more convenient display:
        $formatted = array();
        $seriesNames = array();
        foreach ($collection as $current) {
            $lang = $current['Language_Name'];
            $series = $current['Series_ID'];
            $seriesNames[$series] = $current['Series_Name'];
            $type = $current['Collection_Status'];
            $formatted[$lang][$series][$type][] = $current;
        }
        $view->collection = $formatted;
        $view->seriesNames = $seriesNames;
        return $view;
    }

    /**
     * Potential buyers page
     *
     * @return mixed
     */
    public function buyersAction()
    {
        // TODO
    }

    /**
     * Comments page
     *
     * @return mixed
     */
    public function commentsAction()
    {
        $view = $this->getViewModelWithUser();
        if (!$view) {
            return $this->forwardTo(__NAMESPACE__ . '\User', 'notfound');
        }
        $view->comments = $this->getDbTable('seriesreviews')
            ->getReviewsByUser($view->user['User_ID']);
        return $view;
    }

    /**
     * Extra books in collection page
     *
     * @return mixed
     */
    public function extrasAction()
    {
        $view = $this->getViewModelWithUser();
        if (!$view) {
            return $this->forwardTo(__NAMESPACE__ . '\User', 'notfound');
        }
        $view->extras = $this->getDbTable('collections')
            ->getForUser($view->user['User_ID'], 'extra');
        return $view;
    }

    /**
     * "Show user" page
     *
     * @return mixed
     */
    public function indexAction()
    {
        $view = $this->getViewModelWithUser();
        if (!$view) {
            return $this->forwardTo(__NAMESPACE__ . '\User', 'notfound');
        }
        $view->stats = $this->getDbTable('collections')
            ->getUserStatistics($view->user['User_ID']);
        return $view;
    }

    /**
     * User list
     *
     * @return mixed
     */
    public function listAction()
    {
        return $this->createViewModel(
            array('users' => $this->getDbTable('user')->getList())
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
     * Reviews page
     *
     * @return mixed
     */
    public function reviewsAction()
    {
        $view = $this->getViewModelWithUser();
        if (!$view) {
            return $this->forwardTo(__NAMESPACE__ . '\User', 'notfound');
        }
        $view->reviews = $this->getDbTable('itemsreviews')
            ->getReviewsByUser($view->user['User_ID']);
        return $view;
    }

    /**
     * Potential sellers page
     *
     * @return mixed
     */
    public function sellersAction()
    {
        // TODO
    }
}