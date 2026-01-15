<?php

/**
 * Database service for the Tags_URIs table.
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
use GeebyDeeby\Db\Entity\TagsUriEntityInterface;
use GeebyDeeby\Db\Table\TagsURIs;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Tags_URIs table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class TagsUriService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param TagsURIs $tagsUrisTable TagsURIs table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected TagsURIs $tagsUrisTable
    ) {
    }

    /**
     * Create an empty entity
     *
     * @return TagsUriEntityInterface
     */
    public function createEntity(): TagsUriEntityInterface
    {
        return $this->tagsUrisTable->createRow();
    }

    /**
     * Get a list of tags for the specified URI.
     *
     * @param string $uri URI
     *
     * @return array
     */
    public function getTagsForURI(string $uri): array
    {
        return iterator_to_array($this->tagsUrisTable->getTagsForURI($uri));
    }

    /**
     * Get a list of URIs for the specified tag.
     *
     * @param int|TagEntityInterface $tag Tag entity or ID
     *
     * @return array
     */
    public function getURIsForTag(int|TagEntityInterface $tag): array
    {
        $tagId = $tag instanceof TagEntityInterface ? $tag->getId() : $tag;
        return iterator_to_array($this->tagsUrisTable->getURIsForTag($tagId));
    }

    /**
     * Retrieve an existing entry using a tag ID and URI (null if not found).
     *
     * @param int    $tagId Tag ID
     * @param string $uri   URI
     *
     * @return ?TagsUriEntityInterface
     */
    public function getByTagAndUri(int $tagId, string $uri): ?TagsUriEntityInterface
    {
        foreach ($this->tagsUrisTable->select(['Tag_ID' => $tagId, 'URI' => $uri]) as $row) {
            return $row;
        }
        return null;
    }
}
