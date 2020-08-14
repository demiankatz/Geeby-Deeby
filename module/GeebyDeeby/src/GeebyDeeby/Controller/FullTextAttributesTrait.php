<?php
/**
 * Trait for shared logic related to full text attribute display.
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2020.
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
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\Controller;

/**
 * Trait for shared logic related to full text attribute display.
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
trait FullTextAttributesTrait
{
    /**
     * Add full text attributes to a view object containing full text data.
     *
     * @param object $view View object
     *
     * @return void
     */
    protected function addFullTextAttributesToView($view)
    {
        $fullTextAttributes = [];
        if (count($view->fullText ?? []) > 0) {
            $attrTable = $this->getDbTable('editionsfulltextattributesvalues');
            $ids = array_map(
                function ($current) {
                    return $current['Sequence_ID'];
                }, $view->fullText->toArray()
            );
            foreach ($attrTable->getAttributesForFullTextIDs($ids) as $attr) {
                $fullTextAttributes[$attr->Editions_Full_Text_ID][] = $attr;
            }
        }
        $view->fullTextAttributes = $fullTextAttributes;
    }
}