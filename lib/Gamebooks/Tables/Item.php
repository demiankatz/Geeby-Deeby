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
require_once 'Gamebooks/Tables/Row.php';
require_once 'Gamebooks/ISBN.php';

/**
 * Item Class
 *
 * This class represents an item from the database.
 *
 * @author      Demian Katz
 * @access      public
 */
class Item extends Row
{
    /* Description types -- keys must correspond with enumeration in Source field
     * of Items_Descriptions table.
     */
    private $descriptionTypes = array(
        'User' => 'User Summary',
        'LC' => 'LC Cataloging in Publication Summary',
        'Cover' => 'Back Cover Text');

    /**
     * Constructor
     *
     * @access  public
     * @param   mixed   $input          Send in a full associative array fetched
     *                                  from the database to create a pre-populated
     *                                  object; send in a numeric ID value to fetch
     *                                  a known item from the database; send in
     *                                  boolean false to create a new row.
     */
    public function __construct($input = false)
    {
        $this->table = 'Items';
        $this->idKey = 'Item_ID';
        $this->writableKeys = array('Item_Name', 'Item_Length', 'Item_Endings',
            'Item_Errata', 'Item_Thanks', 'Material_Type_ID');
        
        parent::__construct($input);
    }
    
    /**
     * Add a translation for this item.
     *
     * @access  public
     * @param   int     $id             The ID of the translated item.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function addTranslation($id)
    {
        $trans_id = intval($id);
        $my_id = intval($this->id);
        $sql = "INSERT INTO Items_Translations(Trans_Item_ID, Source_Item_ID) " .
            "VALUES ($trans_id, $my_id);";
        return $this->db->query($sql);
    }
    
    /**
     * Delete a translation for this item.
     *
     * @access  public
     * @param   int     $id             The ID of the translated item.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function deleteTranslation($id)
    {
        $trans_id = intval($id);
        $my_id = intval($this->id);
        $sql = "DELETE FROM Items_Translations WHERE Trans_Item_ID={$trans_id} AND " .
            " Source_Item_ID={$my_id};";
        return $this->db->query($sql);
    }

    /**
     * Add an item reference for this item.
     *
     * @access  public
     * @param   int     $id             The ID of the referenced item.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function addItemReference($id)
    {
        $item_id = intval($id);
        $my_id = intval($this->id);
        $sql = "INSERT INTO Items_Bibliography(Bib_Item_ID, Item_ID) " .
            "VALUES ($my_id, $item_id);";
        return $this->db->query($sql);
    }
    
    /**
     * Delete an item reference for this item.
     *
     * @access  public
     * @param   int     $id             The ID of the referenced item.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function deleteItemReference($id)
    {
        $item_id = intval($id);
        $my_id = intval($this->id);
        $sql = "DELETE FROM Items_Bibliography WHERE Bib_Item_ID={$my_id} AND " .
            " Item_ID={$item_id};";
        return $this->db->query($sql);
    }

    /**
     * Add a series reference for this item.
     *
     * @access  public
     * @param   int     $id             The ID of the referenced series.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function addSeriesReference($id)
    {
        $series_id = intval($id);
        $my_id = intval($this->id);
        $sql = "INSERT INTO Series_Bibliography(Item_ID, Series_ID) " .
            "VALUES ($my_id, $series_id);";
        return $this->db->query($sql);
    }
    
    /**
     * Delete a series reference for this item.
     *
     * @access  public
     * @param   int     $id             The ID of the referenced series.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function deleteSeriesReference($id)
    {
        $series_id = intval($id);
        $my_id = intval($this->id);
        $sql = "DELETE FROM Series_Bibliography WHERE Item_ID={$my_id} AND " .
            " Series_ID={$series_id};";
        return $this->db->query($sql);
    }

    /**
     * Add a person reference for this item.
     *
     * @access  public
     * @param   int     $id             The ID of the referenced person.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function addPersonReference($id)
    {
        $person_id = intval($id);
        $my_id = intval($this->id);
        $sql = "INSERT INTO People_Bibliography(Item_ID, Person_ID) " .
            "VALUES ($my_id, $person_id);";
        return $this->db->query($sql);
    }
    
    /**
     * Delete a person reference for this item.
     *
     * @access  public
     * @param   int     $id             The ID of the referenced person.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function deletePersonReference($id)
    {
        $person_id = intval($id);
        $my_id = intval($this->id);
        $sql = "DELETE FROM People_Bibliography WHERE Item_ID={$my_id} AND " .
            " Person_ID={$person_id};";
        return $this->db->query($sql);
    }

    /**
     * Add an adaptation for this item.
     *
     * @access  public
     * @param   int     $id             The ID of the adapted item.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function addAdaptation($id)
    {
        $adapt_id = intval($id);
        $my_id = intval($this->id);
        $sql = "INSERT INTO Items_Adaptations(Adapted_Item_ID, Source_Item_ID) " .
            "VALUES ($adapt_id, $my_id);";
        return $this->db->query($sql);
    }
    
    /**
     * Delete an adaptation for this item.
     *
     * @access  public
     * @param   int     $id             The ID of the adapted item.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function deleteAdaptation($id)
    {
        $adapt_id = intval($id);
        $my_id = intval($this->id);
        $sql = "DELETE FROM Items_Adaptations WHERE Adapted_Item_ID={$adapt_id} AND " .
            " Source_Item_ID={$my_id};";
        return $this->db->query($sql);
    }

    /**
     * Get alternate title information for this item.
     *
     * @access  public
     * @return  array                   Associative array of title data.
     */
    public function getAltTitles()
    {
        $id = intval($this->id);
        $sql = "SELECT * FROM Items_AltTitles " .
            "LEFT JOIN Notes ON Items_AltTitles.Note_ID = Notes.Note_ID " .
            "WHERE Items_AltTitles.Item_ID={$id} " .
            "ORDER BY Items_AltTitles.Item_AltName;";
        $res = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($res)) {
            $list[] = $tmp;
        }
        return $list;
    }
    
    /**
     * Remove a platform from the item.
     *
     * @access  public
     * @param   int     $id             ID of platform
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function deletePlatform($id)
    {
        $id = intval($id);
        $myID = intval($this->id);
        $sql = "DELETE FROM Items_Platforms " .
            "WHERE Item_ID={$myID} AND Platform_ID={$id};";
        return $this->db->query($sql);
    }
    
    /**
     * Add a platform to the item.
     *
     * @access  public
     * @param   int     $id             ID of platform
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function addPlatform($id)
    {
        $id = intval($id);
        $myID = intval($this->id);
        $sql = "INSERT INTO Items_Platforms(Item_ID, Platform_ID) " .
            "VALUES ({$myID}, {$id});";
        return $this->db->query($sql);
    }
    
    /**
     * Remove an alternate title from the item.
     *
     * @access  public
     * @param   int     $rowID          ID of row containing alternate title.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function deleteAltTitle($rowID)
    {
        $rowID = intval($rowID);
        $myID = intval($this->id);
        // Note -- including Item_ID in query is just an extra safety mechanism.
        $sql = "DELETE FROM Items_AltTitles " .
            "WHERE Item_ID={$myID} AND Sequence_ID={$rowID};";
        return $this->db->query($sql);
    }
    
    /**
     * Add an alternate title to the item.
     *
     * @access  public
     * @param   string  $title          Alternate title to add.
     * @param   int     $noteID         ID of a note about the title (optional).
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function addAltTitle($title, $noteID = null)
    {
        $myID = intval($this->id);
        $title = $this->db->escape($title);
        $noteID = is_numeric($noteID) ? intval($noteID) : 'null';
        $sql = "INSERT INTO Items_AltTitles(Item_ID, Item_AltName, Note_ID) " .
            "VALUES ({$myID}, '{$title}', {$noteID});";
        return $this->db->query($sql);
    }

    /**
     * Get ISBN information for this item.
     *
     * @access  public
     * @return  array                   Associative array of ISBN data.
     */
    public function getISBNs()
    {
        $id = intval($this->id);
        $sql = "SELECT * FROM Items_ISBNs " .
            "LEFT JOIN Notes ON Items_ISBNs.Note_ID = Notes.Note_ID " .
            "WHERE Items_ISBNs.Item_ID={$id} " .
            "ORDER BY Items_ISBNs.ISBN13;";
        $res = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($res)) {
            $list[] = $tmp;
        }
        return $list;
    }
    
    /**
     * Remove an ISBN from the item.
     *
     * @access  public
     * @param   int     $rowID          ID of row containing ISBN.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function deleteISBN($rowID)
    {
        $rowID = intval($rowID);
        $myID = intval($this->id);
        // Note -- including Item_ID in query is just an extra safety mechanism.
        $sql = "DELETE FROM Items_ISBNs " .
            "WHERE Item_ID={$myID} AND Sequence_ID={$rowID};";
        return $this->db->query($sql);
    }
    
    /**
     * Add an ISBN to the item.
     *
     * @access  public
     * @param   string  $isbn           ISBN to add (10 or 13 digit).
     * @param   int     $noteID         ID of a note about the ISBN (optional).
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function addISBN($isbn, $noteID = null)
    {
        // Validate ISBN and get 10/13 formats -- fail if neither is valid:
        $isbn = new ISBN($isbn);
        $isbn10 = $isbn->get10();
        $isbn13 = $isbn->get13();
        if (empty($isbn10) && empty($isbn13)) {
            return false;
        }
        
        // Prepare for database query:
        $myID = intval($this->id);
        $isbn10 = empty($isbn10) ? 'null' : "'" . $this->db->escape($isbn10) . "'";
        $isbn13 = empty($isbn13) ? 'null' : "'" . $this->db->escape($isbn13) . "'";
        $noteID = is_numeric($noteID) ? intval($noteID) : 'null';
        
        // Store the data:
        $sql = "INSERT INTO Items_ISBNs(Item_ID, ISBN, ISBN13, Note_ID) " .
            "VALUES ({$myID}, {$isbn10}, {$isbn13}, {$noteID});";
        return $this->db->query($sql);
    }
    
    /**
     * Get product code information for this item.
     *
     * @access  public
     * @return  array                   Associative array of product code data.
     */
    public function getProductCodes()
    {
        $id = intval($this->id);
        $sql = "SELECT * FROM Items_Product_Codes " .
            "LEFT JOIN Notes ON Items_Product_Codes.Note_ID = Notes.Note_ID " .
            "WHERE Items_Product_Codes.Item_ID={$id} " .
            "ORDER BY Items_Product_Codes.Product_Code;";
        $res = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($res)) {
            $list[] = $tmp;
        }
        return $list;
    }
    
    /**
     * Remove a product code from the item.
     *
     * @access  public
     * @param   int     $rowID          ID of row containing product code.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function deleteProductCode($rowID)
    {
        $rowID = intval($rowID);
        $myID = intval($this->id);
        // Note -- including Item_ID in query is just an extra safety mechanism.
        $sql = "DELETE FROM Items_Product_Codes " .
            "WHERE Item_ID={$myID} AND Sequence_ID={$rowID};";
        return $this->db->query($sql);
    }
    
    /**
     * Add a product code to the item.
     *
     * @access  public
     * @param   string  $code           Product code to add.
     * @param   int     $noteID         ID of a note about the code (optional).
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function addProductCode($code, $noteID = null)
    {
        // Prepare for database query:
        $myID = intval($this->id);
        $code = $this->db->escape($code);
        $noteID = is_numeric($noteID) ? intval($noteID) : 'null';
        
        // Store the data:
        $sql = "INSERT INTO Items_Product_Codes(Item_ID, Product_Code, Note_ID) " .
            "VALUES ({$myID}, '{$code}', {$noteID});";
        return $this->db->query($sql);
    }
    
    /**
     * Get all the dates associated with this item.
     *
     * @access  public
     * @return  array                   Details from Items_Release_Dates table.
     */
    public function getDates()
    {
        $myID = intval($this->id);
        $sql = "SELECT Items_Release_Dates.*, Notes.Note FROM " .
            "Items_Release_Dates LEFT JOIN Notes " .
            "ON Items_Release_Dates.Note_ID=Notes.Note_ID " .
            "WHERE Item_ID={$myID} ORDER BY Year, Month, Day";
        $res = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($res)) {
            $list[] = $tmp;
        }
        return $list;
    }
    
    /**
     * Add a release date to the database.
     *
     * @access  public
     * @param   int     $year           Release year (-1 for unreleased)
     * @param   int     $month          Release month (1-12, 0 for unknown)
     * @param   int     $day            Release day (0 for unknown)
     * @param   int     $noteID         Note associated with release (optional)
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function addDate($year, $month = 0, $day = 0, $noteID = null)
    {
        $myID = intval($this->id);
        $year = intval($year);
        $month = intval($month);
        $day = intval($day);
        $noteID = is_null($noteID) ? 'null' : intval($noteID);
        $sql = 
            "INSERT INTO Items_Release_Dates(Item_ID, Year, Month, Day, Note_ID)" .
            " VALUES({$myID}, {$year}, {$month}, {$day}, {$noteID});";
        return $this->db->query($sql);
    }
    
    /**
     * Remove a release date from the database.
     *
     * @access  public
     * @param   int     $year           Release year (-1 for unreleased)
     * @param   int     $month          Release month (1-12, 0 for unknown)
     * @param   int     $day            Release day (0 for unknown)
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function deleteDate($year, $month = 0, $day = 0)
    {
        $myID = intval($this->id);
        $year = intval($year);
        $month = intval($month);
        $day = intval($day);
        $sql = "DELETE FROM Items_Release_Dates WHERE Item_ID={$myID} AND " .
            "Year={$year} AND Month={$month} AND Day={$day};";
        return $this->db->query($sql);
    }

    /**
     * Return an associative array (database value => user-oriented text) of
     * description types.
     *
     * @access  public
     * @return  array
     */
    public function getDescriptionTypes()
    {
        return $this->descriptionTypes;
    }
    
    /**
     * Get all descriptions associated with the current item.
     *
     * @access  public
     * @return  array                   Rows from Items_Descriptions table.
     */
    public function getDescriptions()
    {
        $myID = intval($this->id);
        $sql = "SELECT * FROM Items_Descriptions WHERE Item_ID={$myID} " .
            "ORDER BY Source;";
        $res = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($res)) {
            // Fill in user-oriented description of source:
            $tmp['Source_Description'] = $this->descriptionTypes[$tmp['Source']];
            $list[] = $tmp;
        }
        return $list;
    }

    /**
     * Add a description of this item to the database.
     *
     * @access  public
     * @param   string  $source         Source of description (User, LC or Cover)
     * @param   string  $desc           Text of description
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function addDescription($source, $desc)
    {
        // Validate the description source:
        $validSources = array_keys($this->descriptionTypes);
        if (!in_array($source, $validSources)) {
            return false;
        }
        
        // Properly escape all values:
        $myID = intval($this->id);
        $source = $this->db->escape($source);
        $desc = $this->db->escape($desc);
        
        // Write to the database:
        $sql = "INSERT INTO Items_Descriptions(Item_ID, Source, Description)" .
            " VALUES({$myID}, \"{$source}\", \"{$desc}\");";
        return $this->db->query($sql);
    }
    
    /**
     * Remove a description from the database.
     *
     * @access  public
     * @param   string  $source         Source of description (User, LC or Cover)
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function deleteDescription($source)
    {
        // Validate the description source:
        $validSources = array_keys($this->descriptionTypes);
        if (!in_array($source, $validSources)) {
            return false;
        }
        
        // Properly escape all values:
        $myID = intval($this->id);
        $source = $this->db->escape($source);
        
        // Remove the description:
        $sql = "DELETE FROM Items_Descriptions WHERE Item_ID={$myID} AND " .
            "Source=\"{$source}\";";
        return $this->db->query($sql);
    }
    
    /**
     * Get credits for the current item, merging details from the People and Roles
     * tables in the process.
     *
     * @access  public
     * @return  array                   Associative array of credit data.
     */
    public function getCredits()
    {
        $myID = intval($this->id);
        $sql = "SELECT People.*, Roles.*, Notes.*, Items_Credits.Position " .
            "FROM Items_Credits " .
            "JOIN People ON Items_Credits.Person_ID=People.Person_ID " .
            "JOIN Roles ON Items_Credits.Role_ID=Roles.Role_ID " .
            "LEFT JOIN Notes ON Items_Credits.Note_ID=Notes.Note_ID " .
            "WHERE Items_Credits.Item_ID={$myID} " .
            "ORDER BY Roles.Role_Name, Items_Credits.Position, " .
            "People.Last_Name, People.First_Name, People.Middle_Name";
        $res = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($res)) {
            $list[] = $tmp;
        }
        return $list;
    }

    /**
     * Add a credit to the database.
     *
     * @access  public
     * @param   int     $personID       ID from the People table
     * @param   int     $roleID         ID from the Roles table
     * @param   int     $noteID         Note associated with release (optional)
     * @param   int     $pos            Position in credit list
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function addCredit($personID, $roleID, $noteID = null, $pos = 0)
    {
        $myID = intval($this->id);
        $personID = intval($personID);
        $roleID = intval($roleID);
        $pos = intval($pos);
        $noteID = is_null($noteID) ? 'null' : intval($noteID);
        $sql = "INSERT INTO Items_Credits(Item_ID, Person_ID, Role_ID, " .
            "Note_ID, Position) " .
            "VALUES({$myID}, {$personID}, {$roleID}, {$noteID}, {$pos});";
        return $this->db->query($sql);
    }
    
    /**
     * Remove a credit from the database.
     *
     * @access  public
     * @param   int     $personID       ID from the People table
     * @param   int     $roleID         ID from the Roles table
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function deleteCredit($personID, $roleID)
    {
        $myID = intval($this->id);
        $personID = intval($personID);
        $roleID = intval($roleID);

        $sql = "DELETE FROM Items_Credits WHERE Item_ID={$myID} AND " .
            "Person_ID={$personID} AND Role_ID={$roleID};";
        return $this->db->query($sql);
    }

    /**
     * Change the position of a credit.
     *
     * @access  public
     * @param   int     $personID       ID from the People table
     * @param   int     $roleID         ID from the Roles table
     * @param   int     $pos            New position of the credit in the list.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function renumberCredit($personID, $roleID, $pos)
    {
        $myID = intval($this->id);
        $personID = intval($personID);
        $roleID = intval($roleID);
        $pos = intval($pos);
        $sql = "UPDATE Items_Credits SET Position={$pos} WHERE " .
            "Item_ID={$myID} AND Person_ID={$personID} AND Role_ID={$roleID};";
        return $this->db->query($sql);
    }
    
    /**
     * Get images for the current item.
     *
     * @access  public
     * @return  array                   Associative array of image data.
     */
    public function getImages()
    {
        $myID = intval($this->id);
        $sql = "SELECT Items_Images.*, Notes.Note FROM Items_Images " .
            "LEFT JOIN Notes ON Items_Images.Note_ID=Notes.Note_ID " .
            "WHERE Items_Images.Item_ID={$myID} ORDER BY Position";
        $res = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($res)) {
            $list[] = $tmp;
        }
        return $list;
    }

    /**
     * Add an image to the database.
     *
     * @access  public
     * @param   string  $image          Path to full-size image
     * @param   string  $thumb          Path to thumbnail (null to build path)
     * @param   int     $noteID         Note associated with release (optional)
     * @param   int     $pos            Position in image list
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function addImage($image, $thumb = null, $noteID = null, $pos = 0)
    {
        // Build thumb path if none was provided:
        if (empty($thumb)) {
            $parts = explode('.', $image);
            $nextToLast = count($parts) - 2;
            $parts[$nextToLast] .= 'thumb';
            $thumb = implode('.', $parts);
        }
        
        $myID = intval($this->id);
        $thumb = $this->db->escape($thumb);
        $image = $this->db->escape($image);
        $pos = intval($pos);
        $noteID = is_null($noteID) ? 'null' : intval($noteID);
        
        $sql = "INSERT INTO Items_Images(Item_ID, Image_Path, Thumb_Path, " .
            "Note_ID, Position) " .
            "VALUES({$myID}, \"{$image}\", \"{$thumb}\", {$noteID}, {$pos});";
        return $this->db->query($sql);
    }
    
    /**
     * Remove an image from the database.
     *
     * @access  public
     * @param   int     $imageID        Sequence_ID from the Items_Images table
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function deleteImage($imageID)
    {
        $myID = intval($this->id);
        $imageID = intval($imageID);

        $sql = "DELETE FROM Items_Images WHERE Item_ID={$myID} AND " .
            "Sequence_ID={$imageID};";
        return $this->db->query($sql);
    }

    /**
     * Change the position of an image.
     *
     * @access  public
     * @param   int     $imageID        Sequence_ID from the Items_Images table
     * @param   int     $pos            New position of the image in the list.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function renumberImage($imageID, $pos)
    {
        $myID = intval($this->id);
        $imageID = intval($imageID);
        $pos = intval($pos);
        $sql = "UPDATE Items_Images SET Position={$pos} WHERE " .
            "Item_ID={$myID} AND Sequence_ID={$imageID};";
        return $this->db->query($sql);
    }

    /**
     * Add an attached item.
     *
     * @access  public
     * @param   int     $itemID         ID of the item to add (from Items table).
     * @param   int     $pos            Position of the item in the collection.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function addAttachment($itemID, $pos = 0)
    {
        $my_id = intval($this->id);
        $itemID = intval($itemID);
        $pos = intval($pos);
        $sql = "INSERT INTO Items_In_Collections" .
            "(Item_ID, Collection_Item_ID, Position) " .
            "VALUES({$itemID}, {$my_id}, {$pos});";
        return $this->db->query($sql);
    }

    /**
     * Remove an attached item.
     *
     * @access  public
     * @param   int     $itemID         ID of the item to remove (from Items table).
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function deleteAttachment($itemID)
    {
        $my_id = intval($this->id);
        $itemID = intval($itemID);
        $sql = "DELETE FROM Items_In_Collections WHERE " .
            "Item_ID={$itemID} AND Collection_Item_ID={$my_id};";
        return $this->db->query($sql);
    }

    /**
     * Change the position of an attached item within the collection.
     *
     * @access  public
     * @param   int     $itemID         ID of the item to move (from Items table).
     * @param   int     $pos            New position of the item in the collection.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function renumberAttachment($itemID, $pos)
    {
        $my_id = intval($this->id);
        $itemID = intval($itemID);
        $pos = intval($pos);
        $sql = "UPDATE Items_In_Collections SET Position={$pos} WHERE " .
            "Item_ID={$itemID} AND Collection_Item_ID={$my_id};";
        return $this->db->query($sql);
    }
    
    /**
     * Reject an unwanted review.
     *
     * @access  public
     * @param   int     $userID         ID of the user submitting the review.
     * @return  boolean                 True on success, false on error.
     */
    public function rejectReview($userID)
    {
        $userID = intval($userID);
        // Only allow rejection of unapproved reviews!
        $sql = "DELETE FROM Items_Reviews WHERE User_ID=$userID AND approved='n'";
        return $this->db->query($sql);
    }
}

/**
 * Item List Class
 *
 * This class represents a set of items from the database.
 *
 * @author      Demian Katz
 * @access      public
 */
class ItemList
{
    private $db;
    private $list = array();
    
    /**
     * Constructor
     *
     * @access  public
     */
    public function __construct()
    {
        $this->db = new GBDB();
    }
    
    /**
     * Get associative array representing item list.
     *
     * @access  public
     * @return  array                   Contents of Items table.
     */
    public function getList()
    {
        // Populate the list if it is currently empty:
        if (empty($this->list)) {
            $sql = "SELECT * FROM Items ORDER BY Item_Name;";
            $res = $this->db->query($sql);
            while ($tmp = $this->db->fetchAssoc($res)) {
                $this->list[] = $tmp;
            }
        }
        return $this->list;
    }

    /**
     * Get all items matching the specified Series_ID.
     *
     * @access  public
     * @param   int     $id             A valid Series_ID value.
     * @return  array                   Selected contents of Items table with
     *                                  Position key from Items_In_Series and
     *                                  details from Material_Types added.
     */
    public function getFromSeries($id)
    {
        // Sanitize input:
        $id = intval($id);
        $sql = "SELECT Items.*, Material_Types.*, Items_In_Series.Position " .
            "FROM Items_In_Series " .
            "JOIN Items ON Items_In_Series.Item_ID=Items.Item_ID " .
            "JOIN Material_Types ON " .
            "Material_Types.Material_Type_ID=Items.Material_Type_ID " .
            "WHERE Items_In_Series.Series_ID='{$id}' " .
            "ORDER BY Items.Material_Type_ID, Items_In_Series.Position, " .
            "Items.Item_Name;";
        $result = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($result)) {
            $list[] = $tmp;
        }
        return $list;
    }

    /**
     * Get all items attached to the specified Item_ID.
     *
     * @access  public
     * @param   int     $id             A valid Item_ID value.
     * @return  array                   Selected contents of Items table with
     *                                  Position key from Items_In_Collections and
     *                                  details from Material_Types added.
     */
    public function getFromCollection($id)
    {
        // Sanitize input:
        $id = intval($id);
        $sql = "SELECT Items.*, Material_Types.*, Items_In_Collections.Position " .
            "FROM Items_In_Collections " .
            "JOIN Items ON Items_In_Collections.Item_ID=Items.Item_ID " .
            "JOIN Material_Types ON " .
            "Material_Types.Material_Type_ID=Items.Material_Type_ID " .
            "WHERE Items_In_Collections.Collection_Item_ID='{$id}' " .
            "ORDER BY Items.Material_Type_ID, Items_In_Collections.Position, " .
            "Items.Item_Name;";
        $result = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($result)) {
            $list[] = $tmp;
        }
        return $list;
    }

    /**
     * Get all items adapted into the specified item.
     *
     * @access  public
     * @param   int     $id             A valid Item_ID value.
     * @return  array                   Selected contents of Items table.
     */
    public function getAdaptedFrom($id)
    {
        // Sanitize input:
        $id = intval($id);
        $sql = "SELECT Items.* FROM Items_Adaptations " .
            "JOIN Items ON Items_Adaptations.Source_Item_ID=Items.Item_ID " .
            "WHERE Items_Adaptations.Adapted_Item_ID='{$id}' " .
            "ORDER BY Item_Name;";
        $result = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($result)) {
            $list[] = $tmp;
        }
        return $list;
    }
    
    /**
     * Get all adaptations of the specified item.
     *
     * @access  public
     * @param   int     $id             A valid Item_ID value.
     * @return  array                   Selected contents of Items table.
     */
    public function getAdaptations($id)
    {
        // Sanitize input:
        $id = intval($id);
        $sql = "SELECT Items.* FROM Items_Adaptations " .
            "JOIN Items ON Items_Adaptations.Adapted_Item_ID=Items.Item_ID " .
            "WHERE Items_Adaptations.Source_Item_ID='{$id}' " .
            "ORDER BY Item_Name;";
        $result = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($result)) {
            $list[] = $tmp;
        }
        return $list;
    }
    
    /**
     * Get all items translated into the specified item.
     *
     * @access  public
     * @param   int     $id             A valid Item_ID value.
     * @return  array                   Selected contents of Items table.
     */
    public function getTranslatedFrom($id)
    {
        // Sanitize input:
        $id = intval($id);
        $sql = "SELECT Items.* FROM Items_Translations " .
            "JOIN Items ON Items_Translations.Source_Item_ID=Items.Item_ID " .
            "WHERE Items_Translations.Trans_Item_ID='{$id}' " .
            "ORDER BY Item_Name;";
        $result = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($result)) {
            $list[] = $tmp;
        }
        return $list;
    }
    
    /**
     * Get all items referenced by the specified item.
     *
     * @access  public
     * @param   int     $id             A valid Item_ID value.
     * @return  array                   Selected contents of Items table.
     */
    public function getReferencedBy($id)
    {
        // Sanitize input:
        $id = intval($id);
        $sql = "SELECT Items.* FROM Items_Bibliography " .
            "JOIN Items ON Items_Bibliography.Item_ID=Items.Item_ID " .
            "WHERE Items_Bibliography.Bib_Item_ID='{$id}' " .
            "ORDER BY Item_Name;";
        $result = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($result)) {
            $list[] = $tmp;
        }
        return $list;
    }
    
    /**
     * Get all translations of the specified item.
     *
     * @access  public
     * @param   int     $id             A valid Item_ID value.
     * @return  array                   Selected contents of Items table.
     */
    public function getTranslations($id)
    {
        // Sanitize input:
        $id = intval($id);
        $sql = "SELECT Items.* FROM Items_Translations " .
            "JOIN Items ON Items_Translations.Trans_Item_ID=Items.Item_ID " .
            "WHERE Items_Translations.Source_Item_ID='{$id}' " .
            "ORDER BY Item_Name;";
        $result = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($result)) {
            $list[] = $tmp;
        }
        return $list;
    }

    /**
     * Get pending item reviews.
     *
     * @access  public
     * @return  array
     */
    public function getPendingReviews()
    {
        $sql = "SELECT Items_Reviews.*, Items.*, Users.* " .
            "FROM Items_Reviews JOIN Items ON Items_Reviews.Item_ID = Items.Item_ID " .
            "JOIN Users ON Items_Reviews.User_ID = Users.User_ID " .
            "WHERE Items_Reviews.Approved = 'n'";
        $result = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($result)) {
            $list[] = $tmp;
        }
        return $list;
    }
    
    /**
     * Get all items related to the specified link.
     *
     * @access  public
     * @param   int     $id             A valid Link_ID value.
     * @return  array                   Selected contents of Items table.
     */
    public function getByLink($id)
    {
        // Sanitize input:
        $id = intval($id);
        $sql = "SELECT Items.* FROM Items_Links " .
            "JOIN Items ON Items_Links.Item_ID=Items.Item_ID " .
            "WHERE Items_Links.Link_ID='{$id}' " .
            "ORDER BY Item_Name;";
        $result = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($result)) {
            $list[] = $tmp;
        }
        return $list;
    }
    
    /**
     * Assign the item list to the user interface.
     *
     * @access  public
     * @param   UI      $interface      User Interface object.
     */
    public function assign($interface)
    {
        $interface->assign('items', $this->getList());
    }
    
    /**
     * Return rows associated with a query typed into an autosuggester.
     *
     * @access  public
     * @param   string  $query          The user query.
     * @param   mixed   $limit          Limit on returned rows (false for no limit).
     * @return  array                   Selected contents of Items table.
     */
    public function getSuggestions($query, $limit = false)
    {
        $query = $this->db->escape($query);
        $sql = "SELECT * FROM Items " .
            "WHERE Item_Name LIKE '{$query}%' ORDER BY Item_Name";
        if ($limit !== false && $limit > 0) {
            $sql .= " LIMIT {$limit}";
        }
        $result = $this->db->query($sql . ';');
        
        // Send back results:
        $list = array();
        while ($tmp = $this->db->fetchAssoc($result)) {
            $list[] = $tmp;
        }
        return $list;
    }
}
?>