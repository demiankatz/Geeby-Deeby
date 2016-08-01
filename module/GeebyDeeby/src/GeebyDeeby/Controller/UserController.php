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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\Controller;
use Zend\Crypt\Password\Bcrypt;

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
        $view = $this->getViewModelWithUser();
        if (!$view) {
            return $this->forwardTo(__NAMESPACE__ . '\User', 'notfound');
        }
        $view->buyers = $this->getDbTable('collections')->compareCollections(
            $view->user['User_ID'], 'extra', 'want'
        );
        return $view;
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
     * Edit account details
     *
     * @return mixed
     */
    public function editAction()
    {
        $view = $this->getViewModelWithUser();
        if (!$view) {
            return $this->forwardTo(__NAMESPACE__ . '\User', 'notfound');
        }
        // Make sure user is logged in.
        if (!($user = $this->getCurrentUser())
            || $view->user['User_ID'] != $user->User_ID
        ) {
            return $this->forceLogin();
        }
        if (null !== $this->params()->fromPost('submit')) {
            $view->fullname = $this->params()->fromPost('Fullname');
            $view->address = $this->params()->fromPost('Address');
            $password1 = $this->params()->fromPost('Password1');
            $password2 = $this->params()->fromPost('Password2');
            if ($view->fullname == '') {
                $view->error = 'Please fill out all required fields.';
            } else if ($password1 != $password2
                || strpos($view->address, '://') !== false // block spam addresses
            ) {
                $view->error = 'Your passwords did not match. Please try again.';
            } else {
                $table = $this->getDbTable('user');
                $update = array(
                    'Name' => $view->fullname, 'Address' => $view->address
                );
                if (!empty($password1)) {
                    $bcrypt = new Bcrypt();
                    $update['Password_Hash'] = $bcrypt->create($password1);
                }
                $table->update(
                    $update, array('User_ID' => $view->user['User_ID'])
                );
                return $this->redirect()->toRoute(
                    'user', array('id' => $view->user['User_ID'])
                );
            }
        } else {
            $view->fullname = $view->user['Name'];
            $view->address = $view->user['Address'];
        }
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
        $view->comments = $this->getDbTable('seriesreviews')
            ->getReviewsByUser($view->user['User_ID']);
        $view->reviews = $this->getDbTable('itemsreviews')
            ->getReviewIDsByUser($view->user['User_ID']);
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
        $view = $this->getViewModelWithUser();
        if (!$view) {
            return $this->forwardTo(__NAMESPACE__ . '\User', 'notfound');
        }
        $view->sellers = $this->getDbTable('collections')->compareCollections(
            $view->user['User_ID'], 'want', 'extra'
        );
        return $view;
    }
}