<?php

/**
 * Local extensions to database service for the Editions table.
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

namespace GeebyDeebyLocal\Db\Service;

use GeebyDeeby\Db\Entity\EditionEntityInterface;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;

use function count;

/**
 * Local extensions to database service for the Editions table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionService extends \GeebyDeeby\Db\Service\EditionService
{
    /**
     * Get editions of the specified type from the specified series that have a position > 0.
     *
     * @param int                   $typeId Material type ID
     * @param SeriesEntityInterface $series Series entity
     *
     * @return EditionEntityInterface[]
     */
    public function getNumberedEditionsOfTypeFromSeries(int $typeId, SeriesEntityInterface $series): array
    {
        $callback = function ($select) use ($typeId, $series): void {
            $select->join(['i' => 'Items'], 'i.Item_ID = Editions.Item_ID', []);
            $select->where(
                [
                    'Series_ID' => $series->getId(),
                    'i.Material_Type_ID' => $typeId,
                ]
            );
            $select->where->greaterThan('Position', 0);
            $select->order('Position');
        };
        return iterator_to_array($this->editionsTable->select($callback));
    }

    /**
     * Check if an edition matching the position of the provided edition and the provided material type exists.
     *
     * @param EditionEntityInterface $edition        Edition entity
     * @param int                    $materialTypeId Material type ID
     *
     * @return bool
     */
    public function hasEditionMatchingPositionAndType(EditionEntityInterface $edition, int $materialTypeId): bool
    {
        $callback = function ($select) use ($materialTypeId, $edition): void {
            $select->join(['i' => 'Items'], 'i.Item_ID = Editions.Item_ID', []);
            $select->where(
                [
                    'Series_ID' => $edition->getSeries()->getId(),
                    'i.Material_Type_ID' => $materialTypeId,
                    'Position' => $edition->getPosition(),
                    'Volume' => $edition->getVolume(),
                    'Replacement_Number' => $edition->getReplacementNumber(),
                ]
            );
        };
        $results = $this->editionsTable->select($callback);
        return count($results) > 0;
    }
}
