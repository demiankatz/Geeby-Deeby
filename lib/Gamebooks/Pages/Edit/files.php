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
require_once 'Gamebooks/Tables/FileType.php';

/**
 * Handler for files page.
 *
 * @param   UI  $interface      Current instance of user interface class.
 */
function files($interface)
{
    // Load people and roles from database:
    $files = new FileList();
    $files->assign($interface);
    $fileTypes = new FileTypeList();
    $fileTypes->assign($interface);
    
    // Display page with appropriate Javascript:
    $interface->addJavascript('edit_files.js');
    $interface->showPage('files.tpl');
}
?>