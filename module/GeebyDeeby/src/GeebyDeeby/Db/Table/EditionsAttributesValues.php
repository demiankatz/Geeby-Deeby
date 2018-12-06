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
     */
    public function __construct()
    {
        parent::__construct('Editions_Attributes_Values');
    }

    /**
     * Get a list of attributes for the specified edition.
     *
     * @var int $editionID Edition ID
     *
     * @return mixed
     */
    public function getAttributesForEdition($editionID)
    {
        $callback = function ($select) use ($editionID) {
            $select->join(
                array('ea' => 'Editions_Attributes'),
                'ea.Editions_Attribute_ID = '
                . 'Editions_Attributes_Values.Editions_Attribute_ID'
            );
            $select->order(array('ea.Display_Priority', 'ea.Editions_Attribute_Name'));
            $select->where->equalTo('Edition_ID', $editionID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of attributes for the specified item.
     *
     * @var int $itemID Item ID
     *
     * @return mixed
     */
    public function getAttributesForItem($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->join(
                array('e' => 'Editions'),
                'e.Edition_ID = Editions_Attributes_Values.Edition_ID'
            );
            $select->join(
                array('i' => 'Items'), 'e.Item_ID = i.Item_ID'
            );
            $select->join(
                array('ea' => 'Editions_Attributes'),
                'ea.Editions_Attribute_ID = '
                . 'Editions_Attributes_Values.Editions_Attribute_ID'
            );
            $select->order(array('ea.Display_Priority', 'ea.Editions_Attribute_Name'));
            $select->where->equalTo('i.Item_ID', $itemID);
        };
        return $this->select($callback);
    }
}
