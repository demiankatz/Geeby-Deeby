<?php
/**
 * Table Definition for Tags_Attributes_Values
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2018.
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
 * Table Definition for Tags_Attributes_Values
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class TagsAttributesValues extends Gateway
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('Tags_Attributes_Values');
    }

    /**
     * Get a list of attributes for the specified tag.
     *
     * @var int $tagID Tag ID
     *
     * @return mixed
     */
    public function getAttributesForTag($tagID)
    {
        $callback = function ($select) use ($tagID) {
            $select->join(
                array('ta' => 'Tags_Attributes'),
                'ta.Tags_Attribute_ID = '
                . 'Tags_Attributes_Values.Tags_Attribute_ID'
            );
            $select->order(array('ta.Display_Priority', 'ta.Tags_Attribute_Name'));
            $select->where->equalTo('Tag_ID', $tagID);
        };
        return $this->select($callback);
    }
}
