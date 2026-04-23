<?php

/**
 * Database service for the Items table.
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

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Query\UnionType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator as PaginationPaginator;
use GeebyDeeby\Db\DoctrinePaginatorAdapter;
use GeebyDeeby\Db\Entity\Edition;
use GeebyDeeby\Db\Entity\EditionsReleaseDate;
use GeebyDeeby\Db\Entity\Item;
use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\ItemsAltTitle;
use GeebyDeeby\Db\Entity\MaterialType;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\Item as ItemTable;
use GeebyDeeby\ServiceManager\Factory\Autowire;
use Laminas\Paginator\Paginator;

/**
 * Database service for the Items table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param EntityManager      $entityManager      Entity manager
     * @param PersistenceManager $persistenceManager Persistence manager
     * @param ItemTable          $itemTable          Item table
     */
    public function __construct(
        EntityManager $entityManager,
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected ItemTable $itemTable
    ) {
        parent::__construct($entityManager, $persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return ItemEntityInterface
     */
    public function createEntity(): ItemEntityInterface
    {
        $entity = new Item();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?ItemEntityInterface
     */
    public function getByPrimaryKey(int $id): ?ItemEntityInterface
    {
        return $this->entityManager->find(Item::class, $id);
    }

    /**
     * Validate a populated entity -- return error message if problem found, null otherwise.
     *
     * @param ItemEntityInterface $entity Entity to validate
     *
     * @return ?string
     */
    public function getValidationError(ItemEntityInterface $entity): ?string
    {
        $name = $entity->getItemName();
        return empty($name) ? 'Item name cannot be blank.' : null;
    }

    /**
     * Get a list of items.
     *
     * @return ItemEntityInterface[]
     */
    public function getList(): array
    {
        $dql = 'SELECT i FROM ' . Item::class . ' i ORDER BY i.itemName';
        $query = $this->entityManager->createQuery($dql);
        return $query->getResult();
    }

    /**
     * Get autocomplete suggestions.
     *
     * @param string $query The user query.
     * @param ?int   $limit Limit on returned rows (null for no limit).
     *
     * @return mixed
     */
    public function getSuggestions(string $query, ?int $limit = null): array
    {
        $connection = $this->entityManager->getConnection();
        $firstQuery = new QueryBuilder($connection);
        $firstQuery->select('i.Item_ID, Item_Name')
            ->from('Items', 'i')
            ->where('i.Item_Name LIKE :query');
        $secondQuery = $this->entityManager->createQueryBuilder();
        $secondQuery->select(
            "i.Item_ID, CONCAT(iat.Item_AltName, ' [alt. title for ', i.Item_Name, ']') AS Item_Name"
        )->from('Items_AltTitles', 'iat')
            ->innerJoin('Items', 'i', 'ON', 'i.Item_ID = iat.Item_ID')
            ->where('iat.Item_AltName LIKE :query');
        $queryBuilder = new QueryBuilder($connection);
        $union = $queryBuilder
            ->union($firstQuery)
            ->addUnion($secondQuery, UnionType::DISTINCT)
            ->orderBy('Item_Name', 'ASC');
        if ($limit) {
            $union->setMaxResults($limit);
        }
        $result = $connection->executeQuery($union->getSQL(), ['query' => $query . '%']);
        return $result->fetchAllAssociative();
    }

    /**
     * Perform a keyword search.
     *
     * @param array $tokens Keywords.
     *
     * @return array
     */
    public function keywordSearch(array $tokens): array
    {
        $where = array_map(fn ($i) => 'i.itemName LIKE ?' . $i, array_keys($tokens));
        $dql = 'SELECT i.id AS Item_ID, i.itemName AS Item_Name FROM ' . Item::class . ' i WHERE '
            . implode(' AND ', $where) . ' ORDER BY i.itemName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters(array_map(fn ($token) => "%$token%", $tokens));
        return $query->getResult();
    }

    /**
     * Get a list of items for the specified series.
     *
     * @param int  $seriesID        Series ID
     * @param bool $topOnly         Retrieve only top-level items?
     * @param bool $groupByMaterial Should we group results by material type?
     *
     * @return mixed
     */
    public function getItemsForSeries(
        int $seriesID,
        bool $topOnly = true,
        bool $groupByMaterial = true
    ): array {
        $order = 'e.volume, e.position, e.replacementNumber, Best_Title';
        if ($groupByMaterial) {
            $order = 'm.singularName, ' . $order;
        }
        $where = ['e.series=:series'];
        $params = ['series' => $seriesID];
        if ($topOnly) {
            $extraJoins =  'LEFT JOIN ' . Edition::class . ' childE ON childE.parentEdition=e.id '
                . 'LEFT JOIN ' . Item::class . ' childI ON childE.item=childI.id '
                . 'LEFT JOIN ' . ItemsAltTitle::class . ' childIat ON childE.preferredItemAltName=childIat.id ';
            $extraSelect = 'GROUP_CONCAT('
                . "COALESCE(childIat.altName, childI.itemName) ORDER BY childE.positionInParent SEPARATOR '||'"
                . ') AS Child_Items, ';
            $where[] = 'e.parentEdition IS NULL';
        }
        $dql = 'SELECT ' . $extraSelect . 'MIN(erd.year) AS Earliest_Year, MIN(e.id) AS Edition_ID, '
            . 'e.volume AS Volume, e.position AS Position, e.replacementNumber AS Replacement_Number, '
            . 'i.itemName AS Item_Name, i.id AS Item_ID, iat.altName AS Item_AltName, '
            . 'm.id AS Material_Type_ID, m.singularName AS Material_Type_Name, '
            . 'm.pluralName AS Material_Type_Plural_Name, COALESCE(iat.altName, i.itemName) AS Best_Title '
            . 'FROM ' . Edition::class . ' e '
            . 'INNER JOIN ' . Item::class . ' i ON e.item=i.id '
            . 'INNER JOIN ' . MaterialType::class . ' m ON i.materialType=m.id '
            . 'LEFT JOIN ' . EditionsReleaseDate::class . ' erd ON e.id=erd.edition '
            . 'LEFT JOIN ' . ItemsAltTitle::class . ' iat ON e.preferredItemAltName=iat.id '
            . $extraJoins
            . 'WHERE ' . implode(' AND ', $where) . ' '
            . 'GROUP BY i.id, e.volume, e.position, e.replacementNumber, m.id '
            . 'ORDER BY ' . $order;
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        return $query->getResult();
    }

    /**
     * Get a list of children for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return array
     */
    public function getItemChildren(int $itemID): array
    {
        return iterator_to_array($this->itemTable->getItemChildren($itemID));
    }

    /**
     * Get a list of parents for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return array
     */
    public function getItemParents(int $itemID): array
    {
        return iterator_to_array($this->itemTable->getItemParents($itemID));
    }

    /**
     * Get a list of items for the specified edition.
     *
     * @param int $editionID Edition ID
     *
     * @return array
     */
    public function getItemsForEdition(int $editionID): array
    {
        return iterator_to_array($this->itemTable->getItemsForEdition($editionID));
    }

    /**
     * Get items with online full text associated with a person
     *
     * @param int $personId Person_ID to filter by
     *
     * @return array
     */
    public function getItemsWithFullTextByPerson(int $personId): array
    {
        return iterator_to_array($this->itemTable->getItemsWithFullTextByPerson($personId));
    }

    /**
     * Get a paginator populated with new items.
     *
     * @param int $page     Result page to load
     * @param int $pageSize Result count per page
     *
     * @return Paginator
     */
    public function getNewItemsPaginator(int $page = 1, int $pageSize = 50): Paginator
    {
        $dql = 'SELECT i FROM ' . Item::class . ' i ORDER BY i.id DESC';
        $query = $this->entityManager->createQuery($dql);
        $query->setFirstResult(($page - 1) * $pageSize)->setMaxResults($pageSize);
        $doctrinePaginator = new PaginationPaginator($query);
        $doctrinePaginator->setUseOutputWalkers(false);
        $paginator = new \Laminas\Paginator\Paginator(
            new DoctrinePaginatorAdapter($doctrinePaginator)
        );
        $paginator->setItemCountPerPage($pageSize);
        $paginator->setCurrentPageNumber($page);
        return $paginator;
    }
}
