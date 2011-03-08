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
require_once 'Gamebooks/Tables/Row.php';

/**
 * Series Class
 *
 * This class represents a series from the database.
 *
 * @author      Demian Katz
 * @access      public
 */
class Series extends Row
{
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
        $this->table = 'Series';
        $this->idKey = 'Series_ID';
        $this->writableKeys = array('Series_Name', 'Series_Description', 'Language_ID');
        
        parent::__construct($input);
    }
    
    /**
     * Add a material type to the series.
     *
     * @access  public
     * @param   int     $materialID     ID of the material type to add.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function addMaterial($materialID)
    {
        $materialID = intval($materialID);
        $seriesID = intval($this->id);
        $sql = "INSERT INTO Series_Material_Types(Series_ID, Material_Type_ID) " .
            "VALUES ({$seriesID}, {$materialID});";
        return $this->db->query($sql);
    }
    
    /**
     * Add a pseudonym for this person.
     *
     * @access  public
     * @param   int     $id             The ID of the pseudonym person.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function addPseudonym($id)
    {
        $pseudo_id = intval($id);
        $real_id = intval($this->id);
        $sql = "INSERT INTO Pseudonyms(Pseudo_Person_ID, Real_Person_ID) " .
            "VALUES ($pseudo_id, $real_id);";
        return $this->db->query($sql);
    }
    
    /**
     * Add a translation for this series.
     *
     * @access  public
     * @param   int     $id             The ID of the translated series.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function addTranslation($id)
    {
        $trans_id = intval($id);
        $my_id = intval($this->id);
        $sql = "INSERT INTO Series_Translations(Trans_Series_ID, Source_Series_ID) " .
            "VALUES ($trans_id, $my_id);";
        return $this->db->query($sql);
    }
    
    /**
     * Remove a material type from the series.
     *
     * @access  public
     * @param   int     $materialID     ID of the material type to delete.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function deleteMaterial($materialID)
    {
        $materialID = intval($materialID);
        $seriesID = intval($this->id);
        $sql = "DELETE FROM Series_Material_Types " .
            "WHERE Series_ID={$seriesID} AND Material_Type_ID={$materialID};";
        return $this->db->query($sql);
    }
    
    /**
     * Delete a translation for this series.
     *
     * @access  public
     * @param   int     $id             The ID of the translated series.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function deleteTranslation($id)
    {
        $trans_id = intval($id);
        $my_id = intval($this->id);
        $sql = "DELETE FROM Series_Translations WHERE Trans_Series_ID={$trans_id} AND " .
            " Source_Series_ID={$my_id};";
        return $this->db->query($sql);
    }

    /**
     * Add an item to the series.
     *
     * @access  public
     * @param   int     $itemID         ID of the item to add (from Items table).
     * @param   int     $pos            Position of the item in the series.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function addItem($itemID, $pos = 0)
    {
        $my_id = intval($this->id);
        $itemID = intval($itemID);
        $pos = intval($pos);
        $sql = "INSERT INTO Items_In_Series(Item_ID, Series_ID, Position) " .
            "VALUES({$itemID}, {$my_id}, {$pos});";
        return $this->db->query($sql);
    }

    /**
     * Remove an item from the series.
     *
     * @access  public
     * @param   int     $itemID         ID of the item to remove (from Items table).
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function deleteItem($itemID)
    {
        $my_id = intval($this->id);
        $itemID = intval($itemID);
        $sql = "DELETE FROM Items_In_Series WHERE " .
            "Item_ID={$itemID} AND Series_ID={$my_id};";
        return $this->db->query($sql);
    }

    /**
     * Change the position of an item within the series.
     *
     * @access  public
     * @param   int     $itemID         ID of the item to move (from Items table).
     * @param   int     $pos            New position of the item in the series.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function renumberItem($itemID, $pos)
    {
        $my_id = intval($this->id);
        $itemID = intval($itemID);
        $pos = intval($pos);
        $sql = "UPDATE Items_In_Series SET Position={$pos} WHERE " .
            "Item_ID={$itemID} AND Series_ID={$my_id};";
        return $this->db->query($sql);
    }

    /**
     * Add a publisher to the series.
     *
     * @access  public
     * @param   int     $publisherID    ID of the publisher to add.
     * @param   int     $countryID      ID of the relevant country of publication.
     * @param   string  $imprint        Publisher's imprint (optional).
     * @param   int     $noteID         ID of a note about the publisher (optional).
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function addPublisher($publisherID, $countryID, $imprint = '', $noteID = null)
    {
        $seriesID = intval($this->id);
        $publisherID = intval($publisherID);
        $countryID = intval($countryID);
        $noteID = is_numeric($noteID) ? intval($noteID) : 'null';
        $imprint = $this->db->escape($imprint);
        $sql = "INSERT INTO Series_Publishers(Series_ID, Publisher_ID, Country_ID, Note_ID, Imprint) " .
            "VALUES ({$seriesID}, {$publisherID}, {$countryID}, {$noteID}, '{$imprint}');";
        return $this->db->query($sql);
    }
    
    /**
     * Remove a publisher from the series.
     *
     * @access  public
     * @param   int     $rowID          ID of row linking publisher to series
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function deletePublisher($rowID)
    {
        $rowID = intval($rowID);
        $seriesID = intval($this->id);
        // Note -- including Series_ID in query is just an extra safety mechanism.
        $sql = "DELETE FROM Series_Publishers " .
            "WHERE Series_ID={$seriesID} AND Series_Publisher_ID={$rowID};";
        return $this->db->query($sql);
    }
    
    /**
     * Get category information for this series.
     *
     * @access  public
     * @return  array                   Integer IDs for all categories associated
     *                                  with the series.
     */
    public function getCategories()
    {
        $id = intval($this->id);
        $sql = "SELECT Category_ID FROM Series_Categories WHERE Series_ID=$id";
        $res = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($res)) {
            $list[] = $tmp['Category_ID'];
        }
        return $list;
    }
    
    /**
     * Clear all existing category information and replace it with the provided list.
     *
     * @access  public
     * @param   array   $cats           Array of categories to save.
     * @return  array                   Boolean true on success, false on fail.
     */
    public function setCategories($cats)
    {
        $id = intval($this->id);
        
        // Clear out existing categories:
        $sql = "DELETE FROM Series_Categories WHERE Series_ID=$id";
        if (!$this->db->query($sql)) {
            return false;
        }
        
        // Save new categories:
        if (is_array($cats)) {
            foreach($cats as $catID) {
                $catID = intval($catID);
                $sql = "INSERT INTO Series_Categories(Series_ID, Category_ID) " .
                    "VALUES({$id}, {$catID});";
                if (!$this->db->query($sql)) {
                    return false;
                }
            }
        }
        
        // If we made it this far, we succeeded:
        return true;
    }
    
    /**
     * Get publisher information for this series.
     *
     * @access  public
     * @return  array                   Associative array of publisher data.
     */
    public function getPublishers()
    {
        $id = intval($this->id);
        $sql = "SELECT * FROM Series_Publishers " .
            "LEFT JOIN Publishers ON Series_Publishers.Publisher_ID = Publishers.Publisher_ID " .
            "LEFT JOIN Countries ON Series_Publishers.Country_ID = Countries.Country_ID " .
            "LEFT JOIN Notes ON Series_Publishers.Note_ID = Notes.Note_ID " .
            "WHERE Series_Publishers.Series_ID={$id} " .
            "ORDER BY Publishers.Publisher_Name, Series_Publishers.Imprint, Countries.Country_Name, Notes.Note;";
        $res = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($res)) {
            $list[] = $tmp;
        }
        return $list;
    }
    
    /**
     * Get alternate title information for this series.
     *
     * @access  public
     * @return  array                   Associative array of title data.
     */
    public function getAltTitles()
    {
        $id = intval($this->id);
        $sql = "SELECT * FROM Series_AltTitles " .
            "LEFT JOIN Notes ON Series_AltTitles.Note_ID = Notes.Note_ID " .
            "WHERE Series_AltTitles.Series_ID={$id} " .
            "ORDER BY Series_AltTitles.Series_AltName;";
        $res = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($res)) {
            $list[] = $tmp;
        }
        return $list;
    }
    
    /**
     * Remove an alternate title from the series.
     *
     * @access  public
     * @param   int     $rowID          ID of row containing alternate title.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function deleteAltTitle($rowID)
    {
        $rowID = intval($rowID);
        $seriesID = intval($this->id);
        // Note -- including Series_ID in query is just an extra safety mechanism.
        $sql = "DELETE FROM Series_AltTitles " .
            "WHERE Series_ID={$seriesID} AND Sequence_ID={$rowID};";
        return $this->db->query($sql);
    }
    
    /**
     * Add an alternate title to the series.
     *
     * @access  public
     * @param   string  $title          Alternate title to add.
     * @param   int     $noteID         ID of a note about the title (optional).
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function addAltTitle($title, $noteID = null)
    {
        $seriesID = intval($this->id);
        $title = $this->db->escape($title);
        $noteID = is_numeric($noteID) ? intval($noteID) : 'null';
        $sql = "INSERT INTO Series_AltTitles(Series_ID, Series_AltName, Note_ID) " .
            "VALUES ({$seriesID}, '{$title}', {$noteID});";
        return $this->db->query($sql);
    }

    /**
     * Approve a pending comment.
     *
     * @access  public
     * @param   int     $userID         ID of the user submitting the comment.
     * @param   string  $text           Text of comment to update.
     * @return  boolean                 True on success, false on error.
     */
    public function approveComment($userID, $text)
    {
        $userID = intval($userID);
        $seriesID = intval($this->id);
        $text = $this->db->escape($text);
        
        // Approve the review!
        $sql = "UPDATE Series_Reviews SET Review='{$text}', Approved='y' " .
            "WHERE User_ID={$userID} AND Series_ID={$seriesID} AND Approved='n'";
        $result = $this->db->query($sql);
        if (!$result) {
            return false;
        }
        
        // Update the recent reviews list and ignore the result (if we try to
        // add something that was previously approved, we should simply ignore
        // the duplicate key error).
        $date = $this->db->escape(date("Y-m-d"));
        $sql = "INSERT INTO Recent_Reviews(Added, User_ID, Item_ID, Type)" .
            " VALUES('{$date}','{$userID}','{$seriesID}','series')";
        $this->db->query($sql);
        return $result;
    }

    /**
     * Reject an unwanted comment.
     *
     * @access  public
     * @param   int     $userID         ID of the user submitting the review.
     * @return  boolean                 True on success, false on error.
     */
    public function rejectComment($userID)
    {
        $userID = intval($userID);
        $seriesID = intval($this->id);
        // Only allow rejection of unapproved reviews!
        $sql = "DELETE FROM Series_Reviews " .
            "WHERE User_ID={$userID} AND Series_ID={$seriesID} AND Approved='n'";
        return $this->db->query($sql);
    }
}

/**
 * Series List Class
 *
 * This class represents a set of series from the database.
 *
 * @author      Demian Katz
 * @access      public
 */
class SeriesList
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
     * Get associative array representing series list.
     *
     * @access  public
     * @return  array                   Contents of Series table.
     */
    public function getList()
    {
        // Populate the list if it is currently empty:
        if (empty($this->list)) {
            $sql = "SELECT * FROM Series ORDER BY Series_Name;";
            $seriesRes = $this->db->query($sql);
            while ($tmp = $this->db->fetchAssoc($seriesRes)) {
                $this->list[] = $tmp;
            }
        }
        return $this->list;
    }
    
    /**
     * Get all series referenced by the specified item.
     *
     * @access  public
     * @param   int     $id             A valid Item_ID value.
     * @return  array                   Selected contents of Series table.
     */
    public function getReferencedBy($id)
    {
        // Sanitize input:
        $id = intval($id);
        $sql = "SELECT Series.* FROM Series_Bibliography " .
            "JOIN Series ON Series_Bibliography.Series_ID=Series.Series_ID " .
            "WHERE Series_Bibliography.Item_ID='{$id}' " .
            "ORDER BY Series_Name;";
        $result = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($result)) {
            $list[] = $tmp;
        }
        return $list;
    }
    
    /**
     * Get pending series comments.
     *
     * @access  public
     * @return  array
     */
    public function getPendingReviews()
    {
        $sql = "SELECT Series_Reviews.*, Series.*, Users.* " .
            "FROM Series_Reviews JOIN Series ON Series_Reviews.Series_ID = Series.Series_ID " .
            "JOIN Users ON Series_Reviews.User_ID = Users.User_ID " .
            "WHERE Series_Reviews.Approved = 'n'";
        $result = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($result)) {
            $list[] = $tmp;
        }
        return $list;
    }
    
    /**
     * Get all series referenced by the specified link.
     *
     * @access  public
     * @param   int     $id             A valid Link_ID value.
     * @return  array                   Selected contents of Series table.
     */
    public function getByLink($id)
    {
        // Sanitize input:
        $id = intval($id);
        $sql = "SELECT Series.* FROM Series_Links " .
            "JOIN Series ON Series_Links.Series_ID=Series.Series_ID " .
            "WHERE Series_Links.Link_ID='{$id}' " .
            "ORDER BY Series_Name;";
        $result = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($result)) {
            $list[] = $tmp;
        }
        return $list;
    }
    
    /**
     * Get all series translated into the specified series.
     *
     * @access  public
     * @param   int     $id             A valid Series_ID value.
     * @return  array                   Selected contents of Series table.
     */
    public function getTranslatedFrom($id)
    {
        // Sanitize input:
        $id = intval($id);
        $sql = "SELECT Series.* FROM Series_Translations " .
            "JOIN Series ON Series_Translations.Source_Series_ID=Series.Series_ID " .
            "WHERE Series_Translations.Trans_Series_ID='{$id}' " .
            "ORDER BY Series_Name;";
        $result = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($result)) {
            $list[] = $tmp;
        }
        return $list;
    }
    
    /**
     * Get all translations of the specified series.
     *
     * @access  public
     * @param   int     $id             A valid Series_ID value.
     * @return  array                   Selected contents of Series table.
     */
    public function getTranslations($id)
    {
        // Sanitize input:
        $id = intval($id);
        $sql = "SELECT Series.* FROM Series_Translations " .
            "JOIN Series ON Series_Translations.Trans_Series_ID=Series.Series_ID " .
            "WHERE Series_Translations.Source_Series_ID='{$id}' " .
            "ORDER BY Series_Name;";
        $result = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($result)) {
            $list[] = $tmp;
        }
        return $list;
    }
    
    /**
     * Assign the series list to the user interface.
     *
     * @access  public
     * @param   UI      $interface      User Interface object.
     */
    public function assign($interface)
    {
        $interface->assign('series', $this->getList());
    }
    
    /**
     * Return rows associated with a query typed into an autosuggester.
     *
     * @access  public
     * @param   string  $query          The user query.
     * @param   mixed   $limit          Limit on returned rows (false for no limit).
     * @return  array                   Selected contents of Series table.
     */
    public function getSuggestions($query, $limit = false)
    {
        $query = $this->db->escape($query);
        $sql = "SELECT * FROM Series " .
            "WHERE Series_Name LIKE '{$query}%' ORDER BY Series_Name";
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