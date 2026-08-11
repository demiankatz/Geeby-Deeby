<?php

/**
 * Edit material type controller
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
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Controller;

use GeebyDeeby\Db\Entity\MaterialTypeEntityInterface;
use GeebyDeeby\Db\Service\MaterialTypeService;

/**
 * Edit material type controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditMaterialTypeController extends AbstractBase
{
    /**
     * Display a list of types
     *
     * @return mixed
     */
    public function listAction()
    {
        return $this->getGenericList(
            MaterialTypeService::class,
            'materials',
            'geeby-deeby/edit-material-type/render-material-types'
        );
    }

    /**
     * Support method for indexAction -- set a default material type.
     *
     * @param MaterialTypeEntityInterface $row Material type to set as default
     *
     * @return void
     */
    protected function setDefaultMaterialType(MaterialTypeEntityInterface $row)
    {
        // If row is already set as default, no further action is needed:
        if ($row->isDefault()) {
            return;
        }

        $this->getDbService(MaterialTypeService::class)->setDefaultMaterialType($row);
    }

    /**
     * Operate on a single type
     *
     * @return mixed
     */
    public function indexAction()
    {
        $assignMap = [
            'material' => 'setSingularName',
            'material_plural' => 'setPluralName',
            'material_rdf' => 'setRdfClass',
        ];
        [$response, $ok] = $this->handleGenericItem(MaterialTypeService::class, $assignMap, 'material');

        // Special handling for "set as default" checkbox:
        if (
            $ok && $this->getRequest()->isPost()
            && $this->params()->fromPost('default')
        ) {
            $this->setDefaultMaterialType($response->affectedEntity);
        }

        return $response;
    }
}
