<?php

/**
 * "First letter" view helper for displaying labels within alphabetical lists
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

use function strlen;

/**
 * "First letter" view helper for displaying labels within alphabetical lists
 *
 * @category GeebyDeeby
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class FirstLetter extends \Laminas\View\Helper\AbstractHelper
{
    /**
     * Return a normalized first letter for label/grouping purposes.
     *
     * @param string $text Text to extract letter from
     *
     * @return string
     */
    public function __invoke($text)
    {
        setlocale(LC_ALL, 'en_US');
        $firstUtf = mb_strtoupper(mb_substr($text, 0, 1));
        $first = preg_replace(
            '/[^0-9A-Z]/',
            '',
            iconv('utf-8', 'ascii//TRANSLIT', $firstUtf)
        );
        return strlen($first) == 1 ? $first : $firstUtf;
    }
}
