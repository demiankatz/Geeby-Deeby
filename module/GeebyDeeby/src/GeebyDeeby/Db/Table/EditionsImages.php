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
use Zend\Db\Sql\Expression, Zend\Db\Sql\Select;

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
     * Get a list of thumbnails attached to multiple editions.
     *
     * @return mixed
     */
    public function getDuplicateThumbs()
    {
        $callback = function ($select) {
            $count = new Expression(
                'count(?)', array('Thumb_Path'), array(Expression::TYPE_IDENTIFIER)
            );
            $select->columns(array('Thumb_Path', 'c' => $count));
            $select->group(array('Thumb_Path'));
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
                array('eds' => 'Editions'),
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
                array('n' => 'Notes'), 'Editions_Images.Note_ID = n.Note_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $select->order(array('Editions_Images.Position'));
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
                array('n' => 'Notes'), 'Editions_Images.Note_ID = n.Note_ID',
                ['Note'], Select::JOIN_LEFT
            );
            $select->join(
                array('eds' => 'Editions'),
                'Editions_Images.Edition_ID = eds.Edition_ID'
                . ' OR eds.Parent_Edition_ID = Editions_Images.Edition_ID',
                ['Edition_ID']
            );
            $select->order(array('Editions_Images.Position'));
            $select->where->equalTo('eds.Edition_ID', $editionID);
        };
        return $this->select($callback);
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
            $select->quantifier('DISTINCT');
            $select->join(
                array('eds' => 'Editions'),
                'Editions_Images.Edition_ID = eds.Edition_ID'
                . ' OR eds.Parent_Edition_ID = Editions_Images.Edition_ID',
                ['Edition_Name']
            );
            $select->join(
                array('i' => 'Items'), 'eds.Item_ID = i.Item_ID', ['Item_ID']
            );
            $select->join(
                array('n' => 'Notes'), 'Editions_Images.Note_ID = n.Note_ID',
                ['Note'], Select::JOIN_LEFT
            );
            $select->order(['Position']);
            $select->where->equalTo('i.Item_ID', $itemID);
        };
        return $this->select($callback);
    }

    /**
     * Get image information for the specified series.
     *
     * @var int  $seriesID        Series ID
     * @var bool $groupByMaterial Should we group results by material type?
     *
     * @return mixed
     */
    public function getImagesForSeries($seriesID, $groupByMaterial = true)
    {
        $callback = function ($select) use ($seriesID, $groupByMaterial) {
            $select->columns(array('Thumb_Path', 'IIIF_URI'));
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
                $groupByMaterial
                    ? array('mt.Material_Type_Name', 'eds.Volume', 'eds.Position', 'eds.Replacement_Number', 'i.Item_Name', 'Editions_Images.Position')
                    : array('eds.Volume', 'eds.Position', 'eds.Replacement_Number', 'i.Item_Name', 'Editions_Images.Position')
            );
            $select->group(
                array(
                    'Thumb_Path', 'IIIF_URI', 'eds.Volume', 'eds.Position', 'eds.Replacement_Number', 'Editions_Images.Position',
                    'i.Item_ID', 'Note'
                )
            );
            $select->where->equalTo('Series_ID', $seriesID);
        };
        return $this->select($callback);
    }
}
