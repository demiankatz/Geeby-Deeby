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
 * User Class
 *
 * This class represents a user from the database.
 *
 * @author      Demian Katz
 * @access      public
 */
class User extends Row
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
        $this->table = 'Users';
        $this->idKey = 'User_ID';
        $this->writableKeys = array('Username', 'Password', 'Name', 'Address', 
            'Person_ID');
        
        parent::__construct($input);
    }
    
    /**
     * Attempt to log in using the specified username and password.  On successful
     * login, the specified user's row will be loaded into the object.
     *
     * @access  public
     * @param   string  $username       Username for login.
     * @param   string  $password       Password for login.
     * @return  boolean                 True on successful login, false otherwise.
     */
    public function passwordLogin($username, $password)
    {
        $safeUser = $this->db->escape($username);
        $safePass = $this->db->escape($password);
        $sql = "SELECT * FROM {$this->table} " .
            "WHERE Username='{$safeUser}' AND Password='{$safePass}';";
        $result = $this->db->query($sql);
        if ($result) {
            $this->row = $this->db->fetchAssoc($result);
            return ($this->row === false) ? false : true;
        }
        return false;
    }
}

/**
 * User List Class
 *
 * This class represents a set of users from the database.
 *
 * @author      Demian Katz
 * @access      public
 */
class UserList
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
        
        // Don't select all fields -- no need to risk exposing password data!
        $sql = "SELECT User_ID, Username, Name, Address, Person_ID FROM Users " .
            "ORDER BY Username;";
        $res = $this->db->query($sql);
        while ($tmp = $this->db->fetchAssoc($res)) {
            $this->list[] = $tmp;
        }
    }
    
    /**
     * Get associative array representing user list.
     *
     * @access  public
     * @return  array                   Contents of Users table.
     */
    public function getList()
    {
        return $this->list;
    }
    
    /**
     * Assign the user list to the user interface.
     *
     * @access  public
     * @param   UI      $interface      User Interface object.
     */
    public function assign($interface)
    {
        $interface->assign('users', $this->list);
    }
}
?>