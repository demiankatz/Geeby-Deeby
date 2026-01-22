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
     * Get a list of edition.
     *
     * @return EditionEntityInterface[]
     */
    public function getList(): array
    {
        return iterator_to_array($this->editionsTable->getList());
    }
}
