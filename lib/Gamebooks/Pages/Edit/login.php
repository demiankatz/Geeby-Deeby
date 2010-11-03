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

/**
 * Handler for login page.
 *
 * @param   UI  $interface      Current instance of user interface class.
 */
function login($interface)
{
    // If username and password are present in the request, the user must have
    // failed to log in -- allow them to revise their choices, and display an
    // error message.
    if (isset($_REQUEST['user']) && isset($_REQUEST['pass'])) {
        $interface->assign('user', $_REQUEST['user']);
        $interface->assign('pass', $_REQUEST['pass']);
        $interface->assign('msg', 'Login failed.  Please try again.');
    }
    
    // Display page with appropriate Javascript:
    $interface->showPage('login.tpl');
}
?>