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
require_once 'Gamebooks/AJAX/Edit/Base.php';

/**
 * Edit AJAX Support
 *
 * This class provides an error message when an invalid edit module is specified.
 *
 * @author      Demian Katz
 * @access      public
 */
class AJAX_Edit_Invalid extends AJAX_Edit_Base
{
    /**
     * Constructor
     *
     * @access  public
     */
    public function __construct()
    {
        $this->jsonDie('Invalid module.');
    }
}
?>