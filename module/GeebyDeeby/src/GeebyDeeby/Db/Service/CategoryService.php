<?php

/**
 * Database service for the Categories table.
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
use GeebyDeeby\Db\Entity\Category;
use GeebyDeeby\Db\Entity\CategoryEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Categories table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class CategoryService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param EntityManager      $entityManager      Entity manager
     * @param PersistenceManager $persistenceManager Persistence manager
     */
    #[Autowire()]
    public function __construct(
        protected EntityManager $entityManager,
        PersistenceManager $persistenceManager,
    ) {
        parent::__construct($persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return CategoryEntityInterface
     */
    public function createEntity(): CategoryEntityInterface
    {
        return new Category();
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?CategoryEntityInterface
     */
    public function getByPrimaryKey(int $id): ?CategoryEntityInterface
    {
        return $this->entityManager->find(Category::class, $id);
    }

    /**
     * Validate a populated entity -- return error message if problem found, null otherwise.
     *
     * @param CategoryEntityInterface $entity Entity to validate
     *
     * @return ?string
     */
    public function getValidationError(CategoryEntityInterface $entity): ?string
    {
        $name = $entity->getCategoryName();
        return empty($name) ? 'Category name cannot be blank.' : null;
    }

    /**
     * Get a list of categories.
     *
     * @return CategoryEntityInterface[]
     */
    public function getList(): array
    {
        $dql = 'SELECT c FROM ' . Category::class . ' c ORDER BY c.categoryName';
        $query = $this->entityManager->createQuery($dql);
        return $query->getResult();
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
        $where = array_map(fn ($i) => 'c.categoryName LIKE ?' . $i, array_keys($tokens));
        $dql = 'SELECT c.id AS Category_ID, c.categoryName AS Category FROM ' . Category::class . ' c WHERE '
            . implode(' AND ', $where) . ' ORDER BY c.categoryName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters(array_map(fn ($token) => "%$token%", $tokens));
        return $query->getResult();
    }
}
