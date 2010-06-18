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
require_once 'Gamebooks/Tables/Row.php';

/**
 * Platform Class
 *
 * This class represents a platform from the database.
 *
 * @author      Demian Katz
 * @access      public
 */
class Platform extends Row
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
        $this->table = 'Platforms';
        $this->idKey = 'Platform_ID';
        $this->writableKeys = array('Platform');
        
        parent::__construct($input);
    }
}

/**
 * Platform List Class
 *
 * This class represents a set of platforms from the database.
 *
 * @author      Demian Katz
 * @access      public
 */
class PlatformList
{
    private $db;
    private $list = array();
    
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
     * Get associative array representing platform list.
     *
     * @access  public
     * @return  array                   Contents of Platforms table.
     */
    public function getList()
    {
        if (empty($this->list)) {
            $sql = "SELECT Platform_ID, Platform FROM Platforms ORDER BY Platform;";
            $platformsRes = $this->db->query($sql);
            while ($tmp = $this->db->fetchAssoc($platformsRes)) {
                $this->list[] = $tmp;
            }
        }
        return $this->list;
    }
    
    /**
     * Get associative array representing platforms belonging to an item.
     *
     * @access  public
     * @param   $itemID                 ID of item from Items table.
     * @return  array                   Selected contents of Platforms table.
     */
    public function getListForItem($itemID)
    {
        // Sanitize input:
        $id = intval($itemID);
        $sql = "SELECT Platforms.* FROM Items_Platforms JOIN Platforms " .
            "ON Items_Platforms.Platform_ID=Platforms.Platform_ID " .
            "WHERE Items_Platforms.Item_ID='{$id}' " .
            "ORDER BY Platforms.Platform;";
        $result = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($result)) {
            $list[] = $tmp;
        }
        return $list;
    }
    
    /**
     * Assign the platform list to the user interface.
     *
     * @access  public
     * @param   UI      $interface      User Interface object.
     */
    public function assign($interface)
    {
        $interface->assign('platforms', $this->getList());
    }
}
?>