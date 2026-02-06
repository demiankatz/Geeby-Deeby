<?php

/**
 * Edit item attribute controller
 *
 * PHP version 8
 *
 * Copyright (C) Demian Katz 2019.
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
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Controller;

use GeebyDeeby\Db\Service\ItemsAttributeService;

/**
 * Edit item attribute controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditItemAttributeController extends AbstractBase
{
    /**
     * Display a list of types
     *
     * @return mixed
     */
    public function listAction()
    {
        return $this->getGenericList(
            ItemsAttributeService::class,
            'attributes',
            'geeby-deeby/edit-item-attribute/render-item-attributes'
        );
    }

    /**
     * Operate on a single type
     *
     * @return mixed
     */
    public function indexAction()
    {
        $assignMap = [
            'attribute_name' => 'setAttributeName',
            'rdf_property' => 'setRdfProperty',
            'allow_html' => 'setAllowsHTML',
            'priority' => 'setDisplayPriority',

        ];
        [$response] = $this->handleGenericItem(ItemsAttributeService::class, $assignMap, 'attribute');

        return $response;
    }
}
