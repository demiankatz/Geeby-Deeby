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
     * @param Editions $editionsTable Editions table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected Edition $editionsTable
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
}
