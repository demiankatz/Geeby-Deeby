<?php
/**
 * Table Definition for Items_Links
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\Db\Table;

/**
 * Table Definition for Items_Links
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsLinks extends Gateway
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('Items_Links');
    }

    /**
     * Get a list of links for the specified item.
     *
     * @var int $itemID Item ID
     *
     * @return mixed
     */
    public function getLinksForItem($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->join(
                array('l' => 'Links'), 'Items_Links.Link_ID = l.Link_ID'
            );
            $select->order(array('Link_Name'));
            $select->where->equalTo('Item_ID', $itemID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of items for the specified link.
     *
     * @var int $linkID Link ID
     *
     * @return mixed
     */
    public function getItemsForLink($linkID)
    {
        $callback = function ($select) use ($linkID) {
            $select->join(
                array('i' => 'Items'),
                'Items_Links.Item_ID = i.Item_ID'
            );
            $select->order('i.Item_Name');
            $select->where->equalTo('Link_ID', $linkID);
        };
        return $this->select($callback);
    }
}
