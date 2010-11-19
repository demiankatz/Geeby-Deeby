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
require_once 'Gamebooks/UI.php';
require_once 'Gamebooks/util.php';
require_once 'Gamebooks/CurrentUser.php';

// Start up a session
session_start();

// We don't want to work with the magic_quotes_gpc setting!
undoMagicQuotes();

// Avoid browser caching:
doNotCache();

// Special case -- handle logout page:
if ($_GET['page'] == 'logout') {
    CurrentUser::logOut();
    header('Location: index.php');
    die();
}

// Initialize user interface:
$interface = new UI('edit');
$interface->addCSS('edit.css');

// Process login -- either validate the current username and password being
// submitted or else check if there is already a user in the session.
$user = (isset($_REQUEST['user']) && isset($_REQUEST['pass'])) ?
    CurrentUser::passwordLogin($_REQUEST['user'], $_REQUEST['pass']) :
    CurrentUser::loggedIn();

// Flag whether or not the user is logged in:
$interface->assign('loggedIn', $user !== false);

// Display appropriate page based on the current parameters; note that if a user
// is not logged in, they are automatically forced to the login page.
$page = ($user == false) ? 'login' : $_GET['page'];

switch(checkPermission($page) ? $page : 'unauthorized') {
case 'approve':
case 'categories':
case 'countries':
case 'edit_item':
case 'edit_link':
case 'edit_person':
case 'edit_series':
case 'languages':
case 'links':
case 'login':
case 'materials':
case 'notes':
case 'platforms':
case 'people':
case 'publishers':
case 'series':
case 'unauthorized':
    require_once "Gamebooks/Pages/Edit/{$page}.php";
    $page($interface);
    break;
default:
    // Assign some permissions so we know which parts of the menu to display:
    $interface->assign('contentEditor', CurrentUser::hasPermission('Content_Editor'));
    $interface->assign('approver', CurrentUser::hasPermission('Approver'));
    $interface->assign('userEditor', CurrentUser::hasPermission('User_Editor'));
    $interface->setPageTitle('Main Menu');
    $interface->showPage('menu.tpl');
    break;
}

function checkPermission($page)
{
    // If no page is set, we want the main menu -- everyone can see that!
    if (empty($page)) {
        return true;
    }
    
    switch($page) {
    // Everyone has permission to log in and see errors!
    case 'login':
    case 'unauthorized':
        return true;
        break;
    // Only editors have permission to change things:
    case 'categories':
    case 'countries':
    case 'edit_item':
    case 'edit_link':
    case 'edit_person':
    case 'edit_series':
    case 'languages':
    case 'links':
    case 'materials':
    case 'notes':
    case 'platforms':
    case 'people':
    case 'publishers':
    case 'series':
        return CurrentUser::hasPermission('Content_Editor');
        break;
    // Only approvers have permission to accept new users/content:
    case 'approve':
        return CurrentUser::hasPermission('Approver');
        break;
    // Undefined pages are blocked by default -- this reduces the chances of a
    // new page being accidentally added without proper security levels.
    default:
        return false;
    }
}
?>