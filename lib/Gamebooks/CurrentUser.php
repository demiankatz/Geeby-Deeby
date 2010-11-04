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
require_once 'Gamebooks/Tables/User.php';

/**
 * CurrentUser Class
 *
 * This class manages the current logged in user.
 *
 * @author      Demian Katz
 * @access      public
 */
class CurrentUser
{
    /**
     * Check if the current user has the specified permission.
     *
     * @access  public
     * @param   string  $permission     The name of the permission to check.
     * @return  boolean                 True if action permitted, false otherwise.
     */
    public static function hasPermission($permission)
    {
        // If the user isn't logged in, they have no permissions!
        $user = self::loggedIn();
        if (!$user) {
            return false;
        }

        // User is logged in... so check permissions:
        return $user->hasPermission($permission);
    }

    /**
     * Attempt to log in using the specified username and password.
     *
     * @access  public
     * @param   string  $username       Username for login.
     * @param   string  $password       Password for login.
     * @return  mixed                   User object if login successful, or false.
     */
    public static function passwordLogin($username, $password)
    {
        $user = new User();
        if ($user->passwordLogin($username, $password)) {
            $_SESSION['gbdbUser'] = $user;
            return $user;
        }
        return false;
    }

    /** 
     * Get the current logged in User object, false if no logged-in user.
     *
     * @access  public
     * @return  mixed                   User object if logged in, false otherwise.
     */
    public static function loggedIn()
    {
        if (isset($_SESSION['gbdbUser'])) {
            return $_SESSION['gbdbUser'];
        }
        return false;
    }
    
    /**
     * Log out the current user.
     *
     * @access  public
     */
    public static function logOut()
    {
        unset($_SESSION['gbdbUser']);
    }
}

?>