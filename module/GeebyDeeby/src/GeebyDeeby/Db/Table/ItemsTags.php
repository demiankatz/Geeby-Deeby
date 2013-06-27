<?php
/**
 * Table Definition for Items_Tags
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

/**
 * Table Definition for Items_Tags
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsTags extends Gateway
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('Items_Tags');
    }

    /**
     * Get items for the specified tag.
     *
     * @var int $tagID Tag ID
     */
    public function getItemsForTag($tagID)
    {
        $callback = function ($select) use ($tagID) {
            $select->join(
                array('i' => 'Items'),
                'Items_Tags.Item_ID = i.Item_ID'
            );
            $select->join(
                array('eds' => 'Editions'), 'i.Item_ID = eds.Item_ID',
                array('Position')
            );
            $select->join(
                array('s' => 'Series'), 'eds.Series_ID = s.Series_ID'
            );
            $select->group(array('i.Item_ID', 'eds.Position'));
            $select->order(
                array('Series_Name', 's.Series_ID', 'eds.Position', 'Item_Name')
            );
            $select->where->equalTo('Tag_ID', $tagID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of tags for the specified item.
     *
     * @var int $itemID Item ID
     *
     * @return mixed
     */
    public function getTags($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->join(array('t' => 'Tags'), 't.Tag_ID = Items_Tags.Tag_ID');
            $select->order('Tag');
            $select->where->equalTo('Item_ID', $itemID);
        };
        return $this->select($callback);
    }
}
