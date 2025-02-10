<?php

/**
 * Table Definition for Editions_Attributes_Values
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

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\RowGateway\RowGateway;

use function is_array;

/**
 * Table Definition for Editions_Attributes_Values
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsAttributesValues extends Gateway
{
    /**
     * Constructor
     *
     * @param Adapter       $adapter Database adapter
     * @param PluginManager $tm      Table manager
     * @param RowGateway    $rowObj  Row prototype object (null for default)
     */
    public function __construct(
        Adapter $adapter,
        PluginManager $tm,
        RowGateway $rowObj = null
    ) {
        parent::__construct($adapter, $tm, $rowObj, 'Editions_Attributes_Values');
    }

    /**
     * Get a list of attributes for the specified edition.
     *
     * @param int $editionID Edition ID
     *
     * @return mixed
     */
    public function getAttributesForEdition($editionID)
    {
        $callback = function ($select) use ($editionID) {
            $select->join(
                ['ea' => 'Editions_Attributes'],
                'ea.Editions_Attribute_ID = '
                . 'Editions_Attributes_Values.Editions_Attribute_ID'
            );
            $select->order(['ea.Display_Priority', 'ea.Editions_Attribute_Name']);
            $select->where->equalTo('Edition_ID', $editionID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of attributes for the specified item.
     *
     * @param int|int[] $itemID      Item ID (or array of IDs)
     * @param ?int      $attributeID Attribute ID filter (null for all attributes)
     *
     * @return mixed
     */
    public function getAttributesForItem($itemID, $attributeID = null)
    {
        $callback = function ($select) use ($itemID, $attributeID) {
            $select->join(
                ['e' => 'Editions'],
                'e.Edition_ID = Editions_Attributes_Values.Edition_ID'
            );
            $select->join(
                ['i' => 'Items'],
                'e.Item_ID = i.Item_ID'
            );
            $select->join(
                ['ea' => 'Editions_Attributes'],
                'ea.Editions_Attribute_ID = '
                . 'Editions_Attributes_Values.Editions_Attribute_ID'
            );
            $select->order(['ea.Display_Priority', 'ea.Editions_Attribute_Name']);
            if (is_array($itemID)) {
                $select->where->in('i.Item_ID', $itemID);
            } else {
                $select->where->equalTo('i.Item_ID', $itemID);
            }
            if ($attributeID) {
                $select->where->equalTo('ea.Editions_Attribute_ID', $attributeID);
            }
        };
        return $this->select($callback);
    }
}
