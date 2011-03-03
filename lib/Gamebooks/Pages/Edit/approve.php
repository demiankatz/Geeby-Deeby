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
require_once 'Gamebooks/Tables/Item.php';
require_once 'Gamebooks/Tables/Series.php';
require_once 'Gamebooks/Tables/User.php';

/**
 * Handler for approval page.
 *
 * @param   UI  $interface      Current instance of user interface class.
 */
function approve($interface)
{
    // Load pending content from database:
    $list = new UserList();
    $interface->assign('newUsers', $list->getUnapproved());
    $list = new ItemList();
    $interface->assign('pendingReviews', $list->getPendingReviews());
    $list = new SeriesList();
    $interface->assign('pendingComments', $list->getPendingReviews());
    
    // Display page with appropriate Javascript:
    $interface->addCSS('ui-lightness/jquery-ui-autocomplete.css');
    $interface->addJavascript('jquery-ui-autocomplete.js');
    $interface->addJavascript('approve.js');
    $interface->showPage('approve.tpl');
}
?>