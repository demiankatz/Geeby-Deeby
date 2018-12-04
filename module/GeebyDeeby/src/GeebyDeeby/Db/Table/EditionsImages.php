<?php
/**
 * Table Definition for Editions_Images
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
 * Table Definition for Editions_Images
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsImages extends Gateway
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('Editions_Images');
    }

    /**
     * Get a list of images for the specified item.
     *
     * @var int $itemID Item ID
     *
     * @return mixed
     */
    public function getImagesForItem($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->join(
                array('eds' => 'Editions'),
                'Editions_Images.Edition_ID = eds.Edition_ID'
            );
            $select->join(
                array('i' => 'Items'), 'eds.Item_ID = i.Item_ID'
            );
            $select->join(
                array('n' => 'Notes'), 'Editions_Images.Note_ID = n.Note_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $select->order(array('Editions_Images.Position'));
            $select->where->equalTo('i.Item_ID', $itemID);
        };
        return $this->select($callback);
    }

    /**
     * Get image information for the specified series.
     *
     * @var int $seriesID Series ID
     *
     * @return mixed
     */
    public function getImagesForSeries($seriesID)
    {
        $callback = function ($select) use ($seriesID) {
            $select->columns(array('Thumb_Path'));
            $select->join(
                array('eds' => 'Editions'),
                'Editions_Images.Edition_ID = eds.Edition_ID'
            );
            $select->join(
                array('i' => 'Items'), 'eds.Item_ID = i.Item_ID'
            );
            $select->join(
                array('mt' => 'Material_Types'),
                'i.Material_Type_ID = mt.Material_Type_ID',
                array()
            );
            $select->join(
                array('n' => 'Notes'), 'Editions_Images.Note_ID = n.Note_ID',
                array('Note'), Select::JOIN_LEFT
            );
            $select->order(
                array(
                    'mt.Material_Type_Name', 'eds.Position',
                    'i.Item_Name', 'Editions_Images.Position'
                )
            );
            $select->group(
                array(
                    'Thumb_Path', 'eds.Position', 'Editions_Images.Position',
                    'i.Item_ID', 'Note'
                )
            );
            $select->where->equalTo('Series_ID', $seriesID);
        };
        return $this->select($callback);
    }
}
