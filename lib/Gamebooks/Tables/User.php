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
    private $permissions = false;

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
     * Check if the user has the specified permission.
     *
     * @access  public
     * @param   string  $permission     The name of the permission to check.
     * @return  boolean                 True if action permitted, false otherwise.
     */
    public function hasPermission($permission)
    {
        // Make sure we have permissions available:
        $this->loadPermissions();

        // Check the permission:
        return (isset($this->permissions[$permission]) &&
            !empty($this->permissions[$permission]));
    }

    /**
     * Load permission data if not already available.
     *
     * @access  private
     */
    public function loadPermissions()
    {
        // If permissions are already loaded, we're done here:
        if ($this->permissions !== false) {
            return;
        // If permissions are not loaded and the current user is assigned to a
        // group, load the permissions for that group.
        } else if (isset($this->row['User_Group_ID']) &&
            !empty($this->row['User_Group_ID'])) {
            $safeGroup = intval($this->row['User_Group_ID']);
            $sql = "SELECT * FROM User_Groups WHERE User_Group_ID={$safeGroup}";
            $result = $this->db->query($sql);
            if ($result && ($permissions = $this->db->fetchAssoc($result))) {
                // Unset non-permission related fields:
                unset($permissions['User_Group_ID']);
                unset($permissions['Group_Name']);
                
                // Store remaining data:
                $this->permissions = $permissions;
                return;
            }
        }

        // If we got this far, we were unable to find permissions -- default to
        // "no permissions."        
        $this->permissions = array();
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
     * Get a list of users pending approval.
     *
     * @access  public
     * @return  array                   Selected contents of Users table.
     */    
    public function getUnapproved()
    {
        $list = array();
        
        // Don't select all fields -- no need to risk exposing password data!
        $sql = "SELECT User_ID, Username, Name, Address, Person_ID FROM Users " .
            "WHERE Person_ID=0 ORDER BY Username;";
        $res = $this->db->query($sql);
        while ($tmp = $this->db->fetchAssoc($res)) {
            $list[] = $tmp;
        }
        
        return $list;
    }

    /**
     * Get associative array representing user list.
     *
     * @access  public
     * @return  array                   Contents of Users table.
     */
    public function getList()
    {
        if ($this->list === false) {
            $this->list = array();

            // Don't select all fields -- no need to risk exposing password data!
            $sql = "SELECT User_ID, Username, Name, Address, Person_ID FROM Users " .
                "ORDER BY Username;";
            $res = $this->db->query($sql);
            while ($tmp = $this->db->fetchAssoc($res)) {
                $this->list[] = $tmp;
            }
        }
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