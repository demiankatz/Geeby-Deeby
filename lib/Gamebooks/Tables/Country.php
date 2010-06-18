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
 * Country Class
 *
 * This class represents a country from the database.
 *
 * @author      Demian Katz
 * @access      public
 */
class Country extends Row
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
        $this->table = 'Countries';
        $this->idKey = 'Country_ID';
        $this->writableKeys = array('Country_Name');
        
        parent::__construct($input);
    }
}

/**
 * Country List Class
 *
 * This class represents a set of countries from the database.
 *
 * @author      Demian Katz
 * @access      public
 */
class CountryList
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
        
        $sql = "SELECT Country_ID, Country_Name FROM Countries ORDER BY Country_Name;";
        $countriesRes = $this->db->query($sql);
        while ($tmp = $this->db->fetchAssoc($countriesRes)) {
            $this->list[] = $tmp;
        }
    }
    
    /**
     * Get associative array representing country list.
     *
     * @access  public
     * @return  array                   Contents of Countries table.
     */
    public function getList()
    {
        return $this->list;
    }
    
    /**
     * Assign the country list to the user interface.
     *
     * @access  public
     * @param   UI      $interface      User Interface object.
     */
    public function assign($interface)
    {
        $interface->assign('countries', $this->list);
    }
}
?>