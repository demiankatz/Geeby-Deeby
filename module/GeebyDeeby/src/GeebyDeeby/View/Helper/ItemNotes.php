<?php

/**
 * ItemNotes view helper
 *
 * PHP version 8
 *
 * Copyright (C) Demian Katz 2025.
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

use function count;

/**
 * ItemNotes view helper
 *
 * @category GeebyDeeby
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemNotes extends \Laminas\View\Helper\AbstractHelper
{
    /**
     * Display parenthetical notes following an item title.
     *
     * @param array $item Item details
     *
     * @return string
     */
    public function __invoke($item): string
    {
        $escapeHtml = $this->getView()->plugin('escapeHtml');
        $fixTitle = $this->getView()->plugin('fixtitle');
        $itemNotes = [];
        $earliestYear = (int)($item['Earliest_Year'] ?? 0);
        if ($earliestYear === -1) {
            $itemNotes[] = 'Unpublished';
        } elseif ($earliestYear > 0) {
            $itemNotes[] = ($escapeHtml)($item['Earliest_Year']);
        }
        if (!empty($item['Child_Items'])) {
            $parts = array_unique(explode('||', $item['Child_Items']));
            $childNote = '<i>' . ($escapeHtml)(($fixTitle)($parts[0])) . '</i>';
            if (count($parts) == 2) {
                $childNote .= ' and 1 more item';
            } elseif (count($parts) > 2) {
                $childNote .= ' and ' . (count($parts) - 1) . ' more items';
            }
            $itemNotes[] = $childNote;
        }
        return empty($itemNotes) ? '' : '(' . implode('; ', $itemNotes) . ')';
    }
}
