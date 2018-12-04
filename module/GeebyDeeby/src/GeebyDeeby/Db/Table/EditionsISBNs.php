<?php
/**
 * Table Definition for Editions_ISBNs
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
 * Table Definition for Editions_ISBNs
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsISBNs extends Gateway
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('Editions_ISBNs');
    }

    /**
     * Get a list of ISBNs for the specified item.
     *
     * @var int $itemID Item ID
     *
     * @return mixed
     */
    public function getISBNsForItem($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->join(
                array('n' => 'Notes'),
                'Editions_ISBNs.Note_ID = n.Note_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $select->join(
                array('eds' => 'Editions'),
                'Editions_ISBNs.Edition_ID = eds.Edition_ID'
            );
            $select->order('ISBN13');
            $select->where->equalTo('Item_ID', $itemID);
        };
        return $this->select($callback);
    }

    /**
     * Find items matching an ISBN search query.
     *
     * @var string $q Query
     *
     * @return mixed
     */
    public function searchForItems($q)
    {
        $callback = function ($select) use ($q) {
            $select->join(
                array('eds' => 'Editions'),
                'Editions_ISBNs.Edition_ID = eds.Edition_ID'
            );
            $select->join(
                array('i' => 'Items'), 'eds.Item_ID = i.Item_ID'
            );
            $select->order('Item_Name');
            $select->group('i.Item_ID');
            $select->where->like('ISBN', $q . '%');
            $select->where->OR->like('ISBN13', $q . '%');
        };
        return $this->select($callback);
    }
}
