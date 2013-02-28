<?php
/**
 * Table Definition for Items_Platforms
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
 * Table Definition for Items_Platforms
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsPlatforms extends Gateway
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('Items_Platforms');
    }

    /**
     * Get items for the specified platform.
     *
     * @var int $platformID Platform ID
     */
    public function getItemsForPlatform($platformID)
    {
        $callback = function ($select) use ($platformID) {
            $select->join(
                array('i' => 'Items'),
                'Items_Platforms.Item_ID = i.Item_ID'
            );
            $select->join(
                array('iis' => 'Items_In_Series'),
                'i.Item_ID = iis.Item_ID'
            );
            $select->join(
                array('s' => 'Series'),
                'iis.Series_ID = s.Series_ID'
            );
            $select->order(
                array('Series_Name', 'iis.Position', 'Item_Name')
            );
            $select->where->equalTo('Platform_ID', $platformID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of platforms for the specified item.
     *
     * @var int $itemID Item ID
     *
     * @return mixed
     */
    public function getPlatformsForItem($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->join(
                array('p' => 'Platforms'),
                'Items_Platforms.Platform_ID = p.Platform_ID'
            );
            $select->order(array('Platform'));
            $select->where->equalTo('Item_ID', $itemID);
        };
        return $this->select($callback);
    }
}
