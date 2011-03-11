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
require_once 'Gamebooks/Tables/File.php';
require_once 'Gamebooks/Tables/Item.php';
require_once 'Gamebooks/Tables/Series.php';
require_once 'Gamebooks/Tables/Person.php';
require_once 'Gamebooks/Tables/FileType.php';

/**
 * Handler for edit person page.
 *
 * @param   UI  $interface      Current instance of user interface class.
 */
function edit_file($interface)
{
    // Load details on the selected file:
    if (is_numeric($_GET['id'])) {
        $file = new File($_GET['id']);
        $row = $file->getRow();
        if (!$row) {
            die('Cannot load file');
        }
        $interface->assign('file', $row);
        
        // Load items, series and people associated with the file:
        $people = new PersonList();
        $interface->assign('peopleFiles', $people->getByFile($_GET['id']));
        $items = new ItemList();
        $interface->assign('itemFiles', $items->getByFile($_GET['id']));
        $series = new SeriesList();
        $interface->assign('seriesFiles', $series->getByFile($_GET['id']));
    } else {
        $interface->assign('file', array('File_ID' => 'NEW'));
    }
    
    // Get the full list of file types:
    $types = new FileTypeList();
    $types->assign($interface);
    
    // Display page with appropriate Javascript:
    $interface->addCSS('ui-lightness/jquery-ui-autocomplete.css');
    $interface->addJavascript('jquery-ui-autocomplete.js');
    $interface->addJavascript('edit_file.js');
    $interface->showPage('file_edit_all.tpl');
}
?>