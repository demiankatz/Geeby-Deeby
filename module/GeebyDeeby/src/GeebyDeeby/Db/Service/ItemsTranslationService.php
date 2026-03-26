<?php

/**
 * Database service for the Items_Translations table.
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
use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\ItemsTranslation;
use GeebyDeeby\Db\Entity\ItemsTranslationEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\ItemsTranslations;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Items_Translations table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsTranslationService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param EntityManager      $entityManager          Entity manager
     * @param PersistenceManager $persistenceManager     Persistence manager
     * @param ItemsTranslations  $itemsTranslationsTable ItemsTranslations table
     */
    public function __construct(
        EntityManager $entityManager,
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected ItemsTranslations $itemsTranslationsTable
    ) {
        parent::__construct($entityManager, $persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return ItemsTranslationEntityInterface
     */
    public function createEntity(): ItemsTranslationEntityInterface
    {
        return $this->itemsTranslationsTable->createRow();
    }

    /**
     * Get a list of items translated from the specified item.
     *
     * @param int  $itemID      Item ID
     * @param bool $includeLang Should we also load language information?
     *
     * @return array
     */
    public function getTranslatedFrom(int $itemID, bool $includeLang = false): array
    {
        return iterator_to_array($this->itemsTranslationsTable->getTranslatedFrom($itemID, $includeLang));
    }

    /**
     * Get a list of items translated into the specified item.
     *
     * @param int  $itemID      Item ID
     * @param bool $includeLang Should we also load language information?
     *
     * @return array
     */
    public function getTranslatedInto(int $itemID, bool $includeLang = false): array
    {
        return iterator_to_array($this->itemsTranslationsTable->getTranslatedInto($itemID, $includeLang));
    }

    /**
     * Get a row matching the provided source item/translated item pair.
     *
     * @param int|ItemEntityInterface $source     Source item ID or entity
     * @param int|ItemEntityInterface $translated Translated item ID or entity
     *
     * @return ?ItemsTranslationEntityInterface
     */
    public function getBySourceItemAndTranslatedItem(
        int|ItemEntityInterface $source,
        int|ItemEntityInterface $translated
    ): ?ItemsTranslationEntityInterface {
        $dql = 'SELECT it FROM ' . ItemsTranslation::class
            . ' it WHERE it.sourceItem=:source AND it.translatedItem=:translated';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('source', $source instanceof ItemEntityInterface ? $source->getId() : $source);
        $query->setParameter(
            'translated',
            $translated instanceof ItemEntityInterface ? $translated->getId() : $translated
        );
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
