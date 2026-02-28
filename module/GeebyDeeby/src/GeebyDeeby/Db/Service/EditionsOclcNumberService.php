<?php

/**
 * Database service for the Editions_OCLC_Numbers table.
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
use GeebyDeeby\Db\Entity\EditionsOclcNumberEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\EditionsOCLCNumbers;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Editions_OCLC_Numbers table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsOclcNumberService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param EntityManager       $entityManager      Entity manager
     * @param PersistenceManager  $persistenceManager Persistence manager
     * @param EditionsOCLCNumbers $oclcNumbersTable   EditionsOCLCNumbers table
     */
    public function __construct(
        EntityManager $entityManager,
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected EditionsOCLCNumbers $oclcNumbersTable
    ) {
        parent::__construct($entityManager, $persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return EditionsOclcNumberEntityInterface
     */
    public function createEntity(): EditionsOclcNumberEntityInterface
    {
        return $this->oclcNumbersTable->createRow();
    }

    /**
     * Retrieve a row by its primary key.
     *
     * @param int $id Identifier to retrieve
     *
     * @return ?EditionsOclcNumberEntityInterface
     */
    public function getByPrimaryKey(int $id): ?EditionsOclcNumberEntityInterface
    {
        return $this->oclcNumbersTable->getByPrimaryKey($id) ?: null;
    }

    /**
     * Get a list of OCLC Numbers for the specified edition.
     *
     * @param int $editionID Edition ID
     *
     * @return EditionsOclcNumberEntityInterface[]
     */
    public function getOCLCNumbersForEdition(int $editionID): array
    {
        $callback = function ($select) use ($editionID): void {
            $select->order('OCLC_Number');
            $select->where->equalTo('Edition_ID', $editionID);
        };
        return iterator_to_array($this->oclcNumbersTable->select($callback));
    }

    /**
     * Get a list of OCLC Numbers for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return EditionsOclcNumberEntityInterface[]
     */
    public function getOCLCNumbersForItem(int $itemID): array
    {
        $callback = function ($select) use ($itemID): void {
            $select->join(
                ['eds' => 'Editions'],
                'Editions_OCLC_Numbers.Edition_ID = eds.Edition_ID',
                []
            );
            $select->order('OCLC_Number');
            $select->where->equalTo('Item_ID', $itemID);
        };
        return iterator_to_array($this->oclcNumbersTable->select($callback));
    }
}
