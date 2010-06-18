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
require_once 'Gamebooks/Tables/Publisher.php';

/**
 * Edit AJAX Support
 *
 * This class provides all AJAX functionality for the edit module.
 *
 * @author      Demian Katz
 * @access      public
 */
class AJAX_Edit_Publisher extends AJAX_Edit_Base
{
    /**
     * Display "edit publisher" form.
     *
     * @access  public
     */
    public function edit()
    {
        if (is_numeric($_GET['id'])) {
            $publisher = new Publisher($_GET['id']);
            $row = $publisher->getRow();
            if (!$row) {
                die('Cannot load publisher');
            }
            $this->interface->assign('publisher', $row);
        } else {
            $this->interface->assign('publisher', array('Publisher_ID' => 'NEW', 'Publisher_Name' => ''));
        }
        $this->interface->showSubPage('publisher_edit.tpl');
    }
    
    /**
     * Display publishers list.
     *
     * @access  public
     */
    public function getList()
    {
        $publishers = new PublisherList();
        $publishers->assign($this->interface);
        $this->interface->showSubPage('publisher_list.tpl');
    }
    
    /**
     * Save changes to a publisher.
     *
     * @access  public
     */
    public function save()
    {
        // Extract values from the POST fields:
        $id = $_POST['id'] == 'NEW' ? false : intval($_POST['id']);
        $text = trim($_POST['publisher']);
        
        // Validate input:
        if (empty($text)) {
            $this->jsonDie('Publisher cannot be blank.');
        }
        
        // Attempt to save changes:
        $publisher = new Publisher($id);
        $publisher->set('Publisher_Name', $text);
        if (!$publisher->save()) {
            $this->jsonDie('Problem saving changes.');
        }
        
        // If we made it this far, we can report success:
        $this->jsonReportSuccess();
    }
    
    /**
     * Get list for autosuggest field
     *
     * @access  public
     */
    public function suggest()
    {
        $pub = new PublisherList();
        $suggestions = $pub->getSuggestions($_GET['q'], $_GET['limit']);
        foreach($suggestions as $s) {
            $line = "{$s['Publisher_ID']}: {$s['Publisher_Name']}\n";
            echo htmlspecialchars($line);
        }
    }
}
?>