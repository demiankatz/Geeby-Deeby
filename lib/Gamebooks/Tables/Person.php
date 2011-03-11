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
 * Person Class
 *
 * This class represents a person from the database.
 *
 * @author      Demian Katz
 * @access      public
 */
class Person extends Row
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
        $this->table = 'People';
        $this->idKey = 'Person_ID';
        $this->writableKeys = array('First_Name', 'Middle_Name', 'Last_Name', 
            'Biography');
        
        parent::__construct($input);
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
     * Add a real name for this person.
     *
     * @access  public
     * @param   int     $id             The ID of the real person.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function addRealName($id)
    {
        $real_id = intval($id);
        $pseudo_id = intval($this->id);
        $sql = "INSERT INTO Pseudonyms(Pseudo_Person_ID, Real_Person_ID) " .
            "VALUES ($pseudo_id, $real_id);";
        return $this->db->query($sql);
    }
    
    /**
     * Delete a pseudonym for this person.
     *
     * @access  public
     * @param   int     $id             The ID of the pseudonym person.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function deletePseudonym($id)
    {
        $pseudo_id = intval($id);
        $real_id = intval($this->id);
        $sql = "DELETE FROM Pseudonyms WHERE Pseudo_Person_ID={$pseudo_id} AND " .
            " Real_Person_ID={$real_id};";
        return $this->db->query($sql);
    }
    
    /**
     * Delete a real name for this person.
     *
     * @access  public
     * @param   int     $id             The ID of the real person.
     * @return  boolean                 Boolean true on success, false on error.
     */
    public function deleteRealName($id)
    {
        $real_id = intval($id);
        $pseudo_id = intval($this->id);
        $sql = "DELETE FROM Pseudonyms WHERE Pseudo_Person_ID={$pseudo_id} AND " .
            " Real_Person_ID={$real_id};";
        return $this->db->query($sql);
    }
    
    /**
     * Get all pseudonyms for this person.
     *
     * @access  public
     * @return  array                   Selected contents of People table.
     */
    public function getPseudonyms()
    {
        $list = new PersonList();
        return $list->getPseudonyms($this->id);
    }
    
    /**
     * Get all real names for this person.
     *
     * @access  public
     * @return  array                   Selected contents of People table.
     */
    public function getRealNames()
    {
        $list = new PersonList();
        return $list->getRealNames($this->id);
    }
}

/**
 * Person List Class
 *
 * This class represents a set of people from the database.
 *
 * @author      Demian Katz
 * @access      public
 */
class PersonList
{
    private $db;
    private $list = false;
    
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
     * Get associative array representing person list.
     *
     * @access  public
     * @return  array                   Contents of People table.
     */
    public function getList()
    {
        // Load the list if we haven't already:
        if ($this->list === false) {
            $sql = "SELECT * FROM People ORDER BY Last_Name, First_Name, Middle_Name;";
            $peopleRes = $this->db->query($sql);
            $this->list = array();
            while ($tmp = $this->db->fetchAssoc($peopleRes)) {
                $this->list[] = $tmp;
            }
        }
        return $this->list;
    }
    
    /**
     * Get all pseudonyms for the specified person.
     *
     * @access  public
     * @param   int     $id             A valid Person_ID value.
     * @return  array                   Selected contents of People table.
     */
    public function getPseudonyms($id)
    {
        // Sanitize input:
        $id = intval($id);
        $sql = "SELECT People.* FROM Pseudonyms " .
            "JOIN People ON Pseudonyms.Pseudo_Person_ID=People.Person_ID " .
            "WHERE Pseudonyms.Real_Person_ID='{$id}' " .
            "ORDER BY People.Last_Name, People.First_Name, People.Middle_Name;";
        $result = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($result)) {
            $list[] = $tmp;
        }
        return $list;
    }
    
    /**
     * Get all real names associated with the specified pseudonym.
     *
     * @access  public
     * @param   int     $id             A valid Person_ID value.
     * @return  array                   Selected contents of People table.
     */
    public function getRealNames($id)
    {
        // Sanitize input:
        $id = intval($id);
        $sql = "SELECT People.* FROM Pseudonyms " .
            "JOIN People ON Pseudonyms.Real_Person_ID=People.Person_ID " .
            "WHERE Pseudonyms.Pseudo_Person_ID='{$id}' " .
            "ORDER BY People.Last_Name, People.First_Name, People.Middle_Name;";
        $result = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($result)) {
            $list[] = $tmp;
        }
        return $list;
    }
    
    /**
     * Get all people referenced by the specified item.
     *
     * @access  public
     * @param   int     $id             A valid Item_ID value.
     * @return  array                   Selected contents of People table.
     */
    public function getReferencedBy($id)
    {
        // Sanitize input:
        $id = intval($id);
        $sql = "SELECT People.* FROM People_Bibliography " .
            "JOIN People ON People_Bibliography.Person_ID=People.Person_ID " .
            "WHERE People_Bibliography.Item_ID='{$id}' " .
            "ORDER BY People.Last_Name, People.First_Name, People.Middle_Name;";
        $result = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($result)) {
            $list[] = $tmp;
        }
        return $list;
    }
    
    /**
     * Get all people related to the specified link.
     *
     * @access  public
     * @param   int     $id             A valid Link_ID value.
     * @return  array                   Selected contents of People table.
     */
    public function getByLink($id)
    {
        // Sanitize input:
        $id = intval($id);
        $sql = "SELECT People.* FROM People_Links " .
            "JOIN People ON People_Links.Person_ID=People.Person_ID " .
            "WHERE People_Links.Link_ID='{$id}' " .
            "ORDER BY People.Last_Name, People.First_Name, People.Middle_Name;";
        $result = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($result)) {
            $list[] = $tmp;
        }
        return $list;
    }
    
    /**
     * Get all people related to the specified file.
     *
     * @access  public
     * @param   int     $id             A valid File_ID value.
     * @return  array                   Selected contents of People table.
     */
    public function getByFile($id)
    {
        // Sanitize input:
        $id = intval($id);
        $sql = "SELECT People.* FROM People_Files " .
            "JOIN People ON People_Files.Person_ID=People.Person_ID " .
            "WHERE People_Files.File_ID='{$id}' " .
            "ORDER BY People.Last_Name, People.First_Name, People.Middle_Name;";
        $result = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($result)) {
            $list[] = $tmp;
        }
        return $list;
    }
    
    /**
     * Return rows associated with a query typed into an autosuggester.
     *
     * @access  public
     * @param   string  $query          The user query.
     * @param   mixed   $limit          Limit on returned rows (false for no limit).
     * @return  array                   Selected contents of People table.
     */
    public function getSuggestions($query, $limit = false)
    {
        // Start array of clauses to match:
        $clauses = array();
        
        // Add first keyword:
        $parts = preg_split("/[\s,]+/", $query);
        $first = $this->db->escape($parts[0]);
        $currentClause = "(First_Name LIKE '{$first}%' OR Last_Name LIKE '{$first}%'";
        if (is_numeric($first)) {
            $currentClause .= " OR Person_ID='" . intval($first) . "'";
        }
        $currentClause .= ")";
        $clauses[] = $currentClause;
        
        // Add last keyword if multiple terms found:
        $c = count($parts);
        if ($c > 1) {
            $last = $this->db->escape($parts[$c - 1]);
            $clauses[] = "(First_Name LIKE '{$last}%' OR Last_Name LIKE '{$last}%')";
        }
        
        // Build and execute query:
        $sql = "SELECT * FROM People WHERE " . implode(' AND ', $clauses) .
            " ORDER BY Last_Name, First_Name, Middle_Name";
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
    
    /**
     * Assign the person list to the user interface.
     *
     * @access  public
     * @param   UI      $interface      User Interface object.
     */
    public function assign($interface)
    {
        $interface->assign('people', $this->getList());
    }
}
?>