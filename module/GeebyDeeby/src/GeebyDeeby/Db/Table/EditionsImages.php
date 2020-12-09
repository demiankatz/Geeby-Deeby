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
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;

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
     *
     * @param Adapter       $adapter Database adapter
     * @param PluginManager $tm      Table manager
     * @param RowGateway    $rowObj  Row prototype object (null for default)
     */
    public function __construct(Adapter $adapter, PluginManager $tm,
        RowGateway $rowObj = null
    ) {
        parent::__construct($adapter, $tm, $rowObj, 'Editions_Images');
    }

    /**
     * Get a list of thumbnails attached to multiple editions.
     *
     * @return mixed
     */
    public function getDuplicateThumbs()
    {
        $callback = function ($select) {
            $count = new Expression(
                'count(?)', ['Thumb_Path'], [Expression::TYPE_IDENTIFIER]
            );
            $select->columns(['Thumb_Path', 'c' => $count]);
            $select->group(['Thumb_Path']);
            $select->having('c > 1');
        };
        return $this->select($callback);
    }

    /**
     * Get edition information matching a particular thumbnail path.
     *
     * @param string $thumb Thumb path
     *
     * @return mixed
     */
    public function getEditionsForThumb($thumb)
    {
        $callback = function ($select) use ($thumb) {
            $select->join(
                ['eds' => 'Editions'],
                'Editions_Images.Edition_ID = eds.Edition_ID'
            );
            $select->where->equalTo('Thumb_Path', $thumb);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of images for the specified edition.
     *
     * @param int $editionID Edition ID
     *
     * @return mixed
     */
    public function getImagesForEdition($editionID)
    {
        $callback = function ($select) use ($editionID) {
            $select->join(
                ['n' => 'Notes'], 'Editions_Images.Note_ID = n.Note_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $select->order(['Editions_Images.Position']);
            $select->where->equalTo('Edition_ID', $editionID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of images for the specified edition (or its immediate parent).
     *
     * @param int $editionID Edition ID
     *
     * @return mixed
     */
    public function getImagesForEditionOrParentEdition($editionID)
    {
        $callback = function ($select) use ($editionID) {
            $select->quantifier('DISTINCT');
            $select->join(
                ['n' => 'Notes'], 'Editions_Images.Note_ID = n.Note_ID',
                ['Note'], Select::JOIN_LEFT
            );
            $select->join(
                ['eds' => 'Editions'],
                'Editions_Images.Edition_ID = eds.Edition_ID'
                . ' OR eds.Parent_Edition_ID = Editions_Images.Edition_ID',
                ['Edition_ID']
            );
            $select->order(['Editions_Images.Position']);
            $select->where->equalTo('eds.Edition_ID', $editionID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of images for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return mixed
     */
    public function getImagesForItem($itemID)
    {
        $callback = function ($select) use ($itemID) {
            $select->quantifier('DISTINCT');
            $fields = [
                'Edition_ID',
                'Image_Path',
                'Thumb_Path',
                'IIIF_URI',
                'Position',
                'Note_ID',
            ];
            $select->columns($fields);
            $select->join(
                ['eds' => 'Editions'],
                'Editions_Images.Edition_ID = eds.Edition_ID'
                . ' OR eds.Parent_Edition_ID = Editions_Images.Edition_ID',
                ['Edition_Name']
            );
            $select->join(
                ['i' => 'Items'], 'eds.Item_ID = i.Item_ID', ['Item_ID']
            );
            $year = new Expression(
                'min(?)', ['erd.Year'],
                [Expression::TYPE_IDENTIFIER]
            );
            $select->join(
                ['erd' => 'Editions_Release_Dates'],
                'eds.Edition_ID = erd.Edition_ID '
                . 'OR eds.Parent_Edition_ID = erd.Edition_ID',
                ['Earliest_Year' => $year], Select::JOIN_LEFT
            );
            $select->join(
                ['n' => 'Notes'], 'Editions_Images.Note_ID = n.Note_ID',
                ['Note'], Select::JOIN_LEFT
            );
            $fields = array_merge($fields, ['Edition_Name', 'Item_ID', 'Note']);
            $select->group($fields);
            $select->order(['Item_Display_Order', 'Position', 'Earliest_Year']);
            $select->where->equalTo('i.Item_ID', $itemID);
        };
        return $this->select($callback);
    }

    /**
     * Get image information for the specified series.
     *
     * @param int  $seriesID        Series ID
     * @param bool $groupByMaterial Should we group results by material type?
     *
     * @return mixed
     */
    public function getImagesForSeries($seriesID, $groupByMaterial = true)
    {
        $callback = function ($select) use ($seriesID, $groupByMaterial) {
            $select->columns(['Thumb_Path', 'IIIF_URI']);
            $select->join(
                ['eds' => 'Editions'],
                'Editions_Images.Edition_ID = eds.Edition_ID'
            );
            $select->join(
                ['i' => 'Items'], 'eds.Item_ID = i.Item_ID'
            );
            $select->join(
                ['mt' => 'Material_Types'],
                'i.Material_Type_ID = mt.Material_Type_ID',
                []
            );
            $select->join(
                ['n' => 'Notes'], 'Editions_Images.Note_ID = n.Note_ID',
                ['Note'], Select::JOIN_LEFT
            );
            $select->order(
                $groupByMaterial
                    ? [
                        'mt.Material_Type_Name', 'eds.Volume', 'eds.Position',
                        'eds.Replacement_Number', 'eds.Item_Display_Order',
                        'i.Item_Name', 'Editions_Images.Position'
                    ] : [
                        'eds.Volume', 'eds.Position', 'eds.Replacement_Number',
                        'eds.Item_Display_Order', 'i.Item_Name',
                        'Editions_Images.Position'
                    ]
            );
            $select->group(
                [
                    'Thumb_Path', 'IIIF_URI', 'eds.Volume', 'eds.Position',
                    'eds.Replacement_Number', 'Editions_Images.Position',
                    'i.Item_ID', 'Note'
                ]
            );
            $select->where->equalTo('Series_ID', $seriesID);
        };
        return $this->select($callback);
    }
}
