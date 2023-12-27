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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Controller;

use Laminas\Crypt\Password\Bcrypt;
use Laminas\Db\Adapter\Exception\InvalidQueryException;

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
        $messages = [];
        $migrations = [
            'migrateItemsInSeriesToEditions' =>
                'rows from Items_In_Series.',
            'migrateItemDatesToEditions' =>
                'release dates from Items_Release_Dates.',
            'migrateItemCreditsToEditions' =>
                'credits from Items_Credits.',
            'migrateItemLengthAndEndingsToEditions' =>
                'length/ending values from Items.',
            'migrateItemImagesToEditions' =>
                'Images from Items_Images',
            'migrateItemISBNsToEditions' =>
                'ISBNs from Items_ISBNs',
            'migrateItemProductCodesToEditions' =>
                'product codes from Items_Product_Codes',
            'migrateItemPlatformsToEditions' =>
                'platforms from Items_Platforms',
            'migratePublisherImprints' =>
                'imprints from Series_Publishers',
            'migratePublisherCountries' =>
                'countries from Series_Publishers',
            'migrateUserPasswords' =>
                'user password hashes',
        ];
        foreach ($migrations as $method => $msg) {
            try {
                $migrated = call_user_func([$this, $method]);
            } catch (InvalidQueryException $e) {
                // Table no longer exists -- no migration necessary
                $migrated = 0;
            }
            if ($migrated > 0) {
                $messages[] = 'Migrated ' . $migrated . ' ' . $msg;
            }
        }
        return $this->createViewModel(['messages' => $messages]);
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
            $row->Edition_Name = $this->serviceLocator
                ->get('GeebyDeeby\Articles')
                ->articleAwareAppend($current->Series_Name, ' edition');
            $row->Item_ID = $current->Item_ID;
            $row->Series_ID = $current->Series_ID;
            $row->Position = $current->Position;
            $row->save();
            $iis->delete(
                [
                    'Item_ID' => $current->Item_ID,
                    'Series_ID' => $current->Series_ID,
                    'Position' => $current->Position,
                ]
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
    protected function genericItemToEditionMigration(
        $inTable,
        $outTable,
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
            'itemscredits',
            'editionscredits'
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
            'itemsreleasedates',
            'editionsreleasedates'
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
            'itemsisbns',
            'editionsisbns',
            'Sequence_ID'
        );
    }

    /**
     * Migrate Item images to Editions.
     *
     * @return int Number of rows migrated
     */
    protected function migrateItemImagesToEditions()
    {
        return $this->genericItemToEditionMigration(
            'itemsimages',
            'editionsimages',
            'Sequence_ID'
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
            'itemsproductcodes',
            'editionsproductcodes',
            'Sequence_ID'
        );
    }

    /**
     * Migrate Item platforms to Editions.
     *
     * @return int Number of rows migrated
     */
    protected function migrateItemPlatformsToEditions()
    {
        return $this->genericItemToEditionMigration(
            'itemsplatforms',
            'editionsplatforms'
        );
    }

    /**
     * Migrate imprints from Series_Publishers.
     *
     * @return int Number of rows migrated
     */
    protected function migratePublisherImprints()
    {
        $sp = $this->getDbTable('seriespublishers');
        $count = 0;
        foreach ($sp->select() as $current) {
            if (!isset($current->Imprint) || empty($current->Imprint)) {
                continue;
            }
            $current->Imprint_ID = $this->getImprintID($current);
            $current->Imprint = null;
            $current->save();
            $count++;
        }
        return $count;
    }

    /**
     * Support method for migratePublisherImprints -- create or retrieve imprint ID
     * for provided Series_Publishers row.
     *
     * @param \GeebyDeeby\Db\Row\SeriesPublishers $current Series_Publishers row
     *
     * @return string
     */
    protected function getImprintID($current)
    {
        $pi = $this->getDbTable('publishersimprints');
        $imprint = [
            'Publisher_ID' => $current->Publisher_ID,
            'Imprint_Name' => $current->Imprint,
        ];
        $row = $pi->select($imprint)->current();
        if (isset($row['Imprint_ID'])) {
            return $row['Imprint_ID'];
        }

        // No row found -- create one!
        $pi->insert($imprint);
        return $this->getImprintID($current);
    }

    /**
     * Migrate countries from Series_Publishers.
     *
     * @return int Number of rows migrated
     */
    protected function migratePublisherCountries()
    {
        $sp = $this->getDbTable('seriespublishers');
        $count = 0;
        foreach ($sp->select() as $current) {
            if (!isset($current->Country_ID) || empty($current->Country_ID)) {
                continue;
            }
            $current->Address_ID = $this->getAddressID($current);
            $current->Country_ID = 0;
            $current->save();
            $count++;
        }
        return $count;
    }

    /**
     * Support method for migratePublisherCountries -- create or retrieve country ID
     * for provided Series_Publishers row.
     *
     * @param \GeebyDeeby\Db\Row\SeriesPublishers $current Series_Publishers row
     *
     * @return string
     */
    protected function getAddressID($current)
    {
        $pi = $this->getDbTable('publishersaddresses');
        $address = [
            'Publisher_ID' => $current->Publisher_ID,
            'Country_ID' => $current->Country_ID,
        ];
        $row = $pi->select($address)->current();
        if (isset($row['Address_ID'])) {
            return $row['Address_ID'];
        }

        // No row found -- create one!
        $pi->insert($address);
        return $this->getAddressID($current);
    }

    /**
     * Migrate user passwords
     *
     * @return int Number of rows migrated
     */
    protected function migrateUserPasswords()
    {
        $users = $this->getDbTable('user');
        $bcrypt = new Bcrypt();
        $count = 0;
        foreach ($users->select(['Password_Hash' => '']) as $user) {
            $user->Password_Hash = $bcrypt->create($user->Password);
            $user->save();
            $count++;
        }
        return $count;
    }
}
