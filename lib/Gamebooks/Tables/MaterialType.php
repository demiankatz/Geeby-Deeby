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
 * Material Type Class
 *
 * This class represents a material type from the database.
 *
 * @author      Demian Katz
 * @access      public
 */
class MaterialType extends Row
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
        $this->table = 'Material_Types';
        $this->idKey = 'Material_Type_ID';
        $this->writableKeys = array('Material_Type_Name');
        
        parent::__construct($input);
    }
}

/**
 * Material Type List Class
 *
 * This class represents a set of material types from the database.
 *
 * @author      Demian Katz
 * @access      public
 */
class MaterialTypeList
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
            $sql = "SELECT Material_Type_ID, Material_Type_Name FROM Material_Types ORDER BY Material_Type_Name;";
            $materialsRes = $this->db->query($sql);
            while ($tmp = $this->db->fetchAssoc($materialsRes)) {
                $this->list[] = $tmp;
            }
        }
        return $this->list;
    }
    
    /**
     * Get associative array representing material types belonging to a series.
     *
     * @access  public
     * @param   $seriesID               ID of series from Series table.
     * @return  array                   Selected contents of Material_Types table.
     */
    public function getListForSeries($seriesID)
    {
        // Sanitize input:
        $id = intval($seriesID);
        $sql = "SELECT Material_Types.* FROM Series_Material_Types " .
            "JOIN Material_Types " .
            "ON Series_Material_Types.Material_Type_ID=Material_Types.Material_Type_ID " .
            "WHERE Series_Material_Types.Series_ID='{$id}' " .
            "ORDER BY Material_Types.Material_Type_Name;";
        $result = $this->db->query($sql);
        $list = array();
        while ($tmp = $this->db->fetchAssoc($result)) {
            $list[] = $tmp;
        }
        return $list;
    }
    
    /**
     * Assign the material type list to the user interface.
     *
     * @access  public
     * @param   UI      $interface      User Interface object.
     */
    public function assign($interface)
    {
        $interface->assign('materials', $this->getList());
    }
}
?>