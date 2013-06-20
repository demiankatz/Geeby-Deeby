<?php
/**
 * Migration controller
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
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\Controller;
use Zend\Db\Adapter\Exception\InvalidQueryException;

/**
 * Migration controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class MigrateController extends AbstractBase
{
    /**
     * Main approval action
     *
     * @return mixed
     */
    public function indexAction()
    {
        $ok = $this->checkPermission('Data_Manager');
        if ($ok !== true) {
            return $ok;
        }
        $messages = array();
        $migrations = array(
            'migrateItemsInSeriesToEditions' =>
                'rows from Items_In_Series.',
            'migrateItemDatesToEditions' =>
                'release dates from Items_Release_Dates.',
            'migrateItemCreditsToEditions' =>
                'credits from Items_Credits.',
            'migrateItemLengthAndEndingsToEditions' =>
                'length/ending values from Items.',
            'migrateItemISBNsToEditions' =>
                'ISBNs from Items_ISBNs',
            'migrateItemProductCodesToEditions' =>
                'product codes from Items_Product_Codes',
        );
        foreach ($migrations as $method => $msg) {
            try {
                $migrated = call_user_func(array($this, $method));
            } catch (InvalidQueryException $e) {
                // Table no longer exists -- no migration necessary
                $migrated = 0;
            }
            if ($migrated > 0) {
                $messages[] = 'Migrated ' . $migrated . ' ' . $msg;
            }
        }
        return $this->createViewModel(array('messages' => $messages));
    }

    /**
     * Migrate Items_In_Series to Editions
     *
     * @return int Number of rows migrated
     */
    protected function migrateItemsInSeriesToEditions()
    {
        $iis = $this->getDbTable('itemsinseries');
        $eds = $this->getDbTable('edition');
        $count = 0;
        foreach ($iis->getAll() as $current) {
            $row = $eds->createRow();
            $row->Edition_Name = $this->getServiceLocator()
                ->get('GeebyDeeby\Articles')
                ->articleAwareAppend($current->Series_Name, ' edition');
            $row->Item_ID = $current->Item_ID;
            $row->Series_ID = $current->Series_ID;
            $row->Position = $current->Position;
            $row->save();
            $iis->delete(
                array(
                    'Item_ID' => $current->Item_ID,
                    'Series_ID' => $current->Series_ID,
                    'Position' => $current->Position
                )
            );
            $count++;
        }
        return $count;
    }

    /**
     * Generic method for migrating from an item table to an edition table.
     *
     * @param string $inTable    Name of input table service
     * @param string $outTable   Name of output table service
     * @param string $primaryKey Name of primary key of input table (null to ignore)
     *
     * @return int Number of rows migrated
     */
    protected function genericItemToEditionMigration($inTable, $outTable,
        $primaryKey = null
    ) {
        $in = $this->getDbTable($inTable);
        $out = $this->getDbTable($outTable);
        $eds = $this->getDbTable('edition');
        $count = 0;
        foreach ($in->select() as $current) {
            $current = (array)$current;
            $currentEds = $eds->getEditionsForItem($current['Item_ID']);
            foreach ($currentEds as $currentEd) {
                unset($current['Item_ID']);
                if (null !== $primaryKey) {
                    unset($current[$primaryKey]);
                }
                $current['Edition_ID'] = $currentEd->Edition_ID;
                $out->insert($current);
            }
            unset($current['Edition_ID']);
            $in->delete($current);
            $count++;
        }
        return $count;
    }

    /**
     * Migrate Items_Credits to Editions_Credits.
     *
     * @return int Number of rows migrated
     */
    protected function migrateItemCreditsToEditions()
    {
        return $this->genericItemToEditionMigration(
            'itemscredits', 'editionscredits'
        );
    }

    /**
     * Migrate Items_Release_Dates to Editions_Release_Dates.
     *
     * @return int Number of rows migrated
     */
    protected function migrateItemDatesToEditions()
    {
        return $this->genericItemToEditionMigration(
            'itemsreleasedates', 'editionsreleasedates'
        );
    }

    /**
     * Migrate key Items fields to Editions.
     *
     * @return int Number of rows migrated
     */
    protected function migrateItemLengthAndEndingsToEditions()
    {
        $i = $this->getDbTable('item');
        $e = $this->getDbTable('edition');
        $count = 0;
        foreach ($i->getList() as $current) {
            if (!empty($current->Item_Length) || !empty($current->Item_Endings)) {
                $currentEds = $e->getEditionsForItem($current['Item_ID']);
                foreach ($currentEds as $currentEd) {
                    $currentEd->Edition_Length = $current->Item_Length;
                    $currentEd->Edition_Endings = $current->Item_Endings;
                    $currentEd->save();
                }
                $current->Item_Length = null;
                $current->Item_Endings = null;
                $current->save();
                $count++;
            }
        }
        return $count;
    }

    /**
     * Migrate Item ISBNs to Editions.
     *
     * @return int Number of rows migrated
     */
    protected function migrateItemISBNsToEditions()
    {
        return $this->genericItemToEditionMigration(
            'itemsisbns', 'editionsisbns', 'Sequence_ID'
        );
    }

    /**
     * Migrate Item product codes to Editions.
     *
     * @return int Number of rows migrated
     */
    protected function migrateItemProductCodesToEditions()
    {
        return $this->genericItemToEditionMigration(
            'itemsproductcodes', 'editionsproductcodes', 'Sequence_ID'
        );
    }
}
