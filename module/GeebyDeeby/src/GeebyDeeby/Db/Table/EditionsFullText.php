<?php
/**
 * Table Definition for Editions_Full_Text
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
 * Table Definition for Editions_Full_Text
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsFullText extends Gateway
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('Editions_Full_Text');
    }

    /**
     * Get a list of full text links for a particular edition.
     *
     * @param int $edition Edition ID
     *
     * @return mixed
     */
    public function getFullTextForEdition($edition)
    {
        $callback = function ($select) use ($edition) {
            $select->join(
                array('fts' => 'Full_Text_Sources'),
                'Editions_Full_Text.Full_Text_Source_ID = fts.Full_Text_Source_ID'
            );
            $fields = array('Full_Text_Source_Name', 'Full_Text_URL');
            $select->order($fields);
            $select->where->equalTo('Edition_ID', $edition);
        };
        return $this->select($callback);
    }
}
