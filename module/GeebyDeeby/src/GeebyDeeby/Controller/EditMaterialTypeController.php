<?php
/**
 * Edit material type controller
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
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\Controller;

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
            'materialtype', 'materials',
            'geeby-deeby/edit-material-type/render-material-types'
        );
    }

    /**
     * Support method for indexAction -- set a default material type.
     *
     * @param \GeebyDeeby\Db\Row\MaterialType $row Material type to set as default
     *
     * @return void
     */
    protected function setDefaultMaterialType($row)
    {
        // If row is already set as default, no further action is needed:
        if ($row->Default) {
            return;
        }

        // First clear existing default:
        $table = $this->getDbTable('materialtype');
        $table->update(array('Default' => 0));

        // Now set new default:
        $row->Default = 1;
        $row->save();
    }

    /**
     * Operate on a single type
     *
     * @return mixed
     */
    public function indexAction()
    {
        $assignMap = array(
            'material' => 'Material_Type_Name',
            'material_plural' => 'Material_Type_Plural_Name',
            'material_rdf' => 'Material_Type_RDF_Class'
        );
        $response = $this->handleGenericItem('materialtype', $assignMap, 'material');

        // Special handling for "set as default" checkbox:
        if ($this->getRequest()->isPost() && $this->params()->fromPost('default')) {
            $this->setDefaultMaterialType($response->affectedRow);
        }

        return $response;
    }
}
