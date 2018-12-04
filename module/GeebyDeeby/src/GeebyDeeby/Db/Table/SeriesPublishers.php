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
     */
    public function __construct()
    {
        parent::__construct(
            'Series_Publishers', 'GeebyDeeby\Db\Row\SeriesPublishers'
        );
    }

    /**
     * Get a list of series for the specified city.
     *
     * @var int $cityID City ID
     *
     * @return mixed
     */
    public function getSeriesForCity($cityID)
    {
        $callback = function ($select) use ($cityID) {
            $select->join(
                array('s' => 'Series'),
                'Series_Publishers.Series_ID = s.Series_ID'
            );
            $select->join(
                array('pa' => 'Publishers_Addresses'),
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
     * @var int $countryID Country ID
     *
     * @return mixed
     */
    public function getSeriesForCountry($countryID)
    {
        $callback = function ($select) use ($countryID) {
            $select->join(
                array('s' => 'Series'),
                'Series_Publishers.Series_ID = s.Series_ID'
            );
            $select->join(
                array('pa' => 'Publishers_Addresses'),
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
     * @var int $publisherID Publisher ID
     *
     * @return mixed
     */
    public function getSeriesForPublisher($publisherID)
    {
        $callback = function ($select) use ($publisherID) {
            $select->join(
                array('s' => 'Series'),
                'Series_Publishers.Series_ID = s.Series_ID'
            );
            $select->order('s.Series_Name');
            $select->where->equalTo('Publisher_ID', $publisherID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of publishers for the specified series.
     *
     * @var int $seriesID Series ID
     *
     * @return mixed
     */
    public function getPublishers($seriesID)
    {
        $callback = function ($select) use ($seriesID) {
            $select->join(
                array('p' => 'Publishers'),
                'Series_Publishers.Publisher_ID = p.Publisher_ID'
            );
            $select->join(
                array('pa' => 'Publishers_Addresses'),
                'Series_Publishers.Address_ID = pa.Address_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $select->join(
                array('pi' => 'Publishers_Imprints'),
                'Series_Publishers.Imprint_ID = pi.Imprint_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $select->join(
                array('c' => 'Countries'), 'pa.Country_ID = c.Country_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $select->join(
                array('ci' => 'Cities'), 'pa.City_ID = ci.City_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $select->join(
                array('n' => 'Notes'),
                'Series_Publishers.Note_ID = n.Note_ID',
                Select::SQL_STAR, Select::JOIN_LEFT
            );
            $select->order('p.Publisher_Name');
            $select->where->equalTo('Series_ID', $seriesID);
        };
        return $this->select($callback);
    }
}
