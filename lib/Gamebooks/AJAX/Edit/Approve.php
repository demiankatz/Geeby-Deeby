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