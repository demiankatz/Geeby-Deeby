<?php

/**
 * Database service for the Editions table.
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
use GeebyDeeby\Db\Entity\EditionEntityInterface;
use GeebyDeeby\Db\Entity\EditionsAttribute;
use GeebyDeeby\Db\Entity\EditionsAttributesValue;
use GeebyDeeby\Db\Entity\EditionsCredit;
use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\Edition as EditionTable;
use GeebyDeeby\ServiceManager\Factory\Autowire;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;

use function count;
use function in_array;
use function is_int;

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
     * @param EntityManager      $entityManager      Entity manager
     * @param PersistenceManager $persistenceManager Persistence manager
     * @param EditionTable       $editionsTable      Editions table
     * @param ItemService        $itemService        Item database service
     */
    public function __construct(
        EntityManager $entityManager,
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected EditionTable $editionsTable,
        #[Autowire(container: \GeebyDeeby\Db\Service\PluginManager::class)]
        protected ItemService $itemService
    ) {
        parent::__construct($entityManager, $persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return EditionEntityInterface
     */
    public function createEntity(): EditionEntityInterface
    {
        $entity = new Edition();
        $entity->setEntityManager($this->entityManager);
        return $entity;
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
        foreach ($editions as $current) {
            $items[] = $this->getByPrimaryKey($current)->getItem()->getId();
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
        $dql = 'SELECT e FROM ' . Edition::class . ' e ORDER BY e.editionName, e.id';
        $query = $this->entityManager->createQuery($dql);
        return $query->getResult();
    }

    /**
     * Get autocomplete suggestions.
     *
     * @param string $query The user query.
     * @param ?int   $limit Limit on returned rows (null for no limit).
     *
     * @return EditionEntityInterface[]
     */
    public function getSuggestions(string $query, ?int $limit = null): array
    {
        $dql = 'SELECT e FROM ' . Edition::class . ' e WHERE e.editionName LIKE :query ORDER BY e.editionName, e.id';
        $queryObj = $this->entityManager->createQuery($dql);
        $queryObj->setParameter('query', $query . '%');
        if ($limit) {
            $queryObj->setMaxResults($limit);
        }
        return $queryObj->getResult();
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
        $dql = 'SELECT e FROM ' . Edition::class . ' e WHERE e.parentEdition=:edition';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('edition', $edition->getId());
        return $query->getResult();
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
        $from = is_int($from) ? $this->getByPrimaryKey($from) : $from;
        $to = is_int($to) ? $this->getByPrimaryKey($to) : $to;
        foreach ($this->getChildren($from) as $child) {
            $this->copyEdition(
                $child,
                [
                    'Parent_Edition_ID' => $to->getId(),
                    'Series_ID' => $to->getSeries()->getId(),
                    'Edition_Name' => $to->getEditionName(),
                ]
            );
        }
        $this->copyAttributes($from->getId(), $to->getId());
        $this->copyCredits($from->getId(), $to->getId());
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
        $new = $this->createEntity();
        $newValues = $source->toArray();
        $newValues['Edition_Name'] = 'Copy of ' . $newValues['Edition_Name'];
        $newValues = array_merge($newValues, $overrides);
        unset($newValues['Edition_ID']);
        $new->populateFromArray($newValues);
        $this->persistEntity($new);
        $this->copyAssociatedInfo($source, $new);
        return $new;
    }

    /**
     * Copy attributes from another edition.
     *
     * @param int $from Edition to copy from
     * @param int $to   Edition to copy to
     *
     * @return void
     */
    protected function copyAttributes($from, $to)
    {
        $dql = 'SELECT DISTINCT v FROM ' . EditionsAttributesValue::class . ' v '
            . 'INNER JOIN ' . EditionsAttribute::class . ' a ON v.attribute = a.id '
            . 'WHERE v.edition=:edition AND a.copyToClone=true';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('edition', $from);
        foreach ($query->getResult() as $attr) {
            $newAttr = new EditionsAttributesValue();
            $newAttr->setEntityManager($this->entityManager);
            $data = $attr->toArray();
            $data['Edition_ID'] = $to;
            $newAttr->populateFromArray($data);
            $this->persistEntity($newAttr);
        }
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
        $dql = 'SELECT c FROM ' . EditionsCredit::class . ' c WHERE c.edition=:from';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('from', $from);
        $credits = $query->getResult();
        foreach ($credits as $credit) {
            $clone = new EditionsCredit();
            $clone->setEntityManager($this->entityManager);
            $data = $credit->toArray();
            $data['Edition_ID'] = $to;
            $clone->populateFromArray($data);
            $this->persistEntity($clone);
        }
    }

    /**
     * Look up editions by item.
     *
     * @param int $item Item ID or entity
     *
     * @return EditionEntityInterface[]
     */
    public function getByItem(int|ItemEntityInterface $item): array
    {
        $itemId = $item instanceof ItemEntityInterface ? $item->getId() : $item;
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
        $dql = 'SELECT e FROM ' . Edition::class . ' e WHERE e.item=:item AND e.series=:series';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters(['item' => $itemId, 'series' => $seriesId]);
        return $query->getResult();
    }

    /**
     * Insert edition callback (used by handleGenericLink for adding children to editions).
     *
     * @param EditionEntityInterface $new Newly created edition.
     *
     * @return void
     */
    public function insertChildEditionCallback(EditionEntityInterface $new): void
    {
        if ($error = $this->getValidationError($new)) {
            $this->deleteEntity($new);
            throw new \Exception($error);
        }
        foreach ($this->getByItem($new->getItem()) as $edition) {
            if ($edition->getId() != $new->getId()) {
                $this->copyCredits($edition->getId(), $new->getId());
                break;
            }
        }
    }

    /**
     * Insert edition callback (used by handleGenericLink for adding top-level editions to series).
     *
     * @param EditionEntityInterface $new Newly created edition.
     *
     * @return void
     */
    public function insertSeriesEditionCallback(EditionEntityInterface $new): void
    {
        foreach ($this->getByItem($new->getItem()) as $edition) {
            if ($edition->getId() != $new->getId()) {
                $this->copyAssociatedInfo($edition->getId(), $new->getId());
                break;
            }
        }
    }

    /**
     * Get editions with the specified preferred item title ID.
     *
     * @param int $altId Item alt title ID.
     *
     * @return EditionEntityInterface[]
     */
    public function getByItemAltTitleId(int $altId): array
    {
        return iterator_to_array($this->editionsTable->select(['Preferred_Item_AltName_ID' => $altId]));
    }

    /**
     * Get editions with the specified preferred series title ID.
     *
     * @param int $altId Series alt title ID.
     *
     * @return EditionEntityInterface[]
     */
    public function getBySeriesAltTitleId(int $altId): array
    {
        return iterator_to_array($this->editionsTable->select(['Preferred_Series_AltName_ID' => $altId]));
    }

    /**
     * Get editions at the specified position in the specified series.
     *
     * @param int|SeriesEntityInterface $series Series ID
     * @param int                       $pos    Position in series
     *
     * @return EditionEntityInterface[]
     */
    public function getBySeriesAndPosition(int|SeriesEntityInterface $series, int $pos): array
    {
        $where = [
            'Series_ID' => $series instanceof SeriesEntityInterface ? $series->getId() : $series,
            'Position' => $pos,
        ];
        return iterator_to_array($this->editionsTable->select($where));
    }

    /**
     * Get editions with the specified preferred publisher ID.
     *
     * @param int $id Preferred publisher ID.
     *
     * @return EditionEntityInterface[]
     */
    public function getByPreferredPublisherId(int $id): array
    {
        return iterator_to_array($this->editionsTable->select(['Preferred_Series_Publisher_ID' => $id]));
    }

    /**
     * Check for missing creators in a series.
     *
     * @param int $seriesId Series to check
     *
     * @return array
     */
    public function getMissingCreators(int $seriesId): array
    {
        $callback = function ($select) use ($seriesId): void {
            $select->join(
                ['ic' => 'Items_Creators'],
                'Editions.Item_ID = ic.Item_ID',
                [],
                Select::JOIN_LEFT
            );
            $select->join(
                ['i' => 'Items'],
                'Editions.Item_ID = i.Item_ID',
                ['Item_Name'],
                Select::JOIN_LEFT
            );
            $select->where->isNull('ic.Person_ID');
            $select->where(['Series_ID' => $seriesId]);
            $select->order(
                'Editions.Volume, Editions.Position, Editions.Replacement_Number'
            );
        };
        return $this->editionsTable->select($callback)->toArray();
    }

    /**
     * Check for missing credits in a series.
     *
     * @param int $seriesId Series to check
     *
     * @return array
     */
    public function getMissingCredits(int $seriesId): array
    {
        $callback = function ($select) use ($seriesId): void {
            $select->join(
                ['ec' => 'Editions_Credits'],
                'Editions.Edition_ID = ec.Edition_ID',
                [],
                Select::JOIN_LEFT
            );
            $select->join(
                ['i' => 'Items'],
                'Editions.Item_ID = i.Item_ID',
                ['Item_Name'],
                Select::JOIN_LEFT
            );
            $select->where->isNull('ec.Person_ID');
            $select->where(['Series_ID' => $seriesId]);
            $select->order(
                'Editions.Volume, Editions.Position, Editions.Replacement_Number'
            );
        };
        return $this->editionsTable->select($callback)->toArray();
    }

    /**
     * Check for missing dates in a series.
     *
     * @param int $seriesId Series to check
     *
     * @return array
     */
    public function getMissingDates(int $seriesId): array
    {
        $callback = function ($select) use ($seriesId): void {
            $select->join(
                ['d' => 'Editions_Release_Dates'],
                'Editions.Edition_ID = d.Edition_ID',
                [],
                Select::JOIN_LEFT
            );
            $select->join(
                ['i' => 'Items'],
                'Editions.Item_ID = i.Item_ID',
                ['Item_Name'],
                Select::JOIN_LEFT
            );
            $select->where->isNull('d.Year');
            $select->where->isNull('Editions.Parent_Edition_ID');
            $select->where(['Series_ID' => $seriesId]);
            $select->order(
                'Editions.Volume, Editions.Position, Editions.Replacement_Number'
            );
        };
        return $this->editionsTable->select($callback)->toArray();
    }

    /**
     * Get date statistics for a series.
     *
     * @param int $seriesId Series to check
     *
     * @return array
     */
    public function getSeriesDateStats(int $seriesId): array
    {
        $callback = function ($select) use ($seriesId): void {
            $select->where(['Series_ID' => $seriesId]);
            $select->columns(
                [
                    'Edition_ID' => new Expression(
                        'min(?)',
                        ['Editions.Edition_ID'],
                        [Expression::TYPE_IDENTIFIER]
                    ),
                ]
            );
            $select->join(
                ['d' => 'Editions_Release_Dates'],
                'Editions.Edition_ID = d.Edition_ID',
                [
                    'Start' => new Expression(
                        'min(?)',
                        ['Year'],
                        [Expression::TYPE_IDENTIFIER]
                    ),
                    'End' => new Expression(
                        'max(?)',
                        ['Year'],
                        [Expression::TYPE_IDENTIFIER]
                    ),
                ],
                Select::JOIN_LEFT
            );
            $select->group('Series_ID');
        };
        return current($this->editionsTable->select($callback)->toArray());
    }

    /**
     * Get item statistics for a series.
     *
     * @param int $seriesId Series to check
     *
     * @return array
     */
    public function getSeriesItemStats(int $seriesId): array
    {
        $callback = function ($select) use ($seriesId): void {
            $select->where(['Series_ID' => $seriesId]);
            $select->columns(
                [
                    'Edition_ID' => new Expression(
                        'min(?)',
                        ['Edition_ID'],
                        [Expression::TYPE_IDENTIFIER]
                    ),
                    'Vol' => new Expression(
                        'min(?)',
                        ['Volume'],
                        [Expression::TYPE_IDENTIFIER]
                    ),
                    'Pos' => new Expression(
                        'min(?)',
                        ['Position'],
                        [Expression::TYPE_IDENTIFIER]
                    ),
                    'Rep' => new Expression(
                        'min(?)',
                        ['Replacement_Number'],
                        [Expression::TYPE_IDENTIFIER]
                    ),
                    'Total' => new Expression(
                        'count(?)',
                        ['Position'],
                        [Expression::TYPE_IDENTIFIER]
                    ),
                ]
            );
            $select->where->isNull('Parent_Edition_ID');
            $select->group(['Volume', 'Position', 'Replacement_Number']);
            $select->order(['Volume', 'Position', 'Replacement_Number']);
        };
        return $this->editionsTable->select($callback)->toArray();
    }
}
