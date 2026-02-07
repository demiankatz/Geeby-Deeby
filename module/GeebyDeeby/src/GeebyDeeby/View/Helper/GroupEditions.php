<?php

/**
 * View helper to group together edition data
 *
 * PHP version 8
 *
 * Copyright (C) Demian Katz 2012.
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
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\View\Helper;

use GeebyDeeby\ServiceManager\Factory\Autowire;

use function count;
use function is_callable;
use function is_object;

/**
 * View helper to group together edition data
 *
 * @category GeebyDeeby
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class GroupEditions
{
    /**
     * Constructor
     *
     * @param FixTitle $fixTitleHelper FixTitle view helper.
     */
    public function __construct(
        #[Autowire(container: 'ViewHelperManager')]
        protected FixTitle $fixTitleHelper
    ) {
    }

    /**
     * Group together edition data
     *
     * @param array   $data            Data to work with
     * @param string  $groupField      Field to group data on
     * @param array   $editions        A list of all editions
     * @param ?string $idField         An ID field to prepend to the $groupField with
     * a pipe delimiter (optional)
     * @param ?string $subEntityGetter A method on objects in $data to fetch a sub-entity containing
     * $groupField and/or $idField
     *
     * @return string
     */
    public function __invoke($data, $groupField, $editions, $idField = null, $subEntityGetter = null)
    {
        // Group the data:
        $grouped = [];
        $editionsByGroup = [];
        foreach ($data as $current) {
            if ($subEntityGetter && is_callable([$current, $subEntityGetter])) {
                $subEntity = $current->$subEntityGetter();
            }
            $groupValue = $subEntity[$groupField] ?? $current[$groupField];
            if (!empty($idField)) {
                $groupValue = ($subEntity[$idField] ?? $current[$idField]) . '|' . $groupValue;
            }
            if (!isset($grouped[$groupValue])) {
                $grouped[$groupValue] = [];
            }
            if (!isset($editionsByGroup[$groupValue])) {
                $editionsByGroup[$groupValue] = [];
            }
            $grouped[$groupValue][] = $current;
            $editionsByGroup[$groupValue][$current['Edition_ID']] = 1;
        }

        // Format the grouped data:
        $edCount = count($editions);
        $final = [];
        foreach ($grouped as $value => $details) {
            $showEds = (count($editionsByGroup[$value]) != $edCount && $edCount > 1);
            $notes = [];
            foreach ($details as $detail) {
                // Use entity interface where appropriate:
                $note = is_object($detail) && is_callable([$detail, 'getNote'])
                    ? $detail->getNote()?->getNote() ?? ''
                    : $detail['Note'] ?? '';
                if ($showEds) {
                    $editionName = is_object($detail) && is_callable([$detail, 'getEdition'])
                        ? $detail->getEdition()?->getEditionName()
                        : $detail['Edition_Name'];
                    $name = ($this->fixTitleHelper)($editionName);
                    $note = empty($note) ? $name : $name . ' - ' . $note;
                }
                if (!empty($note)) {
                    $notes[] = $note;
                }
            }
            $final[$value] = implode(', ', array_unique($notes));
        }
        return $final;
    }
}
