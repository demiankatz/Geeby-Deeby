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
require_once 'Gamebooks/Tables/Category.php';

/**
 * Edit AJAX Support
 *
 * This class provides all AJAX functionality for the edit module.
 *
 * @author      Demian Katz
 * @access      public
 */
class AJAX_Edit_Category extends AJAX_Edit_Base
{
    /**
     * Display "edit category" form.
     *
     * @access  public
     */
    public function edit()
    {
        if (is_numeric($_GET['id'])) {
            $category = new Category($_GET['id']);
            $row = $category->getRow();
            if (!$row) {
                die('Cannot load category');
            }
            $this->interface->assign('category', $row);
        } else {
            $this->interface->assign('category', array('Category_ID' => 'NEW'));
        }
        $this->interface->showSubPage('category_edit.tpl');
    }
    
    /**
     * Display categories list.
     *
     * @access  public
     */
    public function getList()
    {
        $categories = new CategoryList();
        $categories->assign($this->interface);
        $this->interface->showSubPage('category_list.tpl');
    }
    
    /**
     * Save changes to a category.
     *
     * @access  public
     */
    public function save()
    {
        // Extract values from the POST fields:
        $id = $_POST['id'] == 'NEW' ? false : intval($_POST['id']);
        $name = trim($_POST['name']);
        $desc = trim($_POST['desc']);
        
        // Validate input:
        if (empty($name)) {
            $this->jsonDie('Category name cannot be blank.');
        }
        
        // Attempt to save changes:
        $category = new Category($id);
        $category->set('Category', $name);
        $category->set('Description', $desc);
        if (!$category->save()) {
            $this->jsonDie('Problem saving changes.');
        }
        
        // If we made it this far, we can report success:
        $this->jsonReportSuccess();
    }
}
?>