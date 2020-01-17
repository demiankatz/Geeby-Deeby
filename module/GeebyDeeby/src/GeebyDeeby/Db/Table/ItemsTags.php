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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\Db\Table;

use Zend\Db\Adapter\Adapter;
use Zend\Db\RowGateway\RowGateway;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;

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
     *
     * @param Adapter       $adapter Database adapter
     * @param PluginManager $tm      Table manager
     * @param RowGateway    $rowObj  Row prototype object (null for default)
     */
    public function __construct(Adapter $adapter, PluginManager $tm,
        RowGateway $rowObj = null
    ) {
        parent::__construct($adapter, $tm, $rowObj, 'Items_Tags');
    }

    /**
     * Get items for the specified tag.
     *
     * @param int $tagID Tag ID
     *
     * @return mixed
     */
    public function getItemsForTag($tagID)
    {
        $callback = function ($select) use ($tagID) {
            $select->join(
                ['i' => 'Items'],
                'Items_Tags.Item_ID = i.Item_ID'
            );
            $select->join(
                ['eds' => 'Editions'], 'i.Item_ID = eds.Item_ID',
                ['Volume', 'Position', 'Replacement_Number']
            );
            $select->join(
                ['iat' => 'Items_AltTitles'],
                'eds.Preferred_Item_AltName_ID = iat.Sequence_ID',
                ['Item_AltName'], Select::JOIN_LEFT
            );
            $select->join(
                ['s' => 'Series'], 'eds.Series_ID = s.Series_ID'
            );
            $select->group(
                [
                    'i.Item_ID', 'eds.Volume', 'eds.Position',
                    'eds.Replacement_Number'
                ]
            );
            $select->order(
                [
                    'Series_Name', 's.Series_ID', 'eds.Volume', 'eds.Position',
                    'eds.Replacement_Number',
                    new Expression(
                        'COALESCE(?, ?)',
                        ['Item_AltName', 'Item_Name'],
                        [
                            Expression::TYPE_IDENTIFIER,
                            Expression::TYPE_IDENTIFIER
                        ]
                    )
                ]
            );
            $select->where->equalTo('Tag_ID', $tagID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of tags for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return mixed
     */
    public function getTags($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->join(['t' => 'Tags'], 't.Tag_ID = Items_Tags.Tag_ID');
            $select->order('Tag');
            $select->where->equalTo('Item_ID', $itemID);
        };
        return $this->select($callback);
    }
}
