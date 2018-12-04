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
        set_time_limit(0);
        $ok = $this->checkPermission('Data_Manager');
        if ($ok !== true) {
            return $ok;
        }
        $messages = array();
        try {
            $migrated = $this->migrateItemsInSeriesToEditions();
        } catch (\Zend\Db\Adapter\Exception\InvalidQueryException $e) {
            // Table no longer exists -- no migration necessary
            $migrated = 0;
        }
        if ($migrated > 0) {
            $messages[] = 'Migrated ' . $migrated . ' rows from Items_In_Series.';
        }
        try {
            $migrated = $this->migrateItemDatesToEditions();
        } catch (\Zend\Db\Adapter\Exception\InvalidQueryException $e) {
            // Table no longer exists -- no migration necessary
            $migrated = 0;
        }
        if ($migrated > 0) {
            $messages[] = 'Migrated ' . $migrated
                . ' release dates from Items_Release_Dates.';
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
     * Migrate Items_Release_Dates to Editions_Release_Dates.
     *
     * @return int Number of rows migrated
     */
    protected function migrateItemDatesToEditions()
    {
        $iDates = $this->getDbTable('itemsreleasedates');
        $eDates = $this->getDbTable('editionsreleasedates');
        $eds = $this->getDbTable('edition');
        $count = 0;
        foreach ($iDates->select() as $current) {
            $current = (array)$current;
            $currentEds = $eds->getEditionsForItem($current['Item_ID']);
            foreach ($currentEds as $currentEd) {
                unset($current['Item_ID']);
                $current['Edition_ID'] = $currentEd->Edition_ID;
                $eDates->insert($current);
            }
            unset($current['Edition_ID']);
            $iDates->delete($current);
            $count++;
        }
        return $count;
    }
}
