<?php

/**
 * Database service for the Authorities table.
 *
 * PHP version 8
 *
 * Copyright (C) Villanova University 2026.
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
use GeebyDeeby\Db\Entity\Authority;
use GeebyDeeby\Db\Entity\AuthorityEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Authorities table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class AuthorityService extends AbstractDbService
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
     * @return AuthorityEntityInterface
     */
    public function createEntity(): AuthorityEntityInterface
    {
        return new Authority();
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?AuthorityEntityInterface
     */
    public function getByPrimaryKey(int $id): ?AuthorityEntityInterface
    {
        return $this->entityManager->find(Authority::class, $id);
    }

    /**
     * Validate a populated entity -- return error message if problem found, null otherwise.
     *
     * @param AuthorityEntityInterface $entity Entity to validate
     *
     * @return ?string
     */
    public function getValidationError(AuthorityEntityInterface $entity): ?string
    {
        $name = $entity->getAuthorityName();
        return empty($name) ? 'Name cannot be blank.' : null;
    }

    /**
     * Get a list of authorities.
     *
     * @return AuthorityEntityInterface[]
     */
    public function getList(): array
    {
        $dql = 'SELECT a FROM ' . Authority::class . ' a ORDER BY a.authorityName';
        $query = $this->entityManager->createQuery($dql);
        return $query->getResult();
    }
}
