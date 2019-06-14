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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\Db\Table;
use Zend\Db\Sql\Expression;
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

    /**
     * Get a list of full text links for a particular edition (or its immediate
     * parent).
     *
     * @param int $edition Edition ID
     *
     * @return mixed
     */
    public function getFullTextForEditionOrParentEdition($edition)
    {
        $callback = function ($select) use ($edition) {
            $select->quantifier('DISTINCT');
            $select->columns(['Full_Text_URL']);
            $select->join(
                array('fts' => 'Full_Text_Sources'),
                'Editions_Full_Text.Full_Text_Source_ID = fts.Full_Text_Source_ID',
                ['Full_Text_Source_Name']
            );
            $select->join(
                array('eds' => 'Editions'),
                'Editions_Full_Text.Edition_ID = eds.Edition_ID'
                . ' OR eds.Parent_Edition_ID = Editions_Full_Text.Edition_ID',
                ['Edition_ID']
            );
            $fields = array('Full_Text_Source_Name', 'Full_Text_URL');
            $select->order($fields);
            $select->where->equalTo('eds.Edition_ID', $edition);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of full text links for a particular item.
     *
     * @param int $item Item ID
     *
     * @return mixed
     */
    public function getFullTextForItem($item)
    {
        $callback = function ($select) use ($item) {
            $select->quantifier('DISTINCT');
            $select->columns(['Full_Text_URL']);
            $select->join(
                array('fts' => 'Full_Text_Sources'),
                'Editions_Full_Text.Full_Text_Source_ID = fts.Full_Text_Source_ID',
                ['Full_Text_Source_Name']
            );
            $select->join(
                array('eds' => 'Editions'),
                'Editions_Full_Text.Edition_ID = eds.Edition_ID'
                . ' OR eds.Parent_Edition_ID = Editions_Full_Text.Edition_ID',
                ['Edition_Name']
            );
            $select->join(array('i' => 'Items'), 'eds.Item_ID = i.Item_ID');
            $fields
                = array('Full_Text_Source_Name', 'Edition_Name', 'Full_Text_URL');
            $select->order($fields);
            $select->where->equalTo('i.Item_ID', $item);
        };
        return $this->select($callback);
    }

    /**
     * Get a list of items with full text.
     *
     * @param int  $series Series ID (optional limiter)
     * @param bool $fuzzy  Use fuzzy matching? (optional)
     * @param int  $source Full text source ID (optional limiter)
     *
     * @return mixed
     */
    public function getItemsWithFullText($series = null, $fuzzy = false,
        $source = null
    ) {
        $callback = function ($select) use ($series, $fuzzy, $source) {
            if ($fuzzy) {
                $select->join(
                    array('eds2' => 'Editions'),
                    'Editions_Full_Text.Edition_ID = eds2.Edition_ID'
                );
                $select->join(
                    array('eds' => 'Editions'), 'eds2.Item_ID = eds.Item_ID'
                );
            } else {
                $select->join(
                    array('eds' => 'Editions'),
                    'Editions_Full_Text.Edition_ID = eds.Edition_ID'
                );
            }
            $select->join(
                array('i' => 'Items'), 'eds.Item_ID = i.Item_ID'
            );
            $select->join(
                array('iat' => 'Items_AltTitles'),
                'eds.Preferred_Item_AltName_ID = iat.Sequence_ID',
                array('Item_AltName'), Select::JOIN_LEFT
            );
            $select->join(
                array('s' => 'Series'), 'eds.Series_ID = s.Series_ID'
            );
            $select->join(
                array('childEds' => 'Editions'),
                'eds.Edition_ID = childEds.Parent_Edition_ID',
                array(),
                Select::JOIN_LEFT
            );
            $select->join(
                array('childItems' => 'Items'),
                'childEds.Item_ID = childItems.Item_ID',
                array(
                    'Child_Items' => new Expression(
                        'GROUP_CONCAT('
                            . 'COALESCE(?, ?) ORDER BY ? SEPARATOR \'||\')',
                        array(
                            'childIat.Item_AltName', 'childItems.Item_Name',
                            'childEds.Position_In_Parent'),
                        array(
                            Expression::TYPE_IDENTIFIER,
                            Expression::TYPE_IDENTIFIER,
                            Expression::TYPE_IDENTIFIER
                        )
                    )
                ),
                Select::JOIN_LEFT
            );
            $select->join(
                array('childIat' => 'Items_AltTitles'),
                'childEds.Preferred_Item_AltName_ID = childIat.Sequence_ID',
                array(), Select::JOIN_LEFT
            );
            if (null !== $series) {
                $select->where->equalTo('eds.Series_ID', $series);
            }
            if (null !== $source) {
                $select->where
                    ->equalTo('Editions_Full_Text.Full_Text_Source_ID', $source);
            }
            $select->group(
                array(
                    'eds.Item_ID', 'eds.Series_ID', 'eds.Volume', 'eds.Position',
                    'eds.Replacement_Number'
                )
            );
            $ord = array(
                'Series_Name', 's.Series_ID', 'eds.Volume', 'eds.Position',
                'eds.Replacement_Number', 'Item_Name'
            );
            $select->order($ord);
        };
        return $this->select($callback);
    }
}
