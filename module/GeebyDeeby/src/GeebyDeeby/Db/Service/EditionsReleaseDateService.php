<?php

/**
 * Database service for the Editions_Release_Dates table.
 *
 * PHP version 8
 *
 * Copyright (C) Demian Katz 2026.
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
 * along with this program; if not, see
 * <https://www.gnu.org/licenses/>.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Db\Service;

use Doctrine\ORM\EntityManager;
use GeebyDeeby\Db\Entity\EditionEntityInterface;
use GeebyDeeby\Db\Entity\EditionsReleaseDateEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\EditionsReleaseDates;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Editions_Release_Dates table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsReleaseDateService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param EntityManager        $entityManager      Entity manager
     * @param PersistenceManager   $persistenceManager Persistence manager
     * @param EditionsReleaseDates $releaseDatesTable  EditionsReleaseDates table
     */
    public function __construct(
        EntityManager $entityManager,
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected EditionsReleaseDates $releaseDatesTable
    ) {
        parent::__construct($entityManager, $persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return EditionsReleaseDateEntityInterface
     */
    public function createEntity(): EditionsReleaseDateEntityInterface
    {
        return $this->releaseDatesTable->createRow();
    }

    /**
     * Get a list of dates for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return EditionsReleaseDateEntityInterface[]
     */
    public function getDatesForItem(int $itemID): array
    {
        $callback = function ($select) use ($itemID): void {
            $select->quantifier('DISTINCT');
            $select->columns(['Year', 'Month', 'Day', 'Note_ID']);
            $select->join(
                ['eds' => 'Editions'],
                'Editions_Release_Dates.Edition_ID = eds.Edition_ID'
                . ' OR eds.Parent_Edition_ID = Editions_Release_Dates.Edition_ID',
                ['Edition_ID']
            );
            $select->join(['i' => 'Items'], 'eds.Item_ID = i.Item_ID', ['Item_ID']);
            $select->order(['Year', 'Month', 'Day', 'Edition_Name']);
            $select->where->equalTo('i.Item_ID', $itemID);
        };
        return iterator_to_array($this->releaseDatesTable->select($callback));
    }

    /**
     * Get a list of dates for the specified edition.
     *
     * @param int $editionID Edition ID
     *
     * @return EditionsReleaseDateEntityInterface[]
     */
    public function getDatesForEdition(int $editionID): array
    {
        $callback = function ($select) use ($editionID): void {
            $select->order(['Year', 'Month', 'Day']);
            $select->where->equalTo('Edition_ID', $editionID);
        };
        return iterator_to_array($this->releaseDatesTable->select($callback));
    }

    /**
     * Get a list of dates for the specified edition (or its immediate parent).
     *
     * @param int $editionID Edition ID
     *
     * @return EditionsReleaseDateEntityInterface[]
     */
    public function getDatesForEditionOrParentEdition(int $editionID): array
    {
        $callback = function ($select) use ($editionID): void {
            $select->quantifier('DISTINCT');
            $select->columns(['Year', 'Month', 'Day', 'Note_ID']);
            $select->join(
                ['eds' => 'Editions'],
                'Editions_Release_Dates.Edition_ID = eds.Edition_ID'
                . ' OR eds.Parent_Edition_ID = Editions_Release_Dates.Edition_ID',
                ['Edition_ID']
            );
            $select->where->equalTo('eds.Edition_ID', $editionID);
            $select->order(['Year', 'Month', 'Day']);
        };
        return iterator_to_array($this->releaseDatesTable->select($callback));
    }

    /**
     * Get a list of items sorted by publication date.
     *
     * @return array
     */
    public function getItemsByYear(): array
    {
        return iterator_to_array($this->releaseDatesTable->getItemsByYear());
    }

    /**
     * Get an entity by edition and date.
     *
     * @param int|EditionEntityInterface $edition Edition entity or ID
     * @param int                        $year    Year (or -1 for unpublished)
     * @param int                        $month   Month (or 0 for unspecified)
     * @param int                        $day     Day (or 0 for unspecified)
     *
     * @return ?EditionsReleaseDateEntityInterface
     */
    public function getByEditionAndYearAndMonthAndDay(
        int|EditionEntityInterface $edition,
        int $year,
        int $month,
        int $day
    ): ?EditionsReleaseDateEntityInterface {
        $editionId = $edition instanceof EditionEntityInterface ? $edition->getId() : $edition;
        $where = ['Edition_ID' => $editionId, 'Year' => $year, 'Month' => $month, 'Day' => $day];
        foreach ($this->releaseDatesTable->select($where) as $row) {
            return $row;
        }
        return null;
    }
}
