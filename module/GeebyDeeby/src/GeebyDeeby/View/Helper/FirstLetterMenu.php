<?php

/**
 * List first letters as a horizontal jump menu
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
use function is_array;

/**
 * List first letters as a horizontal jump menu
 *
 * @category GeebyDeeby
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class FirstLetterMenu
{
    /**
     * Constructor
     *
     * @param FirstLetter $firstLetterHelper FirstLetter helper
     */
    public function __construct(
        #[Autowire(container: 'ViewHelperManager')]
        protected FirstLetter $firstLetterHelper
    ) {
    }

    /**
     * Build HTML to list first letters as a horizontal jump menu
     *
     * @param array  $list  List of values
     * @param string $index Field in list items to use for display
     *
     * @return string
     */
    public function __invoke($list, $index)
    {
        if (!is_array($list)) {
            $list = $list->toArray();
        }
        $currentLetter = false;
        $letters = [];
        for ($i = 0; $i < count($list); $i++) {
            $first = ($this->firstLetterHelper)($list[$i][$index]);
            if ($currentLetter !== $first) {
                $currentLetter = $first;
                $letters[] = $first;
            }
        }
        $html = $letters[0];
        for ($i = 1; $i < count($letters); $i++) {
            $html .= ' <a href="#' . $letters[$i] . '">' . $letters[$i] . '</a>';
        }
        return $html;
    }
}
