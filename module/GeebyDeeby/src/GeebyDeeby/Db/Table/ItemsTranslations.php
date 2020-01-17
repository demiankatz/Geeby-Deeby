<?php
/**
 * Table Definition for Items_Translations
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

use Zend\Db\Adapter\Adapter;
use Zend\Db\RowGateway\RowGateway;
use Zend\Db\Sql\Expression;

/**
 * Table Definition for Items_Translations
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsTranslations extends Gateway
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
        parent::__construct($adapter, $tm, $rowObj, 'Items_Translations');
    }

    /**
     * Support method to add language information to a query.
     *
     * @param \Zend\Db\Sql\Select $select Query to modify
     *
     * @return void
     */
    public static function addLanguageToSelect($select)
    {
        $select->join(
            ['eds' => 'Editions'], 'eds.Item_ID = i.Item_ID', []
        );
        $select->join(
            ['s' => 'Series'], 's.Series_ID = eds.Series_ID', []
        );
        $select->join(
            ['l' => 'Languages'], 'l.Language_ID = s.Language_ID',
            [
                'Language_Name' => new Expression(
                    'min(?)', ['Language_Name'],
                    [Expression::TYPE_IDENTIFIER]
                )
            ]
        );
        $select->group('i.Item_ID');
    }

    /**
     * Get a list of items translated from the specified item.
     *
     * @var int  $itemID      Item ID
     * @var bool $includeLang Should we also load language information?
     *
     * @return mixed
     */
    public function getTranslatedFrom($itemID, $includeLang = false)
    {
        $callback = function ($select) use ($itemID, $includeLang) {
            $select->join(
                ['i' => 'Items'],
                'Items_Translations.Trans_Item_ID = i.Item_ID'
            );
            $select->where->equalTo('Source_Item_ID', $itemID);
            $select->order('i.Item_Name');
            if ($includeLang) {
                ItemsTranslations::addLanguageToSelect($select);
            }
        };
        return $this->select($callback);
    }

    /**
     * Get a list of items translated into the specified item.
     *
     * @var int  $itemID      Item ID
     * @var bool $includeLang Should we also load language information?
     *
     * @return mixed
     */
    public function getTranslatedInto($itemID, $includeLang = false)
    {
        $callback = function ($select) use ($itemID, $includeLang) {
            $select->join(
                ['i' => 'Items'],
                'Items_Translations.Source_Item_ID = i.Item_ID'
            );
            $select->where->equalTo('Trans_Item_ID', $itemID);
            $select->order('i.Item_Name');
            if ($includeLang) {
                ItemsTranslations::addLanguageToSelect($select);
            }
        };
        return $this->select($callback);
    }
}
