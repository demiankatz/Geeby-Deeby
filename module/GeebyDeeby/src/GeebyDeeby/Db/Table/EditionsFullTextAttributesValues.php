<?php
/**
 * Table Definition for Editions_Full_Text_Attributes_Values
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2020.
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
 * Table Definition for Editions_Full_Text_Attributes_Values
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsFullTextAttributesValues extends Gateway
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
        parent::__construct(
            $adapter, $tm, $rowObj, 'Editions_Full_Text_Attributes_Values'
        );
    }

    /**
     * Get a list of attributes for the specified full text ID(s).
     *
     * @param int[] $fullTextID Full text ID
     *
     * @return mixed
     */
    public function getAttributesForFullTextIDs($fullTextID)
    {
        $callback = function ($select) use ($fullTextID) {
            $select->join(
                ['efta' => 'Editions_Full_Text_Attributes'],
                'efta.Editions_Full_Text_Attribute_ID = '
                . 'Editions_Full_Text_Attributes_Values.'
                . 'Editions_Full_Text_Attribute_ID'
            );
            $select->order(
                ['efta.Display_Priority', 'efta.Editions_Full_Text_Attribute_Name']
            );
            $select->where->in('Editions_Full_Text_ID', (array)$fullTextID);
        };
        return $this->select($callback);
    }
}
