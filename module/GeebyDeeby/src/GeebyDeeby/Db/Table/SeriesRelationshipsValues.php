<?php
/**
 * Table Definition for Series_Relationships_Values
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2019.
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

/**
 * Table Definition for Series_Relationships_Values
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesRelationshipsValues extends Gateway
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
        parent::__construct($adapter, $tm, $rowObj, 'Series_Relationships_Values');
    }

    /**
     * Get a list of series related to the provided subject series ID.
     *
     * @var int $seriesID Series ID
     *
     * @return mixed
     */
    public function getSeriesRelatedtoObjectSeries($seriesID)
    {
        $callback = function ($select) use ($seriesID) {
            $select->join(
                ['s' => 'Series'],
                'Series_Relationships_Values.Subject_Series_ID = s.Series_ID'
            );
            $select->where(['Object_Series_ID' => $seriesID]);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of series related to the provided subject series ID.
     *
     * @var int $seriesID Series ID
     *
     * @return mixed
     */
    public function getSeriesRelatedtoSubjectSeries($seriesID)
    {
        $callback = function ($select) use ($seriesID) {
            $select->join(
                ['s' => 'Series'],
                'Series_Relationships_Values.Object_Series_ID = s.Series_ID'
            );
            $select->where(['Subject_Series_ID' => $seriesID]);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of relationships for the specified series.
     *
     * @var int $seriesID Series ID
     *
     * @return mixed
     */
    public function getRelationshipsForSeries($seriesID)
    {
        // Collect forward and inverse relationships in an index:
        $index = [];
        $subjectList = $this->getSeriesRelatedtoSubjectSeries($seriesID);
        foreach ($subjectList->toArray() as $current) {
            $index[$current['Series_Relationship_ID']][] = $current;
        }
        $objectList = $this->getSeriesRelatedtoObjectSeries($seriesID);
        foreach ($objectList->toArray() as $current) {
            $index['i' . $current['Series_Relationship_ID']][] = $current;
        }

        // Look up all options on the option list in the index to build return value:
        $retVal = [];
        $optionList = $this->getDbTable('seriesrelationship')->getOptionList(true);
        foreach ($optionList as $id => $relationship) {
            if (isset($index[$id])) {
                $retVal[] = $relationship + [
                    'relationship_id' => $id,
                    'values' => $index[$id],
                ];
            }
        }
        return $retVal;
    }
}
