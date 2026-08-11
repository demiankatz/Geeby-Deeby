<?php

/**
 * Database service for the Tags table.
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

use GeebyDeeby\Db\Entity\Edition;
use GeebyDeeby\Db\Entity\Item;
use GeebyDeeby\Db\Entity\ItemsAltTitle;
use GeebyDeeby\Db\Entity\ItemsTag;
use GeebyDeeby\Db\Entity\Tag;
use GeebyDeeby\Db\Entity\TagEntityInterface;

/**
 * Database service for the Tags table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class TagService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return TagEntityInterface
     */
    public function createEntity(): TagEntityInterface
    {
        $entity = new Tag();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?TagEntityInterface
     */
    public function getByPrimaryKey(int $id): ?TagEntityInterface
    {
        return $this->entityManager->find(Tag::class, $id);
    }

    /**
     * Validate a populated entity -- return error message if problem found, null otherwise.
     *
     * @param TagEntityInterface $entity Entity to validate
     *
     * @return ?string
     */
    public function getValidationError(TagEntityInterface $entity): ?string
    {
        $name = $entity->getTag();
        return empty($name) ? 'Name cannot be blank.' : null;
    }

    /**
     * Get a list of tag types.
     *
     * @return TagEntityInterface[]
     */
    public function getList(): array
    {
        $dql = 'SELECT t FROM ' . Tag::class . ' t ORDER BY t.tag';
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
        $dql = 'SELECT t FROM ' . Tag::class . ' t WHERE t.tag LIKE :query ORDER BY t.tag';
        $queryObj = $this->entityManager->createQuery($dql);
        $queryObj->setParameter('query', "$query%");
        if ($limit) {
            $queryObj->setMaxResults($limit);
        }
        return $queryObj->getResult();
    }

    /**
     * Get a list of tags and related items for the specified series.
     *
     * @param int $seriesID Series ID
     *
     * @return TagEntityInterface[]
     */
    public function getTagsForSeries(int $seriesID): array
    {
        $dql = 'SELECT DISTINCT t.id AS Tag_ID, t.tag AS Tag, i.id AS Item_ID, i.itemName AS Item_Name, '
            . 'iat.altName AS Item_AltName, COALESCE(iat.altName, i.itemName) AS Best_Title '
            . 'FROM ' . Tag::class . ' t '
            . 'INNER JOIN ' . ItemsTag::class . ' it ON it.tag=t.id '
            . 'INNER JOIN ' . Item::class . ' i ON it.item=i.id '
            . 'INNER JOIN ' . Edition::class . ' e ON e.item=i.id '
            . 'LEFT JOIN ' . ItemsAltTitle::class . ' iat ON e.preferredItemAltName=iat.id '
            . 'WHERE e.series=:series ORDER BY t.tag, Best_Title';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('series', $seriesID);
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
        $where = array_map(fn ($i) => 't.tag LIKE ?' . $i, array_keys($tokens));
        $dql = 'SELECT t.id AS Tag_ID, t.tag AS Tag FROM ' . Tag::class . ' t WHERE '
            . implode(' AND ', $where) . ' ORDER BY t.tag';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters(array_map(fn ($token) => "%$token%", $tokens));
        return $query->getResult();
    }

    /**
     * Get the first tag that matches the provided label, or null if none.
     *
     * @param string $label Label to look up
     *
     * @return ?TagEntityInterface
     */
    public function getByLabel(string $label): ?TagEntityInterface
    {
        $dql = 'SELECT t FROM ' . Tag::class . ' t WHERE t.tag=:query';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('query', $label);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
