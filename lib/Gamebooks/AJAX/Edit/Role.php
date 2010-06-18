<?php
/**
  *
  * Copyright (c) Demian Katz 2009.
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
require_once 'Gamebooks/Tables/Role.php';

/**
 * Edit AJAX Support
 *
 * This class provides all AJAX functionality for the edit module.
 *
 * @author      Demian Katz
 * @access      public
 */
class AJAX_Edit_Role extends AJAX_Edit_Base
{
    /**
     * Display "edit role" form.
     *
     * @access  public
     */
    public function edit()
    {
        if (is_numeric($_GET['id'])) {
            $role = new Role($_GET['id']);
            $row = $role->getRow();
            if (!$row) {
                die('Cannot load role');
            }
            $this->interface->assign('role', $row);
        } else {
            $this->interface->assign('role', array('Role_ID' => 'NEW', 'Role_Name' => ''));
        }
        $this->interface->showSubPage('role_edit.tpl');
    }
    
    /**
     * Display roles list.
     *
     * @access  public
     */
    public function getList()
    {
        $roles = new RoleList();
        $roles->assign($this->interface);
        $this->interface->showSubPage('role_list.tpl');
    }
    
    /**
     * Save changes to a role.
     *
     * @access  public
     */
    public function save()
    {
        // Extract values from the POST fields:
        $id = $_POST['id'] == 'NEW' ? false : intval($_POST['id']);
        $text = trim($_POST['role']);
        
        // Validate input:
        if (empty($text)) {
            $this->jsonDie('Role cannot be blank.');
        }
        
        // Attempt to save changes:
        $role = new Role($id);
        $role->set('Role_Name', $text);
        if (!$role->save()) {
            $this->jsonDie('Problem saving changes.');
        }
        
        // If we made it this far, we can report success:
        $this->jsonReportSuccess();
    }
}
?>