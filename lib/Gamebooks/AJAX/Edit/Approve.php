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
}
?>