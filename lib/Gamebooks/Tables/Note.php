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
 * Note Class
 *
 * This class represents a note from the database.
 *
 * @author      Demian Katz
 * @access      public
 */
class Note extends Row
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
        $this->table = 'Notes';
        $this->idKey = 'Note_ID';
        $this->writableKeys = array('Note');
        
        parent::__construct($input);
    }
}

/**
 * Note List Class
 *
 * This class represents a set of notes from the database.
 *
 * @author      Demian Katz
 * @access      public
 */
class NoteList
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
        
        $sql = "SELECT Note_ID, Note FROM Notes ORDER BY Note;";
        $notesRes = $this->db->query($sql);
        while ($tmp = $this->db->fetchAssoc($notesRes)) {
            $this->list[] = $tmp;
        }
    }
    
    /**
     * Get associative array representing note list.
     *
     * @access  public
     * @return  array                   Contents of Notes table.
     */
    public function getList()
    {
        return $this->list;
    }
    
    /**
     * Assign the note list to the user interface.
     *
     * @access  public
     * @param   UI      $interface      User Interface object.
     */
    public function assign($interface)
    {
        $interface->assign('notes', $this->list);
    }
    
    /**
     * Return rows associated with a query typed into an autosuggester.
     *
     * @access  public
     * @param   string  $query          The user query.
     * @param   mixed   $limit          Limit on returned rows (false for no limit).
     * @return  array                   Selected contents of Notes table.
     */
    public function getSuggestions($query, $limit = false)
    {
        $query = $this->db->escape($query);
        $sql = "SELECT * FROM Notes " .
            "WHERE Note LIKE '{$query}%' ORDER BY Note";
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