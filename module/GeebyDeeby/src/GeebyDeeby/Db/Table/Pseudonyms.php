<?php
/**
 * Table Definition for Pseudonyms
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
 * Table Definition for Pseudonyms
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class Pseudonyms extends Gateway
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('Pseudonyms');
    }

    /**
     * Get a list of pseudonyms for a real name.
     *
     * @param int $personID Real person ID
     *
     * @return mixed
     */
    public function getPseudonyms($personID)
    {
        $callback = function ($select) use ($personID) {
            $select->join(
                array('p' => 'People'),
                'Pseudonyms.Pseudo_Person_ID = p.Person_ID'
            );
            $select->order(array('Last_Name', 'First_Name', 'Middle_Name'));
            $select->where->equalTo('Real_Person_ID', $personID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of real names for a pseudonym.
     *
     * @param int $personID Pseudonym ID
     *
     * @return mixed
     */
    public function getRealNames($personID)
    {
        $callback = function ($select) use ($personID) {
            $select->join(
                array('p' => 'People'),
                'Pseudonyms.Real_Person_ID = p.Person_ID'
            );
            $select->order(array('Last_Name', 'First_Name', 'Middle_Name'));
            $select->where->equalTo('Pseudo_Person_ID', $personID);
        };
        return $this->select($callback);
    }

    /**
     * Get a batch of real name information keyed by ID.
     *
     * @param Iterable $people Collection of people to look up
     *
     * @return array
     */
    public function getRealNamesBatch($people)
    {
        $retVal = array();
        foreach ($people as $person) {
            $id = $person['Person_ID'];
            if (!isset($retVal[$id])) {
                $retVal[$id] = $this->getRealNames($id)->toArray();
            }
        }
        return $retVal;
    }
}
