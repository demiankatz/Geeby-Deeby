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

/**
 * Edit AJAX Support
 *
 * This class provides all AJAX functionality for the edit module.
 *
 * @author      Demian Katz
 * @access      public
 */
class AJAX_Edit_Base
{
    protected $interface;
    
    /**
     * Constructor
     *
     * @access  public
     */
    public function __construct()
    {
        // Activate the UI in case we need it.
        $this->interface = new UI('edit');
        
        // Call the specified method.
        $method = $_GET['method'];
        if (strtolower($method) != '__construct' && method_exists($this, $method)) {
            $this->$method();
        } else {
            die('Invalid method');
        }
    }
    
    /**
     * Die with a JSON-encoded error message.
     *
     * @access  public
     * @param   string  $msg            The error message to send back.
     */
    public function jsonDie($msg)
    {
        $error = array('success' => false, 'msg' => $msg);
        die(json_encode($error));
    }
    
    /**
     * Die with a JSON success status.
     *
     * @access  public
     */
    public function jsonReportSuccess()
    {
        die(json_encode(array('success' => true)));
    }
}
?>