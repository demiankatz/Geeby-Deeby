<?php

/**
 * Trait to incorporate the getOptionList method into a relationship service.
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

namespace GeebyDeeby\Db\Service\Feature;

/**
 * Trait to incorporate the getOptionList method into a relationship service.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
trait RelationshipOptionListTrait
{
    /**
     * Get a list of relationships, formatted to populate a select control.
     *
     * @param bool $includePredicate Should we return labels only (false) or an
     * array with label and predicate (true)?
     *
     * @return array
     */
    public function getOptionList(bool $includePredicate = false): array
    {
        $options = [];
        foreach ($this->getList() as $current) {
            $id = $current->getId();
            $options[] = [
                $current->getDisplayPriority(),
                $id,
                $current->getRelationshipName(),
                $current->getRdfProperty(),
            ];
            if ($current->getInverseRelationshipName()) {
                $options[] = [
                    $current->getInverseDisplayPriority(),
                    'i' . $id,
                    $current->getInverseRelationshipName(),
                    $current->getInverseRdfProperty(),
                ];
            }
        }
        $callback = function ($a, $b) {
            return $a[0] - $b[0];
        };
        usort($options, $callback);
        $retval = [];
        foreach ($options as $current) {
            $value = $includePredicate
                ? ['label' => $current[2], 'predicate' => $current[3]]
                : $current[2];
            $retval[$current[1]] = $value;
        }
        return $retval;
    }
}
