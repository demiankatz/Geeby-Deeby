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

/**
 * File Class
 *
 * This class represents a file from the database.
 *
 * @author      Demian Katz
 * @access      public
 */
class File extends Row
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
        $this->table = 'Files';
        $this->idKey = 'File_ID';
        $this->writableKeys = array('File_Name', 'File_Path', 'Description',
            'File_Type_ID');
        
        parent::__construct($input);
    }
    
    /**
     * Add an item reference for this file.
     *
     * @access  public
     * @param   int     $id             The ID of the referenced item.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function linkItem($id)
    {
        $item_id = intval($id);
        $my_id = intval($this->id);
        $sql = "INSERT INTO Items_Files(File_ID, Item_ID) " .
            "VALUES ($my_id, $item_id);";
        return $this->db->query($sql);
    }
    
    /**
     * Delete an item reference for this file.
     *
     * @access  public
     * @param   int     $id             The ID of the referenced item.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function unlinkItem($id)
    {
        $item_id = intval($id);
        $my_id = intval($this->id);
        $sql = "DELETE FROM Items_Files WHERE File_ID={$my_id} AND " .
            " Item_ID={$item_id};";
        return $this->db->query($sql);
    }
    
    /**
     * Add a series reference for this file.
     *
     * @access  public
     * @param   int     $id             The ID of the referenced series.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function linkSeries($id)
    {
        $series_id = intval($id);
        $my_id = intval($this->id);
        $sql = "INSERT INTO Series_Files(File_ID, Series_ID) " .
            "VALUES ($my_id, $series_id);";
        return $this->db->query($sql);
    }
    
    /**
     * Delete a series reference for this file.
     *
     * @access  public
     * @param   int     $id             The ID of the referenced series.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function unlinkSeries($id)
    {
        $series_id = intval($id);
        $my_id = intval($this->id);
        $sql = "DELETE FROM Series_Files WHERE File_ID={$my_id} AND " .
            " Series_ID={$series_id};";
        return $this->db->query($sql);
    }
    
    /**
     * Add a person reference for this file.
     *
     * @access  public
     * @param   int     $id             The ID of the referenced person.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function linkPerson($id)
    {
        $person_id = intval($id);
        $my_id = intval($this->id);
        $sql = "INSERT INTO People_Files(File_ID, Person_ID) " .
            "VALUES ($my_id, $person_id);";
        return $this->db->query($sql);
    }
    
    /**
     * Delete a person reference for this file.
     *
     * @access  public
     * @param   int     $id             The ID of the referenced person.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function unlinkPerson($id)
    {
        $person_id = intval($id);
        $my_id = intval($this->id);
        $sql = "DELETE FROM People_Files WHERE File_ID={$my_id} AND " .
            " Person_ID={$person_id};";
        return $this->db->query($sql);
    }
}

/**
 * File List Class
 *
 * This class represents a set of files from the database.
 *
 * @author      Demian Katz
 * @access      public
 */
class FileList
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
     * Get associative array representing file list.
     *
     * @access  public
     * @return  array                   Contents of Files table.
     */
    public function getList()
    {
        // Populate the list if it is currently empty:
        if (empty($this->list)) {
            $sql = "SELECT * FROM Files ORDER BY File_Name;";
            $res = $this->db->query($sql);
            while ($tmp = $this->db->fetchAssoc($res)) {
                $this->list[] = $tmp;
            }
        }
        return $this->list;
    }

    /**
     * Assign the file list to the user interface.
     *
     * @access  public
     * @param   UI      $interface      User Interface object.
     */
    public function assign($interface)
    {
        $interface->assign('files', $this->getList());
    }
}
?>