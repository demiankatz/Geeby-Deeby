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
require_once 'Gamebooks/Tables/Row.php';

/**
 * Link Type Class
 *
 * This class represents a link type from the database.
 *
 * @author      Demian Katz
 * @access      public
 */
class LinkType extends Row
{
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed   $input          Send in a full associative array fetched
     *                                  from the database to create a pre-populated
     *                                  object; send in a numeric ID value to fetch
     *                                  a known item from the database; send in
     *                                  boolean false to create a new row.
     */
    public function __construct($input = false)
    {
        $this->table = 'Link_Types';
        $this->idKey = 'Link_Type_ID';
        $this->writableKeys = array('Link_Type');
        
        parent::__construct($input);
    }
}

/**
 * Link Type List Class
 *
 * This class represents a set of link types from the database.
 *
 * @author      Demian Katz
 * @access      public
 */
class LinkTypeList
{
    private $db;
    private $list = false;
    
    /**
     * Constructor
     *
     * @access  public
     */
    public function __construct()
    {
        $this->db = new GBDB();
    }
    
    /**
     * Get associative array representing material type list.
     *
     * @access  public
     * @return  array                   Contents of Material_Types table.
     */
    public function getList()
    {
        // Load the list if we haven't already:
        if ($this->list === false) {
            $sql = "SELECT * FROM Link_Types ORDER BY Link_Type;";
            $res = $this->db->query($sql);
            while ($tmp = $this->db->fetchAssoc($res)) {
                $this->list[] = $tmp;
            }
        }
        return $this->list;
    }

    /**
     * Assign the link type list to the user interface.
     *
     * @access  public
     * @param   UI      $interface      User Interface object.
     */
    public function assign($interface)
    {
        $interface->assign('linkTypes', $this->getList());
    }
}
?>