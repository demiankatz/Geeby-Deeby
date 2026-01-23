<?php

/**
 * Database service for the Editions table.
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

use GeebyDeeby\Db\Entity\EditionEntityInterface;
use GeebyDeeby\Db\Table\Edition;
use GeebyDeeby\ServiceManager\Factory\Autowire;

use function count;
use function in_array;

/**
 * Database service for the Editions table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param Editions    $editionsTable Editions table
     * @param ItemService $itemService   Item database service
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected Edition $editionsTable,
        #[Autowire(container: \GeebyDeeby\Db\Service\PluginManager::class)]
        protected ItemService $itemService
    ) {
    }

    /**
     * Create an empty entity
     *
     * @return EditionEntityInterface
     */
    public function createEntity(): EditionEntityInterface
    {
        return $this->editionsTable->createRow();
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?EditionEntityInterface
     */
    public function getByPrimaryKey(int $id): ?EditionEntityInterface
    {
        return $this->editionsTable->getByPrimaryKey($id);
    }

    /**
     * Validate a populated entity -- return error message if problem found, null otherwise.
     *
     * @param EditionEntityInterface $entity Entity to validate
     *
     * @return ?string
     */
    public function getValidationError(EditionEntityInterface $entity): ?string
    {
        if (!$entity->getEditionName()) {
            return 'Edition name cannot be blank.';
        }
        if (in_array($entity->getId(), $this->getEditionParentChain($entity))) {
            return 'Edition can not be its own parent or grandparent.';
        }
        $item = $entity->getItem();
        if ($item && in_array($item->getId(), $this->getItemParentChain($entity))) {
            return 'Item can not be its own parent or grandparent.';
        }
        return null;
    }

    /**
     * Get an array of all parent Edition IDs for the specified edition.
     *
     * @param EditionEntityInterface $edition Edition to check
     *
     * @return int[]
     */
    public function getEditionParentChain(EditionEntityInterface $edition): array
    {
        $parents = [];
        $nextParent = $edition->getParentEdition();
        while (true) {
            // Circular parent detection:
            if (!$nextParent || in_array($nextParent->getId(), $parents)) {
                return $parents;
            }
            $parents[] = $nextParent->getId();
            $nextParent = $nextParent->getParentEdition();
        }
    }

    /**
     * Get an array of all parent Item IDs.
     *
     * @param EditionEntityInterface $edition Edition to check
     *
     * @return int[]
     */
    public function getItemParentChain(EditionEntityInterface $edition): array
    {
        $editions = $this->getEditionParentChain($edition);
        $items = [];
        foreach ($editions as $edition) {
            $items[] = $this->editionsTable->getByPrimaryKey($edition)->getItem()->getId();
        }
        return $items;
    }

    /**
     * Support function for getNextInSeries / getPreviousInSeries.
     *
     * @param EditionEntityInterface $edition Edition to start with
     * @param bool                   $next    Get next (true) or previous (false)?
     *
     * @return ?EditionEntityInterface
     */
    protected function getAdjacentInSeries(EditionEntityInterface $edition, bool $next): ?EditionEntityInterface
    {
        $series = $edition->getSeries();
        if (!$series) {
            return null;
        }
        $editionId = $edition->getId();
        $seriesId = $series->getId();
        $vol = $edition->getVolume();
        $pos = $edition->getPosition();
        $rep = $edition->getReplacementNumber();
        $name = $edition->getEditionName();
        $callback = function ($select) use (
            $editionId,
            $seriesId,
            $name,
            $vol,
            $pos,
            $rep,
            $next
        ): void {
            $select->where->equalTo('Series_ID', $seriesId);
            $select->where->notEqualTo('Edition_ID', $editionId);
            $fields = [
                'Volume', 'Position', 'Replacement_Number', 'Edition_Name',
                'Edition_ID',
            ];
            $vals = [$vol, $pos, $rep, $name, $editionId];
            $nest = $select->where->NEST;
            for ($i = 0; $i < count($fields); $i++) {
                $clause = $nest->OR->NEST;
                for ($j = 0; $j <= $i; $j++) {
                    if ($j == $i) {
                        if ($next) {
                            $clause->greaterThan($fields[$j], $vals[$j]);
                        } else {
                            $clause->lessThan($fields[$j], $vals[$j]);
                        }
                    } else {
                        $clause->equalTo($fields[$j], $vals[$j]);
                    }
                }
                $clause->UNNEST;
            }
            $nest->UNNEST;
            $select->order(
                $next ? $fields : array_map(
                    function ($i) {
                        return "$i DESC";
                    },
                    $fields
                )
            );
            $select->limit(1);
        };
        $results = $this->editionsTable->select($callback);
        return count($results) > 0 ? $results->current() : null;
    }

    /**
     * Get previous edition in series.
     *
     * @param EditionEntityInterface $edition Edition to start with
     *
     * @return ?EditionEntityInterface
     */
    public function getNextInSeries(EditionEntityInterface $edition): ?EditionEntityInterface
    {
        return $this->getAdjacentInSeries($edition, true);
    }

    /**
     * Get previous edition in series.
     *
     * @param EditionEntityInterface $edition Edition to start with
     *
     * @return ?EditionEntityInterface
     */
    public function getPreviousInSeries(EditionEntityInterface $edition)
    {
        return $this->getAdjacentInSeries($edition, false);
    }

    /**
     * Get a list of edition.
     *
     * @return EditionEntityInterface[]
     */
    public function getList(): array
    {
        return iterator_to_array($this->editionsTable->getList());
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
        return iterator_to_array($this->editionsTable->getSuggestions($query, $limit));
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
        return iterator_to_array($this->editionsTable->keywordSearch($tokens));
    }

    /**
     * Get parent item for the specified edition (false if none).
     *
     * @param int $editionID Edition ID
     *
     * @return ?EditionEntityInterface
     */
    public function getParentItemForEdition(int $editionID): ?EditionEntityInterface
    {
        return $this->editionsTable->getParentItemForEdition($editionID) ?: null;
    }

    /**
     * Get a list of items for the specified edition.
     *
     * @param int $editionID Edition ID
     *
     * @return mixed
     */
    public function getItemsForEdition($editionID)
    {
        // Proxy item service (so handleGenericLink() can be used in
        // EditEditionController):
        return $this->itemService->getItemsForEdition($editionID);
    }

    /**
     * Get a list of items for the specified series (not grouped by material type).
     *
     * @param int $seriesID Series ID
     *
     * @return mixed
     */
    public function getItemsForSeries($seriesID)
    {
        // Proxy item service (so handleGenericLink() can be used in
        // EditSeriesController):
        return $this->itemService->getItemsForSeries($seriesID, true, false);
    }

    /**
     * Get a list of items for the specified series ( grouped by material type).
     *
     * @param int $seriesID Series ID
     *
     * @return mixed
     */
    public function getItemsForSeriesGroupedByMaterial($seriesID)
    {
        // Proxy item service (so handleGenericLink() can be used in
        // EditSeriesController):
        return $this->itemService->getItemsForSeries($seriesID);
    }

    /**
     * Retrieve editions for the specified item.
     *
     * @param int  $itemID         Item ID.
     * @param bool $includeParents Should we include information on parent items?
     *
     * @return array
     */
    public function getEditionsForItem(int $itemID, bool $includeParents = false): array
    {
        return iterator_to_array($this->editionsTable->getEditionsForItem($itemID, $includeParents));
    }

    /**
     * Retrieve publishers for the specified edition.
     *
     * @param int $id Edition ID.
     *
     * @return array
     */
    public function getPublishersForEdition(int $id): array
    {
        return iterator_to_array($this->editionsTable->getPublishersForEdition($id));
    }

    /**
     * Retrieve publishers for the specified item.
     *
     * @param int $itemID Item ID.
     *
     * @return array
     */
    public function getPublishersForItem(int $itemID): array
    {
        return iterator_to_array($this->editionsTable->getPublishersForItem($itemID));
    }

    /**
     * Delete an edition if there are no attached data items.
     *
     * @param int $id ID of edition to delete
     *
     * @throws \Exception
     * @return void
     */
    public function safeDelete(int $id): void
    {
        $this->editionsTable->safeDelete($id);
    }

    /**
     * Get immediate children of the provided edition.
     *
     * @param EditionEntityInterface $edition Parent edition
     *
     * @return EditionEntityInterface[]
     */
    public function getChildren(EditionEntityInterface $edition): array
    {
        return iterator_to_array($this->editionsTable->getChildren($edition));
    }

    /**
     * Copy information associated with one edition into another.
     *
     * @param int|EditionEntityInterface $from Source item (object or ID)
     * @param int|EditionEntityInterface $to   Target item (object or ID)
     *
     * @return void
     */
    public function copyAssociatedInfo(int|EditionEntityInterface $from, int|EditionEntityInterface $to): void
    {
        $this->editionsTable->copyAssociatedInfo($from, $to);
    }

    /**
     * Create a copy of the specified edition.
     *
     * @param EditionEntityInterface $source    Edition to copy
     * @param array                  $overrides Fields to override during copying
     *
     * @return EditionEntityInterface
     */
    public function copyEdition(EditionEntityInterface $source, array $overrides = []): EditionEntityInterface
    {
        return $this->editionsTable->copyEdition($source, $overrides);
    }

    /**
     * Copy credits from another edition.
     *
     * @param int $from Edition to copy from
     * @param int $to   Edition to copy to
     *
     * @return void
     */
    public function copyCredits(int $from, int $to): void
    {
        $this->editionsTable->copyCredits($from, $to);
    }

    /**
     * Look up editions by item.
     *
     * @param int $itemId Item ID
     *
     * @return EditionEntityInterface[]
     */
    public function getByItem(int $itemId): array
    {
        $itemEditions = $this->editionsTable->select(['Item_ID' => $itemId]);
        return iterator_to_array($itemEditions);
    }

    /**
     * Look up editions by item and series.
     *
     * @param int $itemId   Item ID
     * @param int $seriesId Series ID
     *
     * @return EditionEntityInterface[]
     */
    public function getByItemAndSeries(int $itemId, int $seriesId): array
    {
        $seriesEditions = $this->editionsTable->select(
            ['Item_ID' => $itemId, 'Series_ID' => $seriesId]
        );
        return iterator_to_array($seriesEditions);
    }
}
