<?php

/**
 * User controller
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

use GeebyDeeby\Crypt\PasswordHasher;
use GeebyDeeby\Db\Service\CollectionService;
use GeebyDeeby\Db\Service\ItemsReviewService;
use GeebyDeeby\Db\Service\SeriesReviewService;
use GeebyDeeby\Db\Service\UserService;

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
        $service = $this->getDbService(UserService::class);
        $entity = (null === $id) ? null : $service->getByPrimaryKey($id);
        if (!$entity) {
            return false;
        }
        return $this->createViewModel(['user' => $entity->toArray()]);
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
        $collection = $this->getDbService(CollectionService::class)
            ->getForUser($view->user['User_ID'], ['have', 'want'], true);
        // Format the data for more convenient display:
        $formatted = [];
        $seriesNames = [];
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
        $view->buyers = $this->getDbService(CollectionService::class)->compareCollections(
            $view->user['User_ID'],
            'extra',
            'want'
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
        $view->comments = $this->getDbService(SeriesReviewService::class)->getReviewsByUser($view->user['User_ID']);
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
        if (
            !($user = $this->getCurrentUser())
            || $view->user['User_ID'] != $user->User_ID
        ) {
            return $this->forceLogin();
        }
        if (null !== $this->params()->fromPost('submit')) {
            $view->fullname = $this->params()->fromPost('Fullname');
            $view->address = $this->params()->fromPost('Address');
            $password = $this->params()->fromPost('Password');
            $password1 = $this->params()->fromPost('Password1');
            $password2 = $this->params()->fromPost('Password2');
            if ($view->fullname == '') {
                $view->error = 'Please fill out all required fields.';
            } elseif (
                $password1 != $password2
                || str_contains($view->address, '://')   // block spam addresses
            ) {
                $view->error = 'Your passwords did not match. Please try again.';
            } else {
                try {
                    $passwordCheck = $this->getAuthenticationAdapter(
                        $view->user['Username'],
                        $password
                    )->authenticate();
                } catch (\Exception $e) {
                    $passwordCheck = null;
                }
                if (!empty($password1) && !($passwordCheck?->isValid())) {
                    $view->error = 'The existing password you provided is incorrect.';
                } else {
                    $service = $this->getDbService(UserService::class);
                    $user = $service->getByPrimaryKey($view->user['User_ID']);
                    $user->setName($view->fullname)
                        ->setAddress($view->address);
                    if (!empty($password1)) {
                        $hasher = new PasswordHasher();
                        $user->setPasswordHash($hasher->create($password1));
                    }
                    $service->persistEntity($user);
                    return $this->redirect()->toRoute(
                        'user',
                        ['id' => $view->user['User_ID']]
                    );
                }
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
        $view->extras = $this->getDbService(CollectionService::class)
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
        $view->stats = $this->getDbService(CollectionService::class)->getUserStatistics($view->user['User_ID']);
        $view->comments = $this->getDbService(SeriesReviewService::class)->getReviewsByUser($view->user['User_ID']);
        $view->reviews = $this->getDbService(ItemsReviewService::class)->getReviewIDsByUser($view->user['User_ID']);
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
            ['users' => $this->getDbService(UserService::class)->getList(true)]
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
        $view->reviews = $this->getDbService(ItemsReviewService::class)->getReviewsByUser($view->user['User_ID']);
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
        $view->sellers = $this->getDbService(CollectionService::class)->compareCollections(
            $view->user['User_ID'],
            'want',
            'extra'
        );
        return $view;
    }
}
