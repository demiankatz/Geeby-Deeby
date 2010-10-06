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
require_once 'Gamebooks/Tables/Role.php';
require_once 'Gamebooks/Tables/MaterialType.php';
require_once 'Gamebooks/Tables/Platform.php';

/**
 * Handler for edit item page.
 *
 * @param   UI  $interface      Current instance of user interface class.
 */
function edit_item($interface)
{
    // Load details on the selected series:
    if (is_numeric($_GET['id'])) {
        $item = new Item($_GET['id']);
        $row = $item->getRow();
        if (!$row) {
            die('Cannot load item');
        }
        $interface->assign('item', $row);
    } else {
        die('Cannot load item');
    }
    
    // We'll need material information:
    $matList = new MaterialTypeList();
    $matList->assign($interface);
    
    // We'll need platform information:
    $plats = new PlatformList();
    $plats->assign($interface);
    $interface->assign('item_platforms', $plats->getListForItem($_GET['id']));
    
    // We'll need role information:
    $roles = new RoleList();
    $roles->assign($interface);
    
    // Save various lists of data about the item:
    $interface->assign('item_alt_titles', $item->getAltTitles());
    $interface->assign('releaseDates', $item->getDates());
    $interface->assign('descriptionTypes', $item->getDescriptionTypes());
    $interface->assign('descriptions', $item->getDescriptions());
    $interface->assign('credits', $item->getCredits());
    $interface->assign('images', $item->getImages());
    $interface->assign('productCodes', $item->getProductCodes());
    $interface->assign('ISBNs', $item->getISBNs());
    $list = new ItemList();
    $interface->assign('translatedFrom', $list->getTranslatedFrom($_GET['id']));
    $interface->assign('translatedInto', $list->getTranslations($_GET['id']));
    $interface->assign('adaptedFrom', $list->getAdaptedFrom($_GET['id']));
    $interface->assign('adaptedInto', $list->getAdaptations($_GET['id']));
    $interface->assign('item_list', $list->getFromCollection($_GET['id']));
    $interface->assign('itemBib', $list->getReferencedBy($_GET['id']));
    
    // Display page with appropriate Javascript:
    $interface->addCSS('ui-lightness/jquery-ui-autocomplete.css');
    $interface->addJavascript('jquery-ui-autocomplete.js');
    $interface->addJavascript('ui.tabs.paging.js');
    $interface->addJavascript('edit_item_details.js');
    $interface->showPage('item_edit_all.tpl');
}
?>