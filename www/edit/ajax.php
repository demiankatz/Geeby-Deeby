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
require_once 'Gamebooks/util.php';

// We don't want to work with the magic_quotes_gpc setting!
undoMagicQuotes();

// Avoid browser caching:
doNotCache();

// Normalize and validate the module name:
$module = ucwords($_GET['module']);
switch($module) {
case 'Linktype':
    // Fix capitalization:
    $module = 'LinkType';
    break;
case 'Materialtype':
    // Fix capitalization:
    $module = 'MaterialType';
    break;
case 'Category':
case 'Country':
case 'Item':
case 'Language':
case 'Link':
case 'Note':
case 'Platform':
case 'People':
case 'Publisher':
case 'Role':
case 'Series':
    // Name is valid -- no special action required.
    break;
default:
    die('Unrecognized AJAX module');
}

// Load the appropriate AJAX routine -- we know it is valid if we got this far.
require_once "Gamebooks/AJAX/Edit/{$module}.php";
$class = "AJAX_Edit_{$module}";
$ajax = new $class();

?>