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
 * Link Class
 *
 * This class represents a link from the database.
 *
 * @author      Demian Katz
 * @access      public
 */
class Link extends Row
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
        $this->table = 'Links';
        $this->idKey = 'Link_ID';
        $this->writableKeys = array('Link_Name', 'URL', 'Description',
            'Date_Checked', 'Link_Type_ID');
        
        parent::__construct($input);
    }
    
    /**
     * Add an item reference for this link.
     *
     * @access  public
     * @param   int     $id             The ID of the referenced item.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function linkItem($id)
    {
        $item_id = intval($id);
        $my_id = intval($this->id);
        $sql = "INSERT INTO Items_Links(Link_ID, Item_ID) " .
            "VALUES ($my_id, $item_id);";
        return $this->db->query($sql);
    }
    
    /**
     * Delete an item reference for this link.
     *
     * @access  public
     * @param   int     $id             The ID of the referenced item.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function unlinkItem($id)
    {
        $item_id = intval($id);
        $my_id = intval($this->id);
        $sql = "DELETE FROM Items_Links WHERE Link_ID={$my_id} AND " .
            " Item_ID={$item_id};";
        return $this->db->query($sql);
    }
    
    /**
     * Add a series reference for this link.
     *
     * @access  public
     * @param   int     $id             The ID of the referenced series.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function linkSeries($id)
    {
        $series_id = intval($id);
        $my_id = intval($this->id);
        $sql = "INSERT INTO Series_Links(Link_ID, Series_ID) " .
            "VALUES ($my_id, $series_id);";
        return $this->db->query($sql);
    }
    
    /**
     * Delete a series reference for this link.
     *
     * @access  public
     * @param   int     $id             The ID of the referenced series.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function unlinkSeries($id)
    {
        $series_id = intval($id);
        $my_id = intval($this->id);
        $sql = "DELETE FROM Series_Links WHERE Link_ID={$my_id} AND " .
            " Series_ID={$series_id};";
        return $this->db->query($sql);
    }
    
    /**
     * Add a person reference for this link.
     *
     * @access  public
     * @param   int     $id             The ID of the referenced person.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function linkPerson($id)
    {
        $person_id = intval($id);
        $my_id = intval($this->id);
        $sql = "INSERT INTO People_Links(Link_ID, Person_ID) " .
            "VALUES ($my_id, $person_id);";
        return $this->db->query($sql);
    }
    
    /**
     * Delete a person reference for this link.
     *
     * @access  public
     * @param   int     $id             The ID of the referenced person.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function unlinkPerson($id)
    {
        $person_id = intval($id);
        $my_id = intval($this->id);
        $sql = "DELETE FROM People_Links WHERE Link_ID={$my_id} AND " .
            " Person_ID={$person_id};";
        return $this->db->query($sql);
    }
}

/**
 * Link List Class
 *
 * This class represents a set of links from the database.
 *
 * @author      Demian Katz
 * @access      public
 */
class LinkList
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
     * Get associative array representing link list.
     *
     * @access  public
     * @return  array                   Contents of Links table.
     */
    public function getList()
    {
        // Populate the list if it is currently empty:
        if (empty($this->list)) {
            $sql = "SELECT * FROM Links ORDER BY Link_Name;";
            $res = $this->db->query($sql);
            while ($tmp = $this->db->fetchAssoc($res)) {
                $this->list[] = $tmp;
            }
        }
        return $this->list;
    }

    /**
     * Assign the link list to the user interface.
     *
     * @access  public
     * @param   UI      $interface      User Interface object.
     */
    public function assign($interface)
    {
        $interface->assign('links', $this->getList());
    }
}
?>