<?php

/**
 * Database service for the Notes table.
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

use GeebyDeeby\Db\Entity\Note;
use GeebyDeeby\Db\Entity\NoteEntityInterface;

/**
 * Database service for the Notes table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class NoteService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return NoteEntityInterface
     */
    public function createEntity(): NoteEntityInterface
    {
        return new Note();
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?NoteEntityInterface
     */
    public function getByPrimaryKey(int $id): ?NoteEntityInterface
    {
        return $this->entityManager->find(Note::class, $id);
    }

    /**
     * Validate a populated entity -- return error message if problem found, null otherwise.
     *
     * @param NoteEntityInterface $entity Entity to validate
     *
     * @return ?string
     */
    public function getValidationError(NoteEntityInterface $entity): ?string
    {
        $name = $entity->getNote();
        return empty($name) ? 'Note cannot be blank.' : null;
    }

    /**
     * Get a list of file types.
     *
     * @return NoteEntityInterface[]
     */
    public function getList(): array
    {
        $dql = 'SELECT n FROM ' . Note::class . ' n ORDER BY n.note';
        $query = $this->entityManager->createQuery($dql);
        return $query->getResult();
    }

    /**
     * Get autocomplete suggestions.
     *
     * @param string $query The user query.
     * @param ?int   $limit Limit on returned rows (null for no limit).
     *
     * @return array
     */
    public function getSuggestions(string $query, ?int $limit = null): array
    {
        $dql = 'SELECT n FROM ' . Note::class . ' n WHERE n.note LIKE :query ORDER BY n.note';
        $queryObj = $this->entityManager->createQuery($dql);
        $queryObj->setParameter('query', $query . '%');
        if ($limit) {
            $queryObj->setMaxResults($limit);
        }
        return $queryObj->getResult();
    }
}
