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
require_once 'Gamebooks/Tables/Item.php';

/**
 * Edit AJAX Support
 *
 * This class provides all AJAX functionality for the edit module.
 *
 * @author      Demian Katz
 * @access      public
 */
class AJAX_Edit_Item extends AJAX_Edit_Base
{
    /**
     * Add a platform.
     *
     * @access  public
     */
    public function addPlatform()
    {
        $item = intval($_POST['item_id']);
        $plat = trim($_POST['platform_id']);
        
        $item = new Item($item);
        if ($item->addPlatform($plat)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem storing platform.');
        }
    }
    
    /**
     * Remove a platform.
     *
     * @access  public
     */
    public function deletePlatform()
    {
        $item = intval($_POST['item_id']);
        $plat = trim($_POST['platform_id']);
        
        $item = new Item($item);
        if ($item->deletePlatform($plat)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem removing platform.');
        }
    }

    /**
     * Add an alternate title.
     *
     * @access  public
     */
    public function addAltTitle()
    {
        $item = intval($_POST['item_id']);
        $title = trim($_POST['title']);
        $noteID = is_numeric($_POST['note_id']) ? intval($_POST['note_id']) : null;
        
        $item = new Item($item);
        if ($item->addAltTitle($title, $noteID)) {
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
        $item = intval($_POST['item_id']);
        $rowID = intval($_POST['row_id']);
        
        $item = new Item($item);
        if ($item->deleteAltTitle($rowID)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem removing alternate title.');
        }
    }
    
    /**
     * Add an ISBN.
     *
     * @access  public
     */
    public function addISBN()
    {
        $item = intval($_POST['item_id']);
        $isbn = trim($_POST['isbn']);
        $validator = new ISBN($isbn);
        if (!$validator->isValid()) {
            $this->jsonDie('Invalid ISBN -- cannot save.');
        }
        $noteID = is_numeric($_POST['note_id']) ? intval($_POST['note_id']) : null;
        
        $item = new Item($item);
        if ($item->addISBN($isbn, $noteID)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem storing ISBN.');
        }
    }
    
    /**
     * Remove an ISBN.
     *
     * @access  public
     */
    public function deleteISBN()
    {
        $item = intval($_POST['item_id']);
        $rowID = intval($_POST['row_id']);
        
        $item = new Item($item);
        if ($item->deleteISBN($rowID)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem removing ISBN.');
        }
    }
    
    /**
     * Add a product code.
     *
     * @access  public
     */
    public function addProductCode()
    {
        $item = intval($_POST['item_id']);
        $code = trim($_POST['code']);
        $noteID = is_numeric($_POST['note_id']) ? intval($_POST['note_id']) : null;
        
        $item = new Item($item);
        if ($item->addProductCode($code, $noteID)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem storing product code.');
        }
    }
    
    /**
     * Remove a product code.
     *
     * @access  public
     */
    public function deleteProductCode()
    {
        $item = intval($_POST['item_id']);
        $rowID = intval($_POST['row_id']);
        
        $item = new Item($item);
        if ($item->deleteProductCode($rowID)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem removing product code.');
        }
    }
    
    /**
     * Add a release date.
     *
     * @access  public
     */
    public function addDate()
    {
        $item = intval($_POST['item_id']);
        $year = intval($_POST['year']);
        $month = intval($_POST['month']);
        $day = intval($_POST['day']);
        $noteID = is_numeric($_POST['note_id']) ? intval($_POST['note_id']) : null;

        $item = new Item($item);
        if ($item->addDate($year, $month, $day, $noteID)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem storing date.');
        }
    }
    
    /**
     * Remove a release date.
     *
     * @access  public
     */
    public function deleteDate()
    {
        $item = intval($_POST['item_id']);
        $year = intval($_POST['year']);
        $month = intval($_POST['month']);
        $day = intval($_POST['day']);

        $item = new Item($item);
        if ($item->deleteDate($year, $month, $day)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem removing date.');
        }
    }
    
    /**
     * Add a credit.
     *
     * @access  public
     */
    public function addCredit()
    {
        $item = intval($_POST['item_id']);
        $person = intval($_POST['person_id']);
        $role = intval($_POST['role_id']);
        $noteID = is_numeric($_POST['note_id']) ? intval($_POST['note_id']) : null;

        $item = new Item($item);
        if ($item->addCredit($person, $role, $noteID, intval($_POST['pos']))) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem storing credit.');
        }
    }
    
    /**
     * Remove a credit.
     *
     * @access  public
     */
    public function deleteCredit()
    {
        $item = intval($_POST['item_id']);
        $person = intval($_POST['person_id']);
        $role = intval($_POST['role_id']);

        $item = new Item($item);
        if ($item->deleteCredit($person, $role)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem removing credit.');
        }
    }
    
    /**
     * Renumber a credit.
     *
     * @access  public
     */
    public function renumberCredit()
    {
        $item = intval($_POST['item_id']);
        $person = intval($_POST['person_id']);
        $role = intval($_POST['role_id']);
        $pos = intval($_POST['pos']);

        $item = new Item($item);
        if ($item->renumberCredit($person, $role, $pos)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem renumbering credit.');
        }
    }
    
    /**
     * Add an image.
     *
     * @access  public
     */
    public function addImage()
    {
        $item = intval($_POST['item_id']);
        $image = $_POST['image'];
        $thumb = $_POST['thumb'];
        $noteID = is_numeric($_POST['note_id']) ? intval($_POST['note_id']) : null;

        $item = new Item($item);
        if ($item->addImage($image, $thumb, $noteID, intval($_POST['pos']))) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem storing image.');
        }
    }
    
    /**
     * Remove an image.
     *
     * @access  public
     */
    public function deleteImage()
    {
        $item = intval($_POST['item_id']);
        $image = intval($_POST['image_id']);

        $item = new Item($item);
        if ($item->deleteImage($image)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem removing image.');
        }
    }
    
    /**
     * Renumber an image.
     *
     * @access  public
     */
    public function renumberImage()
    {
        $item = intval($_POST['item_id']);
        $image = intval($_POST['image_id']);
        $pos = intval($_POST['pos']);

        $item = new Item($item);
        if ($item->renumberImage($image, $pos)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem renumbering image.');
        }
    }

    /**
     * Add an attachment.
     *
     * @access  public
     */
    public function addAttachment()
    {
        $item = intval($_POST['item_id']);
        $attach = intval($_POST['attach_id']);
        $pos = intval($_POST['pos']);

        $item = new Item($item);
        if ($item->addAttachment($attach, $pos)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem storing attached item.');
        }
    }
    
    /**
     * Remove an attachment.
     *
     * @access  public
     */
    public function deleteAttachment()
    {
        $item = intval($_POST['item_id']);
        $attach = intval($_POST['attach_id']);

        $item = new Item($item);
        if ($item->deleteAttachment($attach)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem removing attached item.');
        }
    }
    
    /**
     * Renumber an attached item.
     *
     * @access  public
     */
    public function renumberAttachment()
    {
        $item = intval($_POST['item_id']);
        $attach = intval($_POST['attach_id']);
        $pos = intval($_POST['pos']);

        $item = new Item($item);
        if ($item->renumberAttachment($attach, $pos)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem renumbering attached item.');
        }
    }
    
    /**
     * Add a description.
     *
     * @access  public
     */
    public function addDescription()
    {
        $item = intval($_POST['item_id']);

        $item = new Item($item);
        if ($item->addDescription($_POST['source'], $_POST['desc'])) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem storing description.');
        }
    }
    
    /**
     * Remove a description.
     *
     * @access  public
     */
    public function deleteDescription()
    {
        $item = intval($_POST['item_id']);

        $item = new Item($item);
        if ($item->deleteDescription($_POST['source'])) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem removing description.');
        }
    }
    
    /**
     * Display "edit item" form.
     *
     * @access  public
     */
    public function edit()
    {
        require_once 'Gamebooks/Tables/MaterialType.php';

        if (is_numeric($_GET['id'])) {
            $item = new Item($_GET['id']);
            $row = $item->getRow();
            if (!$row) {
                die('Cannot load item');
            }
            $this->interface->assign('item', $row);
        } else {
            // We'll default material type to the first entry in the table --
            // this is convenient for gamebooks.org, though perhaps it should
            // be made more flexible for other sites.
            $this->interface->assign('item', 
                array('Item_ID' => 'NEW', 'Material_Type_ID' => 1));
        }

        // We'll need material information:
        $matList = new MaterialTypeList();
        $matList->assign($this->interface);

        $this->interface->showSubPage('item_edit.tpl');
    }
    
    /**
     * Display attached item list.
     *
     * @access  public
     */
    public function getAttachments()
    {
        $items = new ItemList();
        $this->interface->assign('item_list',
            $items->getFromCollection($_GET['id']));
        $this->interface->showSubPage('item_item_list.tpl');
    }
    
    /**
     * Display item list.
     *
     * @access  public
     */
    public function getList()
    {
        $items = new ItemList();
        $items->assign($this->interface);
        $this->interface->showSubPage('item_list.tpl');
    }
    
    /**
     * Get a list of alternate titles.
     *
     * @access  public
     */
    public function getAltTitles()
    {
        if (is_numeric($_GET['id'])) {
            $item = new Item($_GET['id']);
            $this->interface->assign('item_alt_titles', $item->getAltTitles());
        }
        $this->interface->showSubPage('item_alt_titles.tpl');
    }
    
    /**
     * Get a list of ISBNs.
     *
     * @access  public
     */
    public function getISBNs()
    {
        if (is_numeric($_GET['id'])) {
            $item = new Item($_GET['id']);
            $this->interface->assign('ISBNs', $item->getISBNs());
        }
        $this->interface->showSubPage('item_isbns.tpl');
    }
    
    /**
     * Get a list of product codes.
     *
     * @access  public
     */
    public function getProductCodes()
    {
        if (is_numeric($_GET['id'])) {
            $item = new Item($_GET['id']);
            $this->interface->assign('productCodes', $item->getProductCodes());
        }
        $this->interface->showSubPage('item_codes.tpl');
    }
    
    /**
     * Get a list of platforms.
     *
     * @access  public
     */
    public function getPlatforms()
    {
        if (is_numeric($_GET['id'])) {
            require_once 'Gamebooks/Tables/Platform.php';
            
            $plats = new PlatformList();
            $this->interface->assign('item_platforms',
                $plats->getListForItem($_GET['id']));
        }
        $this->interface->showSubPage('item_platforms.tpl');
    }
    
    /**
     * Display date list.
     *
     * @access  public
     */
    public function getDates()
    {
        $item = intval($_GET['id']);
        $item = new Item($item);
        $this->interface->assign('releaseDates', $item->getDates());
        $this->interface->showSubPage('item_release_dates.tpl');
    }

    /**
     * Display credits.
     *
     * @access  public
     */
    public function getCredits()
    {
        $item = intval($_GET['id']);
        $item = new Item($item);
        $this->interface->assign('credits', $item->getCredits());
        $this->interface->showSubPage('item_credits.tpl');
    }

    /**
     * Display images.
     *
     * @access  public
     */
    public function getImages()
    {
        $item = intval($_GET['id']);
        $item = new Item($item);
        $this->interface->assign('images', $item->getImages());
        $this->interface->showSubPage('item_images.tpl');
    }

    /**
     * Display description list.
     *
     * @access  public
     */
    public function getDescriptions()
    {
        $item = intval($_GET['id']);
        $item = new Item($item);
        $this->interface->assign('descriptions', $item->getDescriptions());
        $this->interface->showSubPage('item_descriptions.tpl');
    }

    /**
     * Display adaptations list.
     *
     * @access  public
     */
    public function getAdaptations()
    {
        $list = new ItemList();
        $this->interface->assign('adaptedInto', $list->getAdaptations($_GET['id']));
        $this->interface->showSubPage('item_adapt_into.tpl');
    }
    
    /**
     * Display "adapted from" list.
     *
     * @access  public
     */
    public function getAdaptedFrom()
    {
        $list = new ItemList();
        $this->interface->assign('adaptedFrom', $list->getAdaptedFrom($_GET['id']));
        $this->interface->showSubPage('item_adapt_from.tpl');
    }
    
    /**
     * Display translation list.
     *
     * @access  public
     */
    public function getTranslations()
    {
        $list = new ItemList();
        $this->interface->assign('translatedInto', $list->getTranslations($_GET['id']));
        $this->interface->showSubPage('item_trans_into.tpl');
    }
    
    /**
     * Display item bibliography.
     *
     * @access  public
     */
    public function getItemReferences()
    {
        $list = new ItemList();
        $this->interface->assign('itemBib', $list->getReferencedBy($_GET['id']));
        $this->interface->showSubPage('item_bib.tpl');
    }
    
    /**
     * Display series bibliography.
     *
     * @access  public
     */
    public function getSeriesReferences()
    {
        require_once 'Gamebooks/Tables/Series.php';
        $list = new SeriesList();
        $this->interface->assign('seriesBib', $list->getReferencedBy($_GET['id']));
        $this->interface->showSubPage('series_bib.tpl');
    }
    
    /**
     * Display person bibliography.
     *
     * @access  public
     */
    public function getPersonReferences()
    {
        require_once 'Gamebooks/Tables/Person.php';
        $list = new PersonList();
        $this->interface->assign('peopleBib', $list->getReferencedBy($_GET['id']));
        $this->interface->showSubPage('people_bib.tpl');
    }
    
    /**
     * Display "translated from" list.
     *
     * @access  public
     */
    public function getTranslatedFrom()
    {
        $list = new ItemList();
        $this->interface->assign('translatedFrom', $list->getTranslatedFrom($_GET['id']));
        $this->interface->showSubPage('item_trans_from.tpl');
    }
    
    /**
     * Get list for autosuggest field
     *
     * @access  public
     */
    public function suggest()
    {
        $list = new ItemList();
        $suggestions = $list->getSuggestions($_GET['q'], $_GET['limit']);
        foreach($suggestions as $s) {
            $line = "{$s['Item_ID']}: {$s['Item_Name']}\n";
            echo htmlspecialchars($line);
        }
    }
    
    /**
     * Save changes to an item.
     *
     * @access  public
     */
    public function save()
    {
        // Extract values from the POST fields:
        $id = $_POST['id'] == 'NEW' ? false : intval($_POST['id']);
        $name = trim($_POST['name']);
        $length = trim($_POST['len']);
        $endings = trim($_POST['endings']);
        $errata = trim($_POST['errata']);
        $thanks = trim($_POST['thanks']);
        $material = trim($_POST['material']);
        
        // Validate input:
        if (empty($name)) {
            $this->jsonDie('Item name cannot be blank.');
        }
        
        // Attempt to save changes:
        $item = new Item($id);
        $item->set('Item_Name', $name);
        $item->set('Item_Length', $length);
        $item->set('Item_Endings', $endings);
        $item->set('Item_Errata', $errata);
        $item->set('Item_Thanks', $thanks);
        $item->set('Material_Type_ID', $material);
        if (!$item->save()) {
            $this->jsonDie('Problem saving changes.');
        }
        
        // Attach the item to a series if requested.
        if (isset($_POST['series_id'])) {
            require_once 'Gamebooks/Tables/Series.php';
            $series = new Series(intval($_POST['series_id']));
            $row = $item->getRow();
            if (!$row) {
                $this->jsonDie('Unable to read newly created item.');
            }
            if (!$series->addItem($row['Item_ID'])) {
                $this->jsonDie('Unable to attach new item to series.');
            }
        }
        
        // If we made it this far, we can report success:
        $this->jsonReportSuccess();
    }
    
    /**
     * Add an adaptation to an item.
     *
     * @access  public
     */
    public function addAdaptation()
    {
        $source_id = intval($_POST['source_id']);
        $adapt_id = intval($_POST['adapt_id']);
        
        if ($source_id == $adapt_id) {
            $this->jsonDie('You cannot link an item to itself.');
        }
        
        $item = new Item($source_id);
        if ($item->addAdaptation($adapt_id)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem storing relationship.');
        }
    }
    
    /**
     * Remove an adaptation from an item.
     *
     * @access  public
     */
    public function deleteAdaptation()
    {
        $source_id = intval($_POST['source_id']);
        $adapt_id = intval($_POST['adapt_id']);
        
        $item = new Item($source_id);
        if ($item->deleteAdaptation($adapt_id)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem removing relationship.');
        }
    }

    /**
     * Add a translation to an item.
     *
     * @access  public
     */
    public function addTranslation()
    {
        $source_id = intval($_POST['source_id']);
        $trans_id = intval($_POST['trans_id']);
        
        if ($source_id == $trans_id) {
            $this->jsonDie('You cannot link an item to itself.');
        }
        
        $item = new Item($source_id);
        if ($item->addTranslation($trans_id)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem storing relationship.');
        }
    }
    
    /**
     * Remove a translation from an item.
     *
     * @access  public
     */
    public function deleteTranslation()
    {
        $source_id = intval($_POST['source_id']);
        $trans_id = intval($_POST['trans_id']);
        
        $item = new Item($source_id);
        if ($item->deleteTranslation($trans_id)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem removing relationship.');
        }
    }

    /**
     * Add an item reference to an item.
     *
     * @access  public
     */
    public function addItemReference()
    {
        $item_id = intval($_POST['item_id']);
        $bib_item_id = intval($_POST['bib_item_id']);
        
        if ($item_id == $bib_item_id) {
            $this->jsonDie('You cannot link an item to itself.');
        }
        
        $item = new Item($item_id);
        if ($item->addItemReference($bib_item_id)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem storing relationship.');
        }
    }
    
    /**
     * Remove an item reference from an item.
     *
     * @access  public
     */
    public function deleteItemReference()
    {
        $item_id = intval($_POST['item_id']);
        $bib_item_id = intval($_POST['bib_item_id']);
        
        $item = new Item($item_id);
        if ($item->deleteItemReference($bib_item_id)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem removing relationship.');
        }
    }

    /**
     * Add a series reference to an item.
     *
     * @access  public
     */
    public function addSeriesReference()
    {
        $item_id = intval($_POST['item_id']);
        $series_id = intval($_POST['series_id']);
        
        $item = new Item($item_id);
        if ($item->addSeriesReference($series_id)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem storing relationship.');
        }
    }
    
    /**
     * Remove a series reference from an item.
     *
     * @access  public
     */
    public function deleteSeriesReference()
    {
        $item_id = intval($_POST['item_id']);
        $series_id = intval($_POST['series_id']);
        
        $item = new Item($item_id);
        if ($item->deleteSeriesReference($series_id)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem removing relationship.');
        }
    }

    /**
     * Add a person reference to an item.
     *
     * @access  public
     */
    public function addPersonReference()
    {
        $item_id = intval($_POST['item_id']);
        $person_id = intval($_POST['person_id']);
        
        $item = new Item($item_id);
        if ($item->addPersonReference($person_id)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem storing relationship.');
        }
    }
    
    /**
     * Remove a person reference from an item.
     *
     * @access  public
     */
    public function deletePersonReference()
    {
        $item_id = intval($_POST['item_id']);
        $person_id = intval($_POST['person_id']);
        
        $item = new Item($item_id);
        if ($item->deletePersonReference($person_id)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem removing relationship.');
        }
    }
}
?>