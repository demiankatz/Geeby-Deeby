<?php
/**
 * Approval controller
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
            return $ok;
        }
        $id = $this->params()->fromPost('id');
        if (null === $id) {
            return $this->jsonDie('Missing ID value.');
        }
        $table = $this->getDbTable('user');
        $where = ['User_ID' => $id];
        $user = $table->select($where);
        if (count($user) < 1) {
            return $this->jsonDie('Problem loading user data.');
        }
        $row = current($user->toArray());
        if ($row['Person_ID'] != 0) {
            return $this->jsonDie('User already approved.');
        }
        $person_id = intval($this->params()->fromPost('person_id'));
        if ($person_id === 0) {
            return $this->jsonDie('Invalid Person ID.');
        }
        $row['Person_ID'] = $person_id;
        $row['Username'] = $this->params()->fromPost('username');
        $row['Name'] = $this->params()->fromPost('fullname');
        $row['Address'] = $this->params()->fromPost('address');
        $table->update($row, $where);
        if (!$this->sendApprovalEmail($row['Address'])) {
            return $this->jsonDie('Problem sending email; user approved anyway.');
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
        $view->newUsers = $this->getDbTable('user')->getUnapproved();
        $view->pendingReviews = $this->getDbTable('itemsreviews')
            ->getReviewsByUser(null, 'n', false);
        $view->pendingComments = $this->getDbTable('seriesreviews')
            ->getReviewsByUser(null, 'n');
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
        $table = $this->getDbTable('user');
        $where = ['User_ID' => $id];
        $user = $table->select($where);
        if (count($user) < 1) {
            return $this->jsonDie('Problem loading user data.');
        }
        $row = current($user->toArray());
        if ($row['Person_ID'] != 0) {
            return $this->jsonDie('User already approved.');
        }
        $table->delete($where);
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

        $userWhere = ['User_ID' => $userId];
        $user = $this->getDbTable('user')->select($userWhere);
        if (count($user) < 1) {
            return $this->jsonDie('Problem loading user data.');
        }

        $itemWhere = [ucwords($type) . '_ID' => $itemId];
        $item = $this->getDbTable($type)->select($itemWhere);
        if (count($item) < 1) {
            return $this->jsonDie('Problem loading item data.');
        }

        $table = $this->getDbTable(
            $type == 'item' ? 'itemsreviews' : 'seriesreviews'
        );
        $value = ['Review' => $text, 'Approved' => 'y'];
        $table->update($value, $itemWhere + $userWhere + ['Approved' => 'n']);
        $recentTable = $this->getDbTable('recentreviews');
        try {
            $recentTable->insert(
                [
                    'User_ID' => $userId,
                    'Item_ID' => $itemId,
                    'Type' => $type,
                    'Added' => date('Y-m-d'),
                ]
            );
        } catch (\Zend\Db\Adapter\Exception\RuntimeException $e) {
            // Ignore duplicate insert errors, but rethrow others....
            if (strpos($e->getMessage(), 'Duplicate entry') !== 0) {
                throw $e;
            }
        }
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

        $userWhere = ['User_ID' => $userId];
        $user = $this->getDbTable('user')->select($userWhere);
        if (count($user) < 1) {
            return $this->jsonDie('Problem loading user data.');
        }

        $itemWhere = [ucwords($type) . '_ID' => $itemId];
        $item = $this->getDbTable($type)->select($itemWhere);
        if (count($item) < 1) {
            return $this->jsonDie('Problem loading item data.');
        }

        $table = $this->getDbTable(
            $type == 'item' ? 'itemsreviews' : 'seriesreviews'
        );
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
        $subject = $view->config('siteTitle') . " Membership";
        $message = $view->render('emails/account-approval.phtml');
        $from = "From: " . $view->config('siteEmail');
        return @mail($address, $subject, $message, $from);
    }
}
