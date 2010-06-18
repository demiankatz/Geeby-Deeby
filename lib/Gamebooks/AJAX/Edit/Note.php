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
require_once 'Gamebooks/Tables/Note.php';

/**
 * Edit AJAX Support
 *
 * This class provides all AJAX functionality for the edit module.
 *
 * @author      Demian Katz
 * @access      public
 */
class AJAX_Edit_Note extends AJAX_Edit_Base
{
    /**
     * Display "edit note" form.
     *
     * @access  public
     */
    public function edit()
    {
        if (is_numeric($_GET['id'])) {
            $note = new Note($_GET['id']);
            $row = $note->getRow();
            if (!$row) {
                die('Cannot load note');
            }
            $this->interface->assign('note', $row);
        } else {
            $this->interface->assign('note', array('Note_ID' => 'NEW', 'Note' => ''));
        }
        $this->interface->showSubPage('note_edit.tpl');
    }
    
    /**
     * Display notes list.
     *
     * @access  public
     */
    public function getList()
    {
        $notes = new NoteList();
        $notes->assign($this->interface);
        $this->interface->showSubPage('note_list.tpl');
    }
    
    /**
     * Save changes to a note.
     *
     * @access  public
     */
    public function save()
    {
        // Extract values from the POST fields:
        $id = $_POST['id'] == 'NEW' ? false : intval($_POST['id']);
        $text = trim($_POST['note']);
        
        // Validate input:
        if (empty($text)) {
            $this->jsonDie('Note cannot be blank.');
        }
        
        // Attempt to save changes:
        $note = new Note($id);
        $note->set('Note', $text);
        if (!$note->save()) {
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
        $list = new NoteList();
        $suggestions = $list->getSuggestions($_GET['q'], $_GET['limit']);
        foreach($suggestions as $s) {
            $line = "{$s['Note_ID']}: {$s['Note']}\n";
            echo htmlspecialchars($line);
        }
    }
}
?>