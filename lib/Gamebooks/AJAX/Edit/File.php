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
require_once 'Gamebooks/Tables/File.php';
require_once 'Gamebooks/Tables/FileType.php';

/**
 * Edit AJAX Support
 *
 * This class provides all AJAX functionality for the edit module.
 *
 * @author      Demian Katz
 * @access      public
 */
class AJAX_Edit_File extends AJAX_Edit_Base
{
    /**
     * Display "edit file" form.
     *
     * @access  public
     */
    public function edit()
    {
        if (is_numeric($_GET['id'])) {
            $file = new File($_GET['id']);
            $row = $file->getRow();
            if (!$row) {
                die('Cannot load file');
            }
            $this->interface->assign('file', $row);
        } else {
            $this->interface->assign('file', array('File_ID' => 'NEW'));
        }
        $types = new FileTypeList();
        $types->assign($this->interface);
        $this->interface->showSubPage('file_edit.tpl');
    }
    
    /**
     * Display file list.
     *
     * @access  public
     */
    public function getList()
    {
        $ll = new FileList();
        $ll->assign($this->interface);
        $this->interface->showSubPage('file_list.tpl');
    }

    /**
     * Get a list of related people.
     *
     * @access  public
     */
    public function getPersonList()
    {
        require_once 'Gamebooks/Tables/Person.php';
        $people = new PersonList();
        $this->interface->assign('peopleFiles', $people->getByFile($_GET['id']));
        $this->interface->showSubPage('file_person_list.tpl');
    }

    /**
     * Get a list of related items.
     *
     * @access  public
     */
    public function getItemList()
    {
        require_once 'Gamebooks/Tables/Item.php';
        $items = new ItemList();
        $this->interface->assign('itemFiles', $items->getByFile($_GET['id']));
        $this->interface->showSubPage('file_item_list.tpl');
    }

    /**
     * Get a list of related series.
     *
     * @access  public
     */
    public function getSeriesList()
    {
        require_once 'Gamebooks/Tables/Series.php';
        $series = new SeriesList();
        $this->interface->assign('seriesFiles', $series->getByFile($_GET['id']));
        $this->interface->showSubPage('file_series_list.tpl');
    }

    /**
     * Associate an item.
     *
     * @access  public
     */
    public function linkItem()
    {
        $file_id = intval($_POST['file_id']);
        $item_id = intval($_POST['item_id']);
        $file = new File($file_id);
        if ($file->linkItem($item_id)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem storing relationship.');
        }
    }

    /**
     * Remove an item association.
     *
     * @access  public
     */
    public function unlinkItem()
    {
        $file_id = intval($_POST['file_id']);
        $item_id = intval($_POST['item_id']);
        $file = new File($file_id);
        if ($file->unlinkItem($item_id)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem removing relationship.');
        }
    }
    
    /**
     * Associate a series.
     *
     * @access  public
     */
    public function linkSeries()
    {
        $file_id = intval($_POST['file_id']);
        $series_id = intval($_POST['series_id']);
        $file = new File($file_id);
        if ($file->linkSeries($series_id)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem storing relationship.');
        }
    }

    /**
     * Remove a series association.
     *
     * @access  public
     */
    public function unlinkSeries()
    {
        $file_id = intval($_POST['file_id']);
        $series_id = intval($_POST['series_id']);
        $file = new File($file_id);
        if ($file->unlinkSeries($series_id)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem removing relationship.');
        }
    }
    
    /**
     * Associate a person.
     *
     * @access  public
     */
    public function linkPerson()
    {
        $file_id = intval($_POST['file_id']);
        $person_id = intval($_POST['person_id']);
        $file = new File($file_id);
        if ($file->linkPerson($person_id)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem storing relationship.');
        }
    }

    /**
     * Remove a person association.
     *
     * @access  public
     */
    public function unlinkPerson()
    {
        $file_id = intval($_POST['file_id']);
        $person_id = intval($_POST['person_id']);
        $file = new File($file_id);
        if ($file->unlinkPerson($person_id)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem removing relationship.');
        }
    }
    
    /**
     * Save changes to a file.
     *
     * @access  public
     */
    public function save()
    {
        // Extract values from the POST fields:
        $id = $_POST['id'] == 'NEW' ? false : intval($_POST['id']);
        $name = trim($_POST['file_name']);
        $path = trim($_POST['path']);
        $desc = trim($_POST['desc']);
        $type_id = intval($_POST['type_id']);
        
        // Validate input:
        if (empty($name)) {
            $this->jsonDie('File name cannot be blank.');
        }
        if (empty($path)) {
            $this->jsonDie('File path cannot be blank.');
        }
        
        // Attempt to save changes:
        $file = new File($id);
        $file->set('File_Name', $name);
        $file->set('File_Path', $path);
        $file->set('Description', $desc);
        $file->set('File_Type_ID', $type_id);
        if (!$file->save()) {
            $this->jsonDie('Problem saving changes.');
        }
        
        // If we made it this far, we can report success:
        $this->jsonReportSuccess();
    }
}
?>