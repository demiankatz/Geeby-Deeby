<?php

/**
 * Database service for the Editions_Full_Text table.
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
use GeebyDeeby\Db\Entity\Edition;
use GeebyDeeby\Db\Entity\EditionsFullText;
use GeebyDeeby\Db\Entity\EditionsFullTextEntityInterface;
use GeebyDeeby\Db\Entity\FullTextSource;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\EditionsFullText as EditionsFullTextTable;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Editions_Full_Text table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsFullTextService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param EntityManager         $entityManager         Entity manager
     * @param PersistenceManager    $persistenceManager    Persistence manager
     * @param EditionsFullTextTable $editionsFullTextTable EditionsFullText table
     */
    public function __construct(
        EntityManager $entityManager,
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected EditionsFullTextTable $editionsFullTextTable
    ) {
        parent::__construct($entityManager, $persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return EditionsFullTextEntityInterface
     */
    public function createEntity(): EditionsFullTextEntityInterface
    {
        $entity = new EditionsFullText();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?EditionsFullTextEntityInterface
     */
    public function getByPrimaryKey(int $id): ?EditionsFullTextEntityInterface
    {
        return $this->entityManager->find(EditionsFullText::class, $id);
    }

    /**
     * Get a list of full text links for a particular edition.
     *
     * @param int $edition Edition ID
     *
     * @return EditionsFullTextEntityInterface[]
     */
    public function getFullTextForEdition(int $edition): array
    {
        $dql = 'SELECT DISTINCT eft FROM ' . EditionsFullText::class . ' eft '
            . 'INNER JOIN ' . FullTextSource::class . ' fts ON eft.source=fts.id '
            . 'INNER JOIN ' . Edition::class . ' e ON eft.edition=e.id '
            . 'WHERE e.id = :edition '
            . 'ORDER BY fts.sourceName, eft.url';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('edition', $edition);
        return $query->getResult();
    }

    /**
     * Get a list of full text links for a particular edition (or its immediate
     * parent).
     *
     * @param int $edition Edition ID
     *
     * @return EditionsFullTextEntityInterface[]
     */
    public function getFullTextForEditionOrParentEdition(int $edition): array
    {
        $dql = 'SELECT DISTINCT eft FROM ' . EditionsFullText::class . ' eft '
            . 'INNER JOIN ' . FullTextSource::class . ' fts ON eft.source=fts.id '
            . 'INNER JOIN ' . Edition::class . ' e ON eft.edition=e.id OR eft.edition=e.parentEdition '
            . 'WHERE e.id = :edition '
            . 'ORDER BY fts.sourceName, eft.url';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('edition', $edition);
        return $query->getResult();
    }

    /**
     * Get a list of full text links for a particular item.
     *
     * @param int $item Item ID
     *
     * @return EditionsFullTextEntityInterface[]
     */
    public function getFullTextForItem(int $item): array
    {
        $dql = 'SELECT DISTINCT eft FROM ' . EditionsFullText::class . ' eft '
            . 'INNER JOIN ' . FullTextSource::class . ' fts ON eft.source=fts.id '
            . 'INNER JOIN ' . Edition::class . ' e ON eft.edition=e.id OR eft.edition=e.parentEdition '
            . 'WHERE e.item = :item '
            . 'ORDER BY fts.sourceName, e.editionName, eft.url';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('item', $item);
        return $query->getResult();
    }

    /**
     * Get a list of full text entries by full text source.
     *
     * @param int $source Full text source ID
     *
     * @return EditionsFullTextEntityInterface[]
     */
    public function getFullTextForSource(int $source): array
    {
        $dql = 'SELECT eft FROM ' . EditionsFullText::class . ' eft WHERE eft.source = :source ORDER BY eft.url';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('source', $source);
        return $query->getResult();
    }

    /**
     * Get a list of items with full text.
     *
     * @param ?int $series Series ID (optional limiter)
     * @param bool $fuzzy  Use fuzzy matching? (default = false)
     * @param ?int $source Full text source ID (optional limiter)
     *
     * @return array
     */
    public function getItemsWithFullText(
        ?int $series = null,
        bool $fuzzy = false,
        ?int $source = null
    ): array {
        return iterator_to_array($this->editionsFullTextTable->getItemsWithFullText($series, $fuzzy, $source));
    }
}
