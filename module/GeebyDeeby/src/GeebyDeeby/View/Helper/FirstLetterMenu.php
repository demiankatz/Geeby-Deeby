<?php
/**
 * List first letters as a horizontal jump menu
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
 * Title display view helper
 *
 * @category GeebyDeeby
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class FirstLetterMenu extends \Zend\View\Helper\AbstractHelper
{    
    /**
     * Format a title with the article in the correct position.
     *
     * @param string $title Title to reformat
     *
     * @return string
     */
    public function __invoke($list, $index)
    {
        $list = $list->toArray();
        $currentLetter = false;
        $letters = array();
        for ($i=0;$i<count($list);$i++) {
            $first = strtoupper(substr($list[$i][$index], 0, 1));
            if ($currentLetter !== $first) {
                $currentLetter = $first;
                $letters[] = $first;
            }
        }
        $html = $letters[0];
        for ($i=1;$i<count($letters);$i++) {
          $html .= ' <a href="#' . $letters[$i] . '">' . $letters[$i] . '</a>';
        }
        return $html;
    }
}