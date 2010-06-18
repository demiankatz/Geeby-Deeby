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
require_once 'Gamebooks/Tables/Person.php';

/**
 * Handler for edit person page.
 *
 * @param   UI  $interface      Current instance of user interface class.
 */
function edit_person($interface)
{
    // Load details on the selected person:
    if (is_numeric($_GET['id'])) {
        $person = new Person($_GET['id']);
        $row = $person->getRow();
        if (!$row) {
            die('Cannot load person');
        }
        $interface->assign('person', $row);
        $interface->assign('pseudonyms', $person->getPseudonyms());
        $interface->assign('realnames', $person->getRealNames());
    } else {
        $interface->assign('person', array('Person_ID' => 'NEW'));
        $interface->assign('pseudonyms', array());
        $interface->assign('realnames', array());
    }
    
    // Display page with appropriate Javascript:
    $interface->addCSS('ui-lightness/jquery-ui-autocomplete.css');
    $interface->addJavascript('jquery-ui-autocomplete.js');
    $interface->addJavascript('edit_person.js');
    $interface->showPage('people_edit_all.tpl');
}
?>