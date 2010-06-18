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
 * Category Class
 *
 * This class represents a category from the database.
 *
 * @author      Demian Katz
 * @access      public
 */
class Category extends Row
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
        $this->table = 'Categories';
        $this->idKey = 'Category_ID';
        $this->writableKeys = array('Category', 'Description');
        
        parent::__construct($input);
    }
}

/**
 * Category List Class
 *
 * This class represents a set of categories from the database.
 *
 * @author      Demian Katz
 * @access      public
 */
class CategoryList
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
        
        $sql = "SELECT * FROM Categories ORDER BY Category;";
        $categoriesRes = $this->db->query($sql);
        while ($tmp = $this->db->fetchAssoc($categoriesRes)) {
            $this->list[] = $tmp;
        }
    }
    
    /**
     * Get associative array representing category list.
     *
     * @access  public
     * @return  array                   Contents of Categories table.
     */
    public function getList()
    {
        return $this->list;
    }
    
    /**
     * Assign the category list to the user interface.
     *
     * @access  public
     * @param   UI      $interface      User Interface object.
     */
    public function assign($interface)
    {
        $interface->assign('categories', $this->list);
    }
}
?>