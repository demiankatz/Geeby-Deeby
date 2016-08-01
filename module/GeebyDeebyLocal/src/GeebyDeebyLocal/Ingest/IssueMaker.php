<?php
/**
 * Class to move Works into Issues within a Series.
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
 * @package  Ingest
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeebyLocal\Ingest;
use Zend\Console\Console;

/**
 * Class to move Works into Issues within a Series.
 *
 * @category GeebyDeeby
 * @package  Ingest
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class IssueMaker
{
    // constant values drawn from dimenovels.org database:
    const MATERIALTYPE_WORK = 1;
    const MATERIALTYPE_ISSUE = 2;

    /**
     * Table plugin manager
     *
     * @var object
     */
    protected $tables;

    /**
     * Articles helper
     *
     * @var object
     */
    protected $articles;

    /**
     * Constructor
     *
     * @param object $tables Table plugin manager
     */
    public function __construct($tables, $articles)
    {
        $this->tables = $tables;
        $this->articles = $articles;
    }

    /**
     * Make issues
     *
     * @param object $seriesObj Series row object
     * @param string $prefix    Title prefix for issues
     *
     * @return bool
     */
    public function makeIssues($seriesObj, $prefix)
    {
        $works = $this->getEligibleWorks($seriesObj);
        if (count($works) == 0) {
            Console::writeLine("No eligible works.");
            return false;
        }
        foreach ($works as $currentEdition) {
            if (!$this->createIssueForWork($currentEdition, $prefix)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get a database table gateway.
     *
     * @param string $table Name of table service to pull
     *
     * @return \Zend\Db\TableGateway\AbstractTableGateway
     */
    protected function getDbTable($table)
    {
        return $this->tables->get($table);
    }

    /**
     * Create an Issue edition to wrap the provided Work edition.
     *
     * @param object $workEdition Editions row object
     * @param string $prefix      Title prefix for issues
     *
     * @return bool
     */
    protected function createIssueForWork($workEdition, $prefix)
    {
        if (!$this->workIsEligibleForConversion($workEdition)) {
            return false;
        }
        $issueItem = $this->createIssueItem($workEdition, $prefix);
        $issueEdition = $this->createIssueEdition($issueItem, $workEdition);
        return $this->transferEditionData($workEdition, $issueEdition);
    }

    /**
     * Validate a work edition before proceeding with issue conversion.
     *
     * @param object $workEdition Editions row object
     *
     * @return bool
     */
    protected function workIsEligibleForConversion($workEdition)
    {
        if ($this->issueAlreadyExists($workEdition)) {
            Console::writeLine('Duplicate issue found for edition #' . $workEdition->Edition_ID);
            return false;
        }
        if ($workEdition->Volume > 0) {
            Console::writeLine('TODO: add support for volumes > 0');
            return false;
        }
        if ($workEdition->Replacement_Number > 0) {
            Console::writeLine('TODO: add support for replacement numbers > 0');
            return false;
        }
        if (!empty($workEdition->Parent_Edition_ID)
            || !empty($workEdition->Position_In_Parent)
            || !empty($workEdition->Extent_In_Parent)
        ) {
            Console::writeLine('Unexpected parent details in edition #' . $workEdition->Edition_ID);
            return false;
        }
        return true;
    }

    /**
     * Create an Issue edition.
     *
     * @param object $issueItem   Items row object for new Issue
     * @param object $workEdition Editions row object for existing Work
     *
     * @return object
     */
    protected function createIssueEdition($issueItem, $workEdition)
    {
        $editions = $this->getDbTable('edition');
        $values = $workEdition->toArray();
        unset($values['Edition_ID']);
        unset($values['Preferred_Item_AltName_ID']);
        $values['Item_ID'] = $issueItem->Item_ID;
        $editions->insert($values);
        return $editions->getByPrimaryKey($editions->getLastInsertValue());
    }

    /**
     * Create an Issue item.
     *
     * @param object $workEdition Editions row object
     * @param string $prefix      Title prefix for issues
     *
     * @return object
     */
    protected function createIssueItem($workEdition, $prefix)
    {
        $name = $this->articles->articleAwareAppend($prefix, $workEdition->Position);
        Console::writeLine('Creating issue: ' . $name);
        $items = $this->getDbTable('item');
        $items->insert(
            [
                'Item_Name' => $name,
                'Material_Type_ID' => self::MATERIALTYPE_ISSUE,
            ]
        );
        return $items->getByPrimaryKey($items->getLastInsertValue());
    }

    /**
     * Transfer relevant data from work edition to issue edition
     *
     * @param object $workEdition  Editions row object for work
     * @param object $issueEdition Editions row object for issue
     *
     * @return bool
     */
    protected function transferEditionData($workEdition, $issueEdition)
    {
        // Attach work to issue and remove no-longer-relevant details
        $workEdition->Parent_Edition_ID = $issueEdition->Edition_ID;
        $workEdition->Position = 0;
        $workEdition->Edition_Length = '';
        $workEdition->Edition_Description = '';
        $workEdition->save();

        // Move relevant associations:
        $editionTables = [
            'Editions_Full_Text', 'Editions_Images', 'Editions_ISBNs',
            'Editions_OCLC_Numbers', 'Editions_Platforms',
            'Editions_Product_Codes', 'Editions_Release_Dates'
        ];
        foreach ($editionTables as $table) {
            $this->getDbTable($table)->update(
                ['Edition_ID' => $issueEdition->Edition_ID],
                ['Edition_ID' => $workEdition->Edition_ID]
            );
        }
        $itemTables = ['Collections'];
        foreach ($itemTables as $table) {
            $this->getDbTable($table)->update(
                ['Item_ID' => $issueEdition->Item_ID],
                ['Item_ID' => $workEdition->Item_ID]
            );
        }
        return true;
    }

    /**
     * Get Works that can be converted to Issues.
     *
     * @param object $seriesObj Series row object
     *
     * @return \Iterable
     */
    protected function getEligibleWorks($seriesObj)
    {
        $workId = self::MATERIALTYPE_WORK;
        $callback = function ($select) use ($workId, $seriesObj) {
            $select->join(['i' => 'Items'], 'i.Item_ID = Editions.Item_ID', []);
            $select->where(['Series_ID' => $seriesObj->Series_ID, 'i.Material_Type_ID' => $workId]);
            $select->where->greaterThan('Position', 0);
            $select->order('Position');
        };
        return $this->getDbTable('edition')->select($callback);
    }

    /**
     * Check if an issue matching the current work edition already exists.
     *
     * @param object $workEdition Editions row object
     *
     * @return bool
     */
    protected function issueAlreadyExists($workEdition)
    {
        $issueId = self::MATERIALTYPE_ISSUE;
        $callback = function ($select) use ($issueId, $workEdition) {
            $select->join(['i' => 'Items'], 'i.Item_ID = Editions.Item_ID', []);
            $select->where(
                [
                    'Series_ID' => $workEdition->Series_ID,
                    'i.Material_Type_ID' => $issueId,
                    'Position' => $workEdition->Position,
                    'Volume' => $workEdition->Volume,
                    'Replacement_Number' => $workEdition->Replacement_Number,
                ]
            );
        };
        $results = $this->getDbTable('edition')->select($callback);
        return count($results) > 0;
    }
}
