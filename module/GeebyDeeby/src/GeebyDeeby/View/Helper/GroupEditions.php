<?php
/**
 * View helper to group together edition data
 *
 * PHP version 5
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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category GeebyDeeby
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\View\Helper;

/**
 * View helper to group together edition data
 *
 * @category GeebyDeeby
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class GroupEditions extends \Zend\View\Helper\AbstractHelper
{
    /**
     * Group together edition data
     *
     * @param array  $data       Data to work with
     * @param string $groupField Field to group data on
     * @param array  $editions   A list of all editions
     * @param string $idField    An ID field to prepend to the $groupField with
     * a pipe delimiter (optional)
     *
     * @return string
     */
    public function __invoke($data, $groupField, $editions, $idField = null)
    {
        $fixTitle = $this->getView()->plugin('fixtitle');

        // Group the data:
        $grouped = [];
        $editionsByGroup = [];
        foreach ($data as $current) {
            $groupValue = $current[$groupField];
            if (!empty($idField)) {
                $groupValue = $current[$idField] . '|' . $groupValue;
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
                $note = $detail['Note'] ?? '';
                if ($showEds) {
                    $name = $fixTitle($detail['Edition_Name']);
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
