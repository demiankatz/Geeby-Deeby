<?php
/**
 * Table Definition for Editions
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2012.
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
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\Db\Table;
use Zend\Db\Sql\Select;

/**
 * Table Definition for Editions
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class Edition extends Gateway
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('Editions', 'GeebyDeeby\Db\Row\Edition');
    }

    /**
     * Get a list of editions.
     *
     * @return mixed
     */
    public function getList()
    {
        $callback = function ($select) {
            $select->order('Edition_Name');
        };
        return $this->select($callback);
    }

    /**
     * Get autocomplete suggestions.
     *
     * @param string $query The user query.
     * @param mixed  $limit Limit on returned rows (false for no limit).
     *
     * @return mixed
     */
    public function getSuggestions($query, $limit = false)
    {
        $callback = function ($select) use ($query, $limit) {
            if ($limit !== false) {
                $select->limit($limit);
            }
            $select->where->like('Edition_Name', $query . '%');
            $select->order('Edition_Name');
        };
        return $this->select($callback);
    }

    /**
     * Perform a keyword search.
     *
     * @param array $tokens Keywords.
     *
     * @return mixed
     */
    public function keywordSearch($tokens)
    {
        $callback = function ($select) use ($tokens) {
            foreach ($tokens as $token) {
                $select->where->like('Edition_Name', '%' . $token . '%');
            }
            $select->order('Edition_Name');
        };
        return $this->select($callback);
    }

    /**
     * Get a list of items for the specified series.
     *
     * @var int $seriesID Series ID
     *
     * @return mixed
     */
    public function getItemsForSeries($seriesID)
    {
        // Proxy item table (so handleGenericLink() can be used in
        // EditSeriesController):
        return $this->getDbTable('item')->getItemsForSeries($seriesID);
    }

    /**
     * Retrieve editions for the specified item.
     *
     * @param int $itemID Item ID.
     *
     * @return mixed
     */
    public function getEditionsForItem($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->where->equalTo('Item_ID', $itemID);
            $select->order('Edition_Name');
        };
        return $this->select($callback);
    }

    /**
     * Retrieve publishers for the specified match.
     *
     * @param string $field Field to match
     * @param string $value Value to match
     *
     * @return mixed
     */
    public function getPublishersForWhereClause($field, $value)
    {
        $callback = function ($select) use ($field, $value) {
            $select->join(
                array('sp' => 'Series_Publishers'),
                'Editions.Preferred_Series_Publisher_ID = sp.Series_Publisher_ID'
            );
            $select->join(
                array('p' => 'Publishers'),
                'sp.Publisher_ID = p.Publisher_ID'
            );
            $select->join(
                array('pa' => 'Publishers_Addresses'),
                'sp.Address_ID = pa.Address_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $select->join(
                array('c' => 'Countries'), 'pa.Country_ID = c.Country_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $select->join(
                array('ci' => 'Cities'), 'pa.City_ID = ci.City_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $select->join(
                array('n' => 'Notes'), 'sp.Note_ID = n.Note_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $select->where->equalTo($field, $value);
            $select->order(
                array(
                    'Edition_Name',
                    'Publisher_Name', 'Country_Name', 'City_Name', 'Street',
                )
            );
        };
        return $this->select($callback);
    }

    /**
     * Retrieve publishers for the specified edition.
     *
     * @param int $id Edition ID.
     *
     * @return mixed
     */
    public function getPublishersForEdition($id)
    {
        return $this->getPublishersForWhereClause('Edition_ID', $id);
    }
    /**
     * Retrieve publishers for the specified item.
     *
     * @param int $itemID Item ID.
     *
     * @return mixed
     */
    public function getPublishersForItem($itemID)
    {
        return $this->getPublishersForWhereClause('Item_ID', $itemID);
    }

    /**
     * Delete an edition if there are no attached data items.
     *
     * @param int $id ID of edition to delete
     *
     * @throws \Exception
     * @return void
     */
    public function safeDelete($id)
    {
        $select = array('Edition_ID' => $id);
        if (count($this->getDbTable('editionscredits')->select($select)) > 0) {
            throw new \Exception('Cannot delete - attached credits.');
        }
        if (count($this->getDbTable('editionsreleasedates')->select($select)) > 0) {
            throw new \Exception('Cannot delete - attached dates.');
        }
        $this->delete($select);
    }
}
