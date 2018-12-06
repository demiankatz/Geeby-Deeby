<?php
/**
 * Table Definition for People_Links
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
 * Table Definition for People_Links
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PeopleLinks extends Gateway
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('People_Links');
    }

    /**
     * Get a list of links for the specified person.
     *
     * @var int $personID Person ID
     *
     * @return mixed
     */
    public function getLinksForPerson($personID)
    {
        $callback = function ($select) use ($personID) {
            $select->join(
                array('l' => 'Links'), 'People_Links.Link_ID = l.Link_ID'
            );
            $select->order(array('Link_Name'));
            $select->where->equalTo('Person_ID', $personID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of people for the specified link.
     *
     * @var int $linkID Link ID
     *
     * @return mixed
     */
    public function getPeopleForLink($linkID)
    {
        $callback = function ($select) use ($linkID) {
            $select->join(
                array('p' => 'People'),
                'People_Links.Person_ID = p.Person_ID'
            );
            $select->order(array('Last_Name', 'First_Name', 'Middle_Name'));
            $select->where->equalTo('Link_ID', $linkID);
        };
        return $this->select($callback);
    }
}
