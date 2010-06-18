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

// We don't want to work with the magic_quotes_gpc setting!
undoMagicQuotes();

// Avoid browser caching:
doNotCache();

// Initialize user interface:
$interface = new UI('edit');
$interface->addCSS('edit.css');

// Display appropriate page based on the current parameters:
$page = $_GET['page'];
switch($page) {
case 'categories':
case 'countries':
case 'edit_item':
case 'edit_person':
case 'edit_series':
case 'languages':
case 'materials':
case 'notes':
case 'platforms':
case 'people':
case 'publishers':
case 'series':
    require_once "Gamebooks/Pages/Edit/{$page}.php";
    $page($interface);
    break;
default:
    $interface->setPageTitle('Main Menu');
    $interface->showPage('menu.tpl');
    break;
}
?>