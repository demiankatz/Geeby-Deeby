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
require_once 'Gamebooks/GBDB.php';

/**
 * Database Row Abstract Base Class
 *
 * This class provides a generic mechanism for accessing gamebook database tables.
 * It needs to be extended for each individual table within the database.  At the
 * very least, each child class's constructor must populate the table, writableKeys
 * and idKey properties with appropriate values.
 *
 * @author      Demian Katz
 * @access      public
 */
class Row
{
    protected $table = 'table'; // The name of the table represented by the class
    protected $idKey = 'id';    // The name of the ID column in $row
    protected $writableKeys;    // Names of all columns that may be legally modified
    protected $row = false;     // The database row in associative array format
    protected $id = false;      // The ID value of the current row (false for new)
    protected $db;              // GBDB class for database access
    
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
        // If we're given a valid row, save it; if we're given a number, assume
        // it's a database ID.
        if (is_array($input) && isset($input[$this->idKey])) {
            $this->row = $input;
            $this->id = $input[$this->idKey];
        } else if (is_numeric($input)) {
            $this->id = $input;
        }
        
        // Set up database connection:
        $this->db = new GBDB();
    }
    
    /**
     * Read in the current row from the database using the current ID value.
     *
     * @access  private
     */
    private function loadRow()
    {
        if ($this->id !== false) {
            $safeId = $this->db->escape($this->id);
            $sql = "SELECT * FROM {$this->table} WHERE {$this->idKey}='{$safeId}';";
            $result = $this->db->query($sql);
            if ($result) {
                $this->row = $this->db->fetchAssoc($result);
            }
        }
    }
    
    /**
     * Fetch the current contents of the object, loading from the database if
     * necessary.
     *
     * @access  public
     * @return  mixed                   Boolean false on error, associative
     *                                  array of columns otherwise.
     */
    public function getRow()
    {
        if (!$this->row) {
            $this->loadRow();
        }
        return $this->row;
    }
    
    /**
     * Insert the current object into the database.  Support method for save;
     * DO NOT CALL DIRECTLY!
     *
     * @access  private
     * @return  boolean                 True on success, false on failure.
     */
    private function insert()
    {
        // Build the appropriate SQL:
        $keys = array();
        $values = array();
        if (is_array($this->row)) {
            foreach($this->row as $key => $value) {
                // Only update writable columns!
                if (is_array($this->writableKeys) && 
                    in_array($key, $this->writableKeys)) {
                    $keys[] = $key;
                    $values[] = "'" . $this->db->escape($value) . "'";
                }
            }
        }
        $sql = "INSERT INTO {$this->table}";
        if (!empty($keys)) {
            $sql .= '(' . implode(', ', $keys) . ') VALUES (' .
                implode(', ', $values) . ')';
        }
        $sql .= ';';
        
        // Execute the SQL and load the new ID value if successful:
        $result = $this->db->query($sql);
        if (!$result) {
            return false;
        }
        $this->id = $this->db->getNewID();
        
        // Reload the row to get any defaults generated during the insert process.
        $this->loadRow();
        
        // Done -- report success.
        return true;
    }
    
    /**
     * Update the current object in the database.  Support method for save;
     * DO NOT CALL DIRECTLY!
     *
     * @access  private
     * @return  boolean                 True on success, false on failure.
     */
    private function update()
    {
        // Build the appropriate SQL:
        $clauses = array();
        if (is_array($this->row)) {
            foreach($this->row as $key => $value) {
                // Only update writable columns!
                if (is_array($this->writableKeys) && 
                    in_array($key, $this->writableKeys)) {
                    $clauses[] = $key . "='" . $this->db->escape($value) . "'";
                }
            }
        }
        
        // No changes?  Don't do anything!
        if (empty($clauses)) {
            return true;
        }
        
        // Build and execute the query:
        $safeId = $this->db->escape($this->id);
        $sql = "UPDATE {$this->table} SET " . implode(', ', $clauses) .
            " WHERE {$this->idKey}='{$safeId}';";
        return $this->db->query($sql);
    }
    
    /**
     * Save the current values in the object back to the database.
     *
     * @access  public
     * @return  boolean                 True on success, false on failure.
     */
    public function save()
    {
        // Is this a new object?  If so, we need to perform an insert.
        if (!$this->id) {
            return $this->insert();
        }
        
        // Default behavior: update an existing row.
        return $this->update();
    }
    
    /**
     * Set the value of a column in the row.  Note that changes are not written
     * to the database until the save method is called!
     *
     * @access  public
     * @param   string  $key            Name of column to update
     * @param   string  $value          Value to store in column
     * @return  boolean                 True on success, false on attempt to write
     *                                  to illegal column.
     */
    public function set($key, $value)
    {
        if (is_array($this->writableKeys) && in_array($key, $this->writableKeys)) {
            $this->row[$key] = $value;
            return true;
        }
        return false;
    }
}

?>