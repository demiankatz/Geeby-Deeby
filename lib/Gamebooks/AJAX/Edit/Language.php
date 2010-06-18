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
require_once 'Gamebooks/Tables/Language.php';

/**
 * Edit AJAX Support
 *
 * This class provides all AJAX functionality for the edit module.
 *
 * @author      Demian Katz
 * @access      public
 */
class AJAX_Edit_Language extends AJAX_Edit_Base
{
    /**
     * Display "edit language" form.
     *
     * @access  public
     */
    public function edit()
    {
        if (is_numeric($_GET['id'])) {
            $language = new Language($_GET['id']);
            $row = $language->getRow();
            if (!$row) {
                die('Cannot load language');
            }
            $this->interface->assign('language', $row);
        } else {
            $this->interface->assign('language', array('Language_ID' => 'NEW', 'Language_Name' => ''));
        }
        $this->interface->showSubPage('language_edit.tpl');
    }
    
    /**
     * Display languages list.
     *
     * @access  public
     */
    public function getList()
    {
        $languages = new LanguageList();
        $languages->assign($this->interface);
        $this->interface->showSubPage('language_list.tpl');
    }
    
    /**
     * Save changes to a language.
     *
     * @access  public
     */
    public function save()
    {
        // Extract values from the POST fields:
        $id = $_POST['id'] == 'NEW' ? false : intval($_POST['id']);
        $text = trim($_POST['language']);
        
        // Validate input:
        if (empty($text)) {
            $this->jsonDie('Language cannot be blank.');
        }
        
        // Attempt to save changes:
        $language = new Language($id);
        $language->set('Language_Name', $text);
        if (!$language->save()) {
            $this->jsonDie('Problem saving changes.');
        }
        
        // If we made it this far, we can report success:
        $this->jsonReportSuccess();
    }
}
?>