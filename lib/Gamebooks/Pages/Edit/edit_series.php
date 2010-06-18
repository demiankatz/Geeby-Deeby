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
require_once 'Gamebooks/Tables/Item.php';
require_once 'Gamebooks/Tables/Series.php';
require_once 'Gamebooks/Tables/Language.php';
require_once 'Gamebooks/Tables/MaterialType.php';
require_once 'Gamebooks/Tables/Country.php';
require_once 'Gamebooks/Tables/Category.php';

/**
 * Handler for edit series page.
 *
 * @param   UI  $interface      Current instance of user interface class.
 */
function edit_series($interface)
{
    // Load details on the selected series:
    if (is_numeric($_GET['id'])) {
        $series = new Series($_GET['id']);
        $row = $series->getRow();
        if (!$row) {
            die('Cannot load series');
        }
        $interface->assign('series', $row);
    } else {
        die('Cannot load series');
    }
    
    // We'll need the language list in addition to the series data:
    $lang = new LanguageList();
    $lang->assign($interface);
    
    // We need Material Type data:
    $mt = new MaterialTypeList();
    $mt->assign($interface);            // list all types
    $interface->assign('series_materials', $mt->getListForSeries($_GET['id']));
    
    // Load categories from database:
    $categories = new CategoryList();
    $categories->assign($interface);
    $interface->assign('selected_categories', $series->getCategories());
    
    // Load countries from database:
    $countries = new CountryList();
    $countries->assign($interface);
    
    // Save various lists of data about the series:
    $interface->assign('series_publishers', $series->getPublishers());
    $interface->assign('series_alt_titles', $series->getAltTitles());
    $list = new SeriesList();
    $interface->assign('translatedFrom', $list->getTranslatedFrom($_GET['id']));
    $interface->assign('translatedInto', $list->getTranslations($_GET['id']));
    $items = new ItemList();
    $interface->assign('item_list', $items->getFromSeries($_GET['id']));
    
    // Display page with appropriate Javascript:
    $interface->addCSS('ui-lightness/jquery-ui-autocomplete.css');
    $interface->addJavascript('jquery-ui-autocomplete.js');
    $interface->addJavascript('edit_series_details.js');
    $interface->showPage('series_edit_all.tpl');
}
?>