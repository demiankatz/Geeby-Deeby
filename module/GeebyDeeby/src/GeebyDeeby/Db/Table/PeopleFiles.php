<?php
/**
 * Table Definition for People_Files
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
 * Table Definition for People_Files
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PeopleFiles extends Gateway
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('People_Files');
    }

    /**
     * Get a list of people for the specified file.
     *
     * @var int $fileID File ID
     *
     * @return mixed
     */
    public function getPeopleForFile($fileID)
    {
        $callback = function ($select) use ($fileID) {
            $select->join(
                array('p' => 'People'),
                'People_Files.Person_ID = p.Person_ID'
            );
            $select->order(array('Last_Name', 'First_Name', 'Middle_Name'));
            $select->where->equalTo('File_ID', $fileID);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of files for the specified person.
     *
     * @var int $personID Person ID
     *
     * @return mixed
     */
    public function getFilesForPerson($personID)
    {
        $callback = function ($select) use ($personID) {
            $select->join(
                array('f' => 'Files'),
                'People_Files.File_ID = f.File_ID'
            );
            $select->join(
                array('ft' => 'File_Types'),
                'f.File_Type_ID = ft.File_Type_ID'
            );
            $select->order(array('File_Type', 'File_Name'));
            $select->where->equalTo('Person_ID', $personID);
        };
        return $this->select($callback);
    }
}
