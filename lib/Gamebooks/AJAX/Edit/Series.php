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
require_once 'Gamebooks/Tables/Series.php';

/**
 * Edit AJAX Support
 *
 * This class provides all AJAX functionality for the edit module.
 *
 * @author      Demian Katz
 * @access      public
 */
class AJAX_Edit_Series extends AJAX_Edit_Base
{
    /**
     * Add a material type.
     *
     * @access  public
     */
    public function addMaterial()
    {
        $series = intval($_POST['series_id']);
        $material = intval($_POST['material_id']);
        
        $series = new Series($series);
        if ($series->addMaterial($material)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem storing material type.');
        }
    }
    
    /**
     * Add a publisher.
     *
     * @access  public
     */
    public function addPublisher()
    {
        $series = intval($_POST['series_id']);
        $publisherID = intval($_POST['publisher_id']);
        $countryID = intval($_POST['country_id']);
        $imprint = trim($_POST['imprint']);
        $noteID = is_numeric($_POST['note_id']) ? intval($_POST['note_id']) : null;
        
        $series = new Series($series);
        if ($series->addPublisher($publisherID, $countryID, $imprint, $noteID)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem storing publisher.');
        }
    }
    
    /**
     * Save category settings.
     *
     * @access  public
     */
    public function saveCategories()
    {
        $series = intval($_POST['series_id']);
        
        $series = new Series($series);
        if ($series->setCategories($_POST['categories'])) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem storing categories.');
        }
    }
    
    /**
     * Add an alternate title.
     *
     * @access  public
     */
    public function addAltTitle()
    {
        $series = intval($_POST['series_id']);
        $title = trim($_POST['title']);
        $noteID = is_numeric($_POST['note_id']) ? intval($_POST['note_id']) : null;
        
        $series = new Series($series);
        if ($series->addAltTitle($title, $noteID)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem storing alternate title.');
        }
    }
    
    /**
     * Remove an alternate title.
     *
     * @access  public
     */
    public function deleteAltTitle()
    {
        $series = intval($_POST['series_id']);
        $rowID = intval($_POST['row_id']);
        
        $series = new Series($series);
        if ($series->deleteAltTitle($rowID)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem removing alternate title.');
        }
    }
    
    /**
     * Remove a publisher.
     *
     * @access  public
     */
    public function deletePublisher()
    {
        $series = intval($_POST['series_id']);
        $rowID = intval($_POST['row_id']);
        
        $series = new Series($series);
        if ($series->deletePublisher($rowID)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem removing publisher.');
        }
    }
    
    /**
     * Delete a material type.
     *
     * @access  public
     */
    public function deleteMaterial()
    {
        $series = intval($_POST['series_id']);
        $material = intval($_POST['material_id']);
        
        $series = new Series($series);
        if ($series->deleteMaterial($material)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem removing material type.');
        }
    }
    
    /**
     * Display "edit series" form.
     *
     * @access  public
     */
    public function edit()
    {
        require_once 'Gamebooks/Tables/Language.php';
        
        if (is_numeric($_GET['id'])) {
            $series = new Series($_GET['id']);
            $row = $series->getRow();
            if (!$row) {
                die('Cannot load series');
            }
            $this->interface->assign('series', $row);
        } else {
            $this->interface->assign('series', array('Series_ID' => 'NEW'));
        }
        
        // We'll need the language list in addition to the series data:
        $lang = new LanguageList();
        $lang->assign($this->interface);
        
        $this->interface->showSubPage('series_edit.tpl');
    }
    
    /**
     * Display series list.
     *
     * @access  public
     */
    public function getList()
    {
        $series = new SeriesList();
        $series->assign($this->interface);
        $this->interface->showSubPage('series_list.tpl');
    }
    
    /**
     * Get a list of material types.
     *
     * @access  public
     */
    public function getMaterials()
    {
        require_once 'Gamebooks/Tables/MaterialType.php';
        $mt = new MaterialTypeList();
        $this->interface->assign('series_materials', $mt->getListForSeries($_GET['id']));
        $this->interface->showSubPage('series_material_list.tpl');
    }
    
    /**
     * Get a list of items in the series.
     *
     * @access  public
     */
    public function getItems()
    {
        require_once 'Gamebooks/Tables/Item.php';
        $items = new ItemList();
        $this->interface->assign('item_list', $items->getFromSeries($_GET['id']));
        $this->interface->showSubPage('series_item_list.tpl');
    }
    
    /**
     * Get a list of publishers.
     *
     * @access  public
     */
    public function getPublishers()
    {
        if (is_numeric($_GET['id'])) {
            $series = new Series($_GET['id']);
            $this->interface->assign('series_publishers', $series->getPublishers());
        }
        $this->interface->showSubPage('series_publisher_list.tpl');
    }
    
    /**
     * Get a list of alternate titles.
     *
     * @access  public
     */
    public function getAltTitles()
    {
        if (is_numeric($_GET['id'])) {
            $series = new Series($_GET['id']);
            $this->interface->assign('series_alt_titles', $series->getAltTitles());
        }
        $this->interface->showSubPage('series_alt_titles.tpl');
    }
    
    /**
     * Display translation list.
     *
     * @access  public
     */
    public function getTranslations()
    {
        $list = new SeriesList();
        $this->interface->assign('translatedInto', $list->getTranslations($_GET['id']));
        $this->interface->showSubPage('series_trans_into.tpl');
    }
    
    /**
     * Display "translated from" list.
     *
     * @access  public
     */
    public function getTranslatedFrom()
    {
        $list = new SeriesList();
        $this->interface->assign('translatedFrom', $list->getTranslatedFrom($_GET['id']));
        $this->interface->showSubPage('series_trans_from.tpl');
    }
    
    /**
     * Get list for autosuggest field
     *
     * @access  public
     */
    public function suggest()
    {
        $list = new SeriesList();
        $suggestions = $list->getSuggestions($_GET['q'], $_GET['limit']);
        foreach($suggestions as $s) {
            $line = "{$s['Series_ID']}: {$s['Series_Name']}\n";
            echo htmlspecialchars($line);
        }
    }
    
    /**
     * Save changes to a series.
     *
     * @access  public
     */
    public function save()
    {
        // Extract values from the POST fields:
        $id = $_POST['id'] == 'NEW' ? false : intval($_POST['id']);
        $name = trim($_POST['name']);
        $desc = trim($_POST['desc']);
        $lang = trim($_POST['lang']);
        
        // Validate input:
        if (empty($name)) {
            $this->jsonDie('Series name cannot be blank.');
        }
        
        // Attempt to save changes:
        $series = new Series($id);
        $series->set('Series_Name', $name);
        $series->set('Series_Description', $desc);
        $series->set('Language_ID', $lang);
        if (!$series->save()) {
            $this->jsonDie('Problem saving changes.');
        }
        
        // If we made it this far, we can report success:
        $this->jsonReportSuccess();
    }
    
    /**
     * Add a translation to a series.
     *
     * @access  public
     */
    public function addTranslation()
    {
        $source_id = intval($_POST['source_id']);
        $trans_id = intval($_POST['trans_id']);
        
        if ($source_id == $trans_id) {
            $this->jsonDie('You cannot link a series to itself.');
        }
        
        $series = new Series($source_id);
        if ($series->addTranslation($trans_id)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem storing relationship.');
        }
    }
    
    /**
     * Remove a translation from a series.
     *
     * @access  public
     */
    public function deleteTranslation()
    {
        $source_id = intval($_POST['source_id']);
        $trans_id = intval($_POST['trans_id']);
        
        $series = new Series($source_id);
        if ($series->deleteTranslation($trans_id)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem removing relationship.');
        }
    }

    /**
     * Add an item to a series.
     *
     * @access  public
     */
    public function addItem()
    {
        $series_id = intval($_POST['series_id']);
        $item_id = intval($_POST['item_id']);
        $pos = isset($_POST['pos']) ? intval($_POST['pos']) : 0;
        
        $series = new Series($series_id);
        if ($series->addItem($item_id, $pos)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem storing relationship.');
        }
    }

    /**
     * Renumber an item in a series.
     *
     * @access  public
     */
    public function renumberItem()
    {
        $series_id = intval($_POST['series_id']);
        $item_id = intval($_POST['item_id']);
        $pos = isset($_POST['pos']) ? intval($_POST['pos']) : 0;
        
        $series = new Series($series_id);
        if ($series->renumberItem($item_id, $pos)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem renumbering item.');
        }
    }

    /**
     * Remove an item from a series.
     *
     * @access  public
     */
    public function deleteItem()
    {
        $series_id = intval($_POST['series_id']);
        $item_id = intval($_POST['item_id']);
        
        $series = new Series($series_id);
        if ($series->deleteItem($item_id)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem storing relationship.');
        }
    }
}
?>