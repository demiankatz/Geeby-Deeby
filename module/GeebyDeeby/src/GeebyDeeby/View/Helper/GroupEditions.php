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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
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
     *
     * @return string
     */
    public function __invoke($data, $groupField, $editions)
    {
        $fixTitle = $this->getView()->plugin('fixtitle');

        // Group the data:
        $grouped = array();
        $editionsByGroup = array();
        foreach ($data as $current) {
            if (!isset($grouped[$current[$groupField]])) {
                $grouped[$current[$groupField]] = array();
            }
            if (!isset($editionsByGroup[$current[$groupField]])) {
                $editionsByGroup[$current[$groupField]] = array();
            }
            $grouped[$current[$groupField]][] = $current;
            $editionsByGroup[$current[$groupField]][$current['Edition_ID']] = 1;
        }

        // Format the grouped data:
        $edCount = count($editions);
        $final = array();
        foreach ($grouped as $value => $details) {
            $showEds = (count($editionsByGroup[$value]) != $edCount && $edCount > 1);
            $notes = array();
            foreach ($details as $detail) {
                $note = $detail['Note'];
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
