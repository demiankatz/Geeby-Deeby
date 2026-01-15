<?php

/**
 * Database service for the Tags table.
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

use GeebyDeeby\Db\Entity\TagEntityInterface;
use GeebyDeeby\Db\Table\Tag;
use GeebyDeeby\ServiceManager\Factory\Autowire;

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
     * Constructor
     *
     * @param Tag $tagTable Tag table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected Tag $tagTable
    ) {
    }

    /**
     * Create an empty entity
     *
     * @return TagEntityInterface
     */
    public function createEntity(): TagEntityInterface
    {
        return $this->tagTable->createRow();
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
        return $this->tagTable->getByPrimaryKey($id);
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
        return iterator_to_array($this->tagTable->getList());
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
        return iterator_to_array($this->tagTable->getSuggestions($query, $limit));
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
        return iterator_to_array($this->tagTable->getTagsForSeries($seriesID));
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
        return iterator_to_array($this->tagTable->keywordSearch($tokens));
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
        $tags = $this->tagTable->select(['Tag' => $label]);
        foreach ($tags as $tag) {
            return $tag;
        }
        return null;
    }
}
