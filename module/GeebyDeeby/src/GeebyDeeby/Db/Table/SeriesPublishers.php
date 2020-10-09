<?php
/**
 * Table Definition for Series_Publishers
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
use Laminas\Db\Sql\Select;

/**
 * Table Definition for Series_Publishers
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesPublishers extends Gateway
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
        parent::__construct($adapter, $tm, $rowObj, 'Series_Publishers');
    }

    /**
     * Get a list of series for the specified city.
     *
     * @param int $cityID City ID
     *
     * @return mixed
     */
    public function getSeriesForCity($cityID)
    {
        $callback = function ($select) use ($cityID) {
            $select->join(
                ['s' => 'Series'],
                'Series_Publishers.Series_ID = s.Series_ID'
            );
            $select->join(
                ['pa' => 'Publishers_Addresses'],
                'Series_Publishers.Address_ID = pa.Address_ID'
            );
            $select->order('s.Series_Name');
            $select->group('s.Series_ID');
            $select->where->equalTo('pa.City_ID', $cityID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of series for the specified country.
     *
     * @param int $countryID Country ID
     *
     * @return mixed
     */
    public function getSeriesForCountry($countryID)
    {
        $callback = function ($select) use ($countryID) {
            $select->join(
                ['s' => 'Series'],
                'Series_Publishers.Series_ID = s.Series_ID'
            );
            $select->join(
                ['pa' => 'Publishers_Addresses'],
                'Series_Publishers.Address_ID = pa.Address_ID'
            );
            $select->order('s.Series_Name');
            $select->group('s.Series_ID');
            $select->where->equalTo('pa.Country_ID', $countryID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of series for the specified publisher.
     *
     * @param int $publisherID Publisher ID
     *
     * @return mixed
     */
    public function getSeriesForPublisher($publisherID)
    {
        $callback = function ($select) use ($publisherID) {
            $select->join(
                ['s' => 'Series'],
                'Series_Publishers.Series_ID = s.Series_ID'
            );
            $select->order('s.Series_Name');
            $select->group('s.Series_ID');
            $select->where->equalTo('Publisher_ID', $publisherID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of publishers for the specified series.
     *
     * @param int $seriesID Series ID
     *
     * @return mixed
     */
    public function getPublishers($seriesID)
    {
        $callback = function ($select) use ($seriesID) {
            $select->join(
                ['p' => 'Publishers'],
                'Series_Publishers.Publisher_ID = p.Publisher_ID'
            );
            $select->join(
                ['pa' => 'Publishers_Addresses'],
                'Series_Publishers.Address_ID = pa.Address_ID',
                ['Street'], Select::JOIN_LEFT
            );
            $select->join(
                ['pi' => 'Publishers_Imprints'],
                'Series_Publishers.Imprint_ID = pi.Imprint_ID',
                ['Imprint_Name'], Select::JOIN_LEFT
            );
            $select->join(
                ['c' => 'Countries'], 'pa.Country_ID = c.Country_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $select->join(
                ['ci' => 'Cities'], 'pa.City_ID = ci.City_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $select->join(
                ['n' => 'Notes'],
                'Series_Publishers.Note_ID = n.Note_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $select->order('p.Publisher_Name');
            $select->where->equalTo('Series_ID', $seriesID);
        };
        return $this->select($callback);
    }
}
