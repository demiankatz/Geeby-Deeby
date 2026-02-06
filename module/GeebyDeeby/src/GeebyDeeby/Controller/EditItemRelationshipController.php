<?php

/**
 * Edit item relationship controller
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

use GeebyDeeby\Db\Service\ItemsRelationshipService;

/**
 * Edit item relationship controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditItemRelationshipController extends AbstractBase
{
    /**
     * Display a list of types
     *
     * @return mixed
     */
    public function listAction()
    {
        return $this->getGenericList(
            ItemsRelationshipService::class,
            'relationships',
            'geeby-deeby/edit-item-relationship/render-item-relationships'
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
            'relationship_name' => 'setRelationshipName',
            'rdf_property' => 'setRDFProperty',
            'priority' => 'setDisplayPriority',
            'inverse_relationship_name' => 'setInverseRelationshipName',
            'inverse_rdf_property' => 'setInverseRDFProperty',
            'inverse_priority' => 'setInverseDisplayPriority',
        ];
        [$response] = $this->handleGenericItem(ItemsRelationshipService::class, $assignMap, 'relationship');

        return $response;
    }
}
