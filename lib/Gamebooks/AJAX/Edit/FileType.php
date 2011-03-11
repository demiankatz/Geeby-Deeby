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
require_once 'Gamebooks/Tables/FileType.php';

/**
 * Edit AJAX Support
 *
 * This class provides all AJAX functionality for the edit module.
 *
 * @author      Demian Katz
 * @access      public
 */
class AJAX_Edit_FileType extends AJAX_Edit_Base
{
    /**
     * Display "edit file type" form.
     *
     * @access  public
     */
    public function edit()
    {
        if (is_numeric($_GET['id'])) {
            $lt = new FileType($_GET['id']);
            $row = $lt->getRow();
            if (!$row) {
                die('Cannot load file type');
            }
            $this->interface->assign('fileType', $row);
        } else {
            $this->interface->assign('fileType', array('File_Type_ID' => 'NEW', 'File_Type' => ''));
        }
        $this->interface->showSubPage('file_type_edit.tpl');
    }
    
    /**
     * Display file type list.
     *
     * @access  public
     */
    public function getList()
    {
        $types = new FileTypeList();
        $types->assign($this->interface);
        $this->interface->showSubPage('file_type_list.tpl');
    }
    
    /**
     * Save changes to a file type.
     *
     * @access  public
     */
    public function save()
    {
        // Extract values from the POST fields:
        $id = $_POST['id'] == 'NEW' ? false : intval($_POST['id']);
        $text = trim($_POST['fileType']);
        
        // Validate input:
        if (empty($text)) {
            $this->jsonDie('File type cannot be blank.');
        }
        
        // Attempt to save changes:
        $lt = new FileType($id);
        $lt->set('File_Type', $text);
        if (!$lt->save()) {
            $this->jsonDie('Problem saving changes.');
        }
        
        // If we made it this far, we can report success:
        $this->jsonReportSuccess();
    }
}
?>