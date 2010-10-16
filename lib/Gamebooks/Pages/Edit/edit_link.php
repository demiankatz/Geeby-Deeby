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
require_once 'Gamebooks/Tables/Link.php';
require_once 'Gamebooks/Tables/Item.php';
require_once 'Gamebooks/Tables/Series.php';
require_once 'Gamebooks/Tables/Person.php';
require_once 'Gamebooks/Tables/LinkType.php';

/**
 * Handler for edit person page.
 *
 * @param   UI  $interface      Current instance of user interface class.
 */
function edit_link($interface)
{
    // Load details on the selected link:
    if (is_numeric($_GET['id'])) {
        $link = new Link($_GET['id']);
        $row = $link->getRow();
        if (!$row) {
            die('Cannot load link');
        }
        $interface->assign('link', $row);
        
        // Load items, series and people associated with the link:
        $people = new PersonList();
        $interface->assign('peopleLinks', $people->getByLink($_GET['id']));
        $items = new ItemList();
        $interface->assign('itemLinks', $items->getByLink($_GET['id']));
        $series = new SeriesList();
        $interface->assign('seriesLinks', $series->getByLink($_GET['id']));
    } else {
        $interface->assign('link', array('Link_ID' => 'NEW'));
    }
    
    // Get the full list of link types:
    $types = new LinkTypeList();
    $types->assign($interface);
    
    // Display page with appropriate Javascript:
    $interface->addCSS('ui-lightness/jquery-ui-autocomplete.css');
    $interface->addJavascript('jquery-ui-autocomplete.js');
    $interface->addJavascript('edit_link.js');
    $interface->showPage('link_edit_all.tpl');
}
?>