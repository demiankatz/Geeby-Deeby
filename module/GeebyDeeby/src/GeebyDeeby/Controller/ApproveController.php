<?php

/**
 * Approval controller
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

use GeebyDeeby\Db\Service\ItemService;
use GeebyDeeby\Db\Service\SeriesService;
use GeebyDeeby\Db\Service\UserService;

use function intval;

/**
 * Approval controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ApproveController extends AbstractBase
{
    /**
     * Approve a comment
     *
     * @return mixed
     */
    public function approvecommentAction()
    {
        return $this->approveReview('series');
    }

    /**
     * Approve a review
     *
     * @return mixed
     */
    public function approvereviewAction()
    {
        return $this->approveReview('item');
    }

    /**
     * Approve a user
     *
     * @return mixed
     */
    public function approveuserAction()
    {
        $ok = $this->checkPermission('Approver');
        if ($ok !== true) {
            return $this->jsonDie('Access denied.');
        }
        $id = $this->params()->fromPost('id');
        if (null === $id) {
            return $this->jsonDie('Missing ID value.');
        }
        $service = $this->getDbService(UserService::class);
        $user = $service->getByPrimaryKey($id);
        if (!$user) {
            return $this->jsonDie('Problem loading user data.');
        }
        if ($user->isApproved()) {
            return $this->jsonDie('User already approved.');
        }
        $person_id = intval($this->params()->fromPost('person_id'));
        $user->setPerson($person_id ? $person_id : null)
            ->setUsername($this->params()->fromPost('username'))
            ->setName($this->params()->fromPost('fullname'))
            ->setAddress($this->params()->fromPost('address'))
            ->setIsApproved(true);
        $service->persistEntity($user);
        try {
            $this->sendApprovalEmail($user->getAddress());
        } catch (\Exception $e) {
            return $this->jsonDie(
                'Problem sending email; user approved anyway. Details: '
                . $e->getMessage()
            );
        }
        return $this->jsonReportSuccess();
    }

    /**
     * Main approval action
     *
     * @return mixed
     */
    public function indexAction()
    {
        $ok = $this->checkPermission('Approver');
        if ($ok !== true) {
            return $ok;
        }
        $view = $this->createViewModel();
        $view->newUsers = $this->getDbService(UserService::class)->getList(false);
        $view->pendingReviews = $this->getDbTable('itemsreviews')->getReviewsByUser(null, 'n', false);
        $view->pendingComments = $this->getDbTable('seriesreviews')->getReviewsByUser(null, 'n');
        return $view;
    }

    /**
     * Reject a comment
     *
     * @return mixed
     */
    public function rejectcommentAction()
    {
        return $this->rejectReview('series');
    }

    /**
     * Reject a review
     *
     * @return mixed
     */
    public function rejectreviewAction()
    {
        return $this->rejectReview('item');
    }

    /**
     * Reject a user
     *
     * @return mixed
     */
    public function rejectuserAction()
    {
        $ok = $this->checkPermission('Approver');
        if ($ok !== true) {
            return $ok;
        }
        $id = $this->params()->fromPost('id');
        if (null === $id) {
            return $this->jsonDie('Missing ID value.');
        }
        $service = $this->getDbService(UserService::class);
        $user = $service->getByPrimaryKey($id);
        if (!$user) {
            return $this->jsonDie('Problem loading user data.');
        }
        if ($user->isApproved()) {
            return $this->jsonDie('User already approved.');
        }
        $service->deleteEntity($user);
        return $this->jsonReportSuccess();
    }

    /**
     * Approve a review or comment.
     *
     * @param string $type Review type: 'item' or 'series'
     *
     * @return mixed
     */
    public function approveReview($type)
    {
        $ok = $this->checkPermission('Approver');
        if ($ok !== true) {
            return $ok;
        }
        $userId = $this->params()->fromPost('user_id');
        $itemId = $this->params()->fromPost($type . '_id');
        $text = $this->params()->fromPost('text');
        if (null === $userId) {
            return $this->jsonDie('Missing User ID value.');
        }
        if (null === $itemId) {
            return $this->jsonDie('Missing ' . ucwords($type) . ' ID value.');
        }
        if (empty($text)) {
            return $this->jsonDie('Text must not be blank.');
        }

        $user = $this->getDbService(UserService::class)->getByPrimaryKey($userId);
        if (!$user) {
            return $this->jsonDie('Problem loading user data.');
        }

        $serviceName = $type == 'item' ? ItemService::class : SeriesService::class;
        $itemOrSeries = $this->getDbService($serviceName)->getByPrimaryKey($itemId);
        if (!$itemOrSeries) {
            return $this->jsonDie('Problem loading item data.');
        }

        $table = $this->getDbTable(
            $type == 'item' ? 'itemsreviews' : 'seriesreviews'
        );
        $userWhere = ['User_ID' => $userId];
        $itemWhere = [ucwords($type) . '_ID' => $itemId];
        $value = ['Review' => $text, 'Approved' => 'y', 'Added' => date('Y-m-d')];
        $table->update($value, $itemWhere + $userWhere + ['Approved' => 'n']);
        return $this->jsonReportSuccess();
    }

    /**
     * Reject a review or comment.
     *
     * @param string $type Review type: 'item' or 'series'
     *
     * @return mixed
     */
    public function rejectReview($type)
    {
        $ok = $this->checkPermission('Approver');
        if ($ok !== true) {
            return $ok;
        }
        $userId = $this->params()->fromPost('user_id');
        $itemId = $this->params()->fromPost($type . '_id');
        if (null === $userId) {
            return $this->jsonDie('Missing User ID value.');
        }
        if (null === $itemId) {
            return $this->jsonDie('Missing ' . ucwords($type) . ' ID value.');
        }

        $user = $this->getDbService(UserService::class)->getByPrimaryKey($userId);
        if (!$user) {
            return $this->jsonDie('Problem loading user data.');
        }

        $serviceName = $type == 'item' ? ItemService::class : SeriesService::class;
        $itemOrSeries = $this->getDbService($serviceName)->getByPrimaryKey($itemId);
        if (!$itemOrSeries) {
            return $this->jsonDie('Problem loading item data.');
        }

        $table = $this->getDbTable(
            $type == 'item' ? 'itemsreviews' : 'seriesreviews'
        );
        $userWhere = ['User_ID' => $userId];
        $itemWhere = [ucwords($type) . '_ID' => $itemId];
        $table->delete($itemWhere + $userWhere + ['Approved' => 'n']);
        return $this->jsonReportSuccess();
    }

    /**
     * Send an account approval email.  Report success or failure.
     *
     * @param string $address Target email address.
     *
     * @return bool
     */
    protected function sendApprovalEmail($address)
    {
        // If we don't have an address, report success -- we'll skip the email step:
        $address = trim($address);
        if (empty($address)) {
            return true;
        }
        $view = $this->getViewRenderer();
        $subject = $view->config('siteTitle') . ' Membership';
        $message = $view->render('emails/account-approval.phtml');
        $from = $view->config('siteEmail');
        return $this->serviceLocator->get(\GeebyDeeby\EmailService::class)
            ->send($address, $subject, $message, $from);
    }
}
