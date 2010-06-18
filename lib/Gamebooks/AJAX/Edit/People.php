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
require_once 'Gamebooks/Tables/Person.php';

/**
 * Edit AJAX Support
 *
 * This class provides all AJAX functionality for the edit module.
 *
 * @author      Demian Katz
 * @access      public
 */
class AJAX_Edit_People extends AJAX_Edit_Base
{
    /**
     * Add a pseudonym to a person.
     *
     * @access  public
     */
    public function addPseudonym()
    {
        $real_id = intval($_POST['real_id']);
        $pseudo_id = intval($_POST['pseudo_id']);
        
        if ($real_id == $pseudo_id) {
            $this->jsonDie('You cannot link a person to itself.');
        }
        
        $person = new Person($real_id);
        if ($person->addPseudonym($pseudo_id)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem storing relationship.');
        }
    }
    
    /**
     * Remove a pseudonym from a person.
     *
     * @access  public
     */
    public function deletePseudonym()
    {
        $real_id = intval($_POST['real_id']);
        $pseudo_id = intval($_POST['pseudo_id']);
        
        $person = new Person($real_id);
        if ($person->deletePseudonym($pseudo_id)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem removing relationship.');
        }
    }
    
    /**
     * Display "edit person" form.
     *
     * @access  public
     */
    public function edit()
    {
        if (is_numeric($_GET['id'])) {
            $person = new Person($_GET['id']);
            $row = $person->getRow();
            if (!$row) {
                die('Cannot load person');
            }
            $this->interface->assign('person', $row);
        } else {
            $this->interface->assign('person', array('Person_ID' => 'NEW'));
        }
        $this->interface->showSubPage('people_edit.tpl');
    }
    
    /**
     * Display people list.
     *
     * @access  public
     */
    public function getList()
    {
        $people = new PersonList();
        $people->assign($this->interface);
        $this->interface->showSubPage('people_list.tpl');
    }
    
    /**
     * Display pseudonym list.
     *
     * @access  public
     */
    public function getPseudonymList()
    {
        $people = new PersonList();
        $results = $people->getPseudonyms($_GET['id']);
        $this->interface->assign('pseudonyms', $results);
        $this->interface->showSubPage('people_pseudonyms.tpl');
    }
    
    /**
     * Display real name list.
     *
     * @access  public
     */
    public function getRealNameList()
    {
        $people = new PersonList();
        $this->interface->assign('realnames', $people->getRealNames($_GET['id']));
        $this->interface->showSubPage('people_realnames.tpl');
    }
    
    /**
     * Save changes to a person.
     *
     * @access  public
     */
    public function save()
    {
        // Extract values from the POST fields:
        $id = $_POST['id'] == 'NEW' ? false : intval($_POST['id']);
        $first = trim($_POST['first']);
        $middle = trim($_POST['middle']);
        $last = trim($_POST['last']);
        $bio = trim($_POST['bio']);
        
        // Validate input:
        if (empty($last)) {
            $this->jsonDie('Last name cannot be blank.');
        }
        
        // Attempt to save changes:
        $person = new Person($id);
        $person->set('First_Name', $first);
        $person->set('Middle_Name', $middle);
        $person->set('Last_Name', $last);
        $person->set('Biography', $bio);
        if (!$person->save()) {
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
        $people = new PersonList();
        $suggestions = $people->getSuggestions($_GET['q'], $_GET['limit']);
        foreach($suggestions as $s) {
            $line = "{$s['Person_ID']}: {$s['First_Name']} {$s['Middle_Name']} {$s['Last_Name']}\n";
            echo htmlspecialchars(str_replace('  ', ' ', $line));
        }
    }
}
?>