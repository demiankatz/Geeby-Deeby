<?php
/**
  *
  * Copyright (c) Demian Katz 2010.
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
  */
require_once 'Gamebooks/AJAX/Edit/Base.php';
require_once 'Gamebooks/Tables/User.php';
require_once 'Gamebooks/Tables/Item.php';
require_once 'Gamebooks/Tables/Series.php';

/**
 * Edit AJAX Support
 *
 * This class provides approval-related AJAX functionality.
 *
 * @author      Demian Katz
 * @access      public
 */
class AJAX_Edit_Approve extends AJAX_Edit_Base
{
    /**
     * Constructor
     *
     * @access  public
     */
    public function __construct()
    {
        // Override default required permission:
        $this->requiredPermission = 'Approver';
        parent::__construct();
    }

    /**
     * Send an email to an approved user.
     *
     * @param string $address Email address.
     *
     * @return bool
     * @access private
     */
    private function _sendApprovalEmail($address)
    {
        // If we don't have an address, report success -- we'll skip the email step:
        $address = trim($address);
        if (empty($address)) {
            return true;
        }
        $subject = GAMEBOOKS_SITE_NAME . " Membership";
        $message = "I have just approved your membership to " . GAMEBOOKS_SITE_NAME . ".\n";
        $message .= "Thanks for signing up!  You can now start building \n";
        $message .= "collections and submitting reviews.  Please let me know\n"; 
        $message .= "if you have any questions or suggestions.\n\n";
        $message .= "- " . GAMEBOOKS_SITE_OWNER;
        $from = "From: " . GAMEBOOKS_SITE_EMAIL;
        return @mail($address, $subject, $message, $from);
    }

    /**
     * Accept a pending user.
     *
     * @access  public
     */
    public function approveUser()
    {
        if (!isset($_REQUEST['id'])) {
            $this->jsonDie('Missing ID value.');
        }
        
        $id = intval($_REQUEST['id']);
        $person_id = intval($_REQUEST['person_id']);
        $user = new User($id);
        $row = $user->getRow();
        if (!$row) {
            $this->jsonDie('Problem loading user data.');
        }
        if ($row['Person_ID'] != 0) {
            $this->jsonDie('User already approved.');
        }
        if ($person_id === 0) {
            $this->jsonDie('Invalid Person ID.');
        }
        $user->set('Person_ID', $person_id);
        $user->set('Username', $_REQUEST['username']);
        $user->set('Name', $_REQUEST['fullname']);
        $user->set('Address', $_REQUEST['address']);
        if (!$user->save()) {
            $this->jsonDie('Cannot approve user.');
        }
        if (!$this->_sendApprovalEmail($_REQUEST['address'])) {
            $this->jsonDie('Problem sending email; user approved anyway.');
        }
        $this->jsonReportSuccess();
    }
    
    /**
     * Reject a pending user.
     *
     * @access  public
     */
    public function rejectUser()
    {
        if (!isset($_REQUEST['id'])) {
            $this->jsonDie('Missing ID value.');
        }
        
        $id = intval($_REQUEST['id']);
        $user = new User($id);
        $row = $user->getRow();
        if (!$row) {
            $this->jsonDie('Problem loading user data.');
        }
        if ($row['Person_ID'] != 0) {
            $this->jsonDie('User already approved.');
        }
        if (!$user->delete()) {
            $this->jsonDie('Cannot reject user.');
        } else {
            $this->jsonReportSuccess();
        }
    }
    
    /**
     * Reject a pending item review.
     *
     * @access  public
     */
    public function rejectReview()
    {
        if (!isset($_REQUEST['user_id'])) {
            $this->jsonDie('Missing User ID value.');
        }
        if (!isset($_REQUEST['item_id'])) {
            $this->jsonDie('Missing Item ID value.');
        }
        
        $user_id = intval($_REQUEST['user_id']);
        $user = new User($user_id);
        $row = $user->getRow();
        if (!$row) {
            $this->jsonDie('Problem loading user data.');
        }
        
        $item_id = intval($_REQUEST['item_id']);
        $item = new Item($item_id);
        $row = $item->getRow();
        if (!$row) {
            $this->jsonDie('Problem loading item data.');
        }
        
        if (!$item->rejectReview($user_id)) {
            $this->jsonDie('Cannot reject review.');
        } else {
            $this->jsonReportSuccess();
        }
    }
    
    /**
     * Reject a pending series comment.
     *
     * @access  public
     */
    public function rejectComment()
    {
        if (!isset($_REQUEST['user_id'])) {
            $this->jsonDie('Missing User ID value.');
        }
        if (!isset($_REQUEST['series_id'])) {
            $this->jsonDie('Missing Series ID value.');
        }
        
        $user_id = intval($_REQUEST['user_id']);
        $user = new User($user_id);
        $row = $user->getRow();
        if (!$row) {
            $this->jsonDie('Problem loading user data.');
        }
        
        $series_id = intval($_REQUEST['series_id']);
        $series = new Series($series_id);
        $row = $series->getRow();
        if (!$row) {
            $this->jsonDie('Problem loading series data.');
        }
        
        if (!$series->rejectComment($user_id)) {
            $this->jsonDie('Cannot reject comment.');
        } else {
            $this->jsonReportSuccess();
        }
    }
}
?>