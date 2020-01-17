<?php
/**
 * Person display view helper
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
 * Person display view helper
 *
 * @category GeebyDeeby
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ShowPerson extends \Zend\View\Helper\AbstractHelper
{
    /**
     * Format a person's name.
     *
     * @param array $person Person to reformat
     *
     * @return string
     */
    public function __invoke($person)
    {
        $text = $person['Last_Name'];
        $first = trim($person['First_Name'] . ' ' . $person['Middle_Name']);
        if (!empty($first)) {
            $text .= ', ' . $first;
        }
        if (!empty($person['Extra_Details'])) {
            // We don't want spaces before commas:
            if (substr($person['Extra_Details'], 0, 1) !== ',') {
                $text .= ' ';
            }
            $text .= $person['Extra_Details'];
        }
        return $text;
    }
}
