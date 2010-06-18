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
require_once 'Gamebooks/Tables/MaterialType.php';

/**
 * Edit AJAX Support
 *
 * This class provides all AJAX functionality for the edit module.
 *
 * @author      Demian Katz
 * @access      public
 */
class AJAX_Edit_MaterialType extends AJAX_Edit_Base
{
    /**
     * Display "edit material type" form.
     *
     * @access  public
     */
    public function edit()
    {
        if (is_numeric($_GET['id'])) {
            $material = new MaterialType($_GET['id']);
            $row = $material->getRow();
            if (!$row) {
                die('Cannot load material type');
            }
            $this->interface->assign('material', $row);
        } else {
            $this->interface->assign('material', array('Material_Type_ID' => 'NEW', 'Material_Type_Name' => ''));
        }
        $this->interface->showSubPage('material_edit.tpl');
    }
    
    /**
     * Display materials list.
     *
     * @access  public
     */
    public function getList()
    {
        $materials = new MaterialTypeList();
        $materials->assign($this->interface);
        $this->interface->showSubPage('material_list.tpl');
    }
    
    /**
     * Save changes to a material type.
     *
     * @access  public
     */
    public function save()
    {
        // Extract values from the POST fields:
        $id = $_POST['id'] == 'NEW' ? false : intval($_POST['id']);
        $text = trim($_POST['material']);
        
        // Validate input:
        if (empty($text)) {
            $this->jsonDie('Material type cannot be blank.');
        }
        
        // Attempt to save changes:
        $material = new MaterialType($id);
        $material->set('Material_Type_Name', $text);
        if (!$material->save()) {
            $this->jsonDie('Problem saving changes.');
        }
        
        // If we made it this far, we can report success:
        $this->jsonReportSuccess();
    }
}
?>