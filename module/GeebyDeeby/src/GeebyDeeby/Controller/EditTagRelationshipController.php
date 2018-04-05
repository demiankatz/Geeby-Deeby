<?php
/**
 * Edit tag relationship controller
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2018.
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
 * Edit tag attribute controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditTagRelationshipController extends AbstractBase
{
    /**
     * Display a list of types
     *
     * @return mixed
     */
    public function listAction()
    {
        return $this->getGenericList(
            'tagsrelationship', 'relationships',
            'geeby-deeby/edit-tag-relationship/render-tag-relationships'
        );
    }

    /**
     * Operate on a single type
     *
     * @return mixed
     */
    public function indexAction()
    {
        $assignMap = array(
            'relationship_name' => 'Tags_Relationship_Name',
            'rdf_property' => 'Tags_Relationship_RDF_Property',
            'priority' => 'Display_Priority',
            'inverse_relationship_name' => 'Tags_Inverse_Relationship_Name',
            'inverse_rdf_property' => 'Tags_Inverse_Relationship_RDF_Property',
            'inverse_priority' => 'Inverse_Display_Priority',            
        );
        $response = $this
            ->handleGenericItem('tagsrelationship', $assignMap, 'relationship');

        return $response;
    }
}
