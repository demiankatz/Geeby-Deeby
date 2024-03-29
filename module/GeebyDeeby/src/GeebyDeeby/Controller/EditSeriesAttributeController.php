<?php

/**
 * Edit series attribute controller
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
 * Edit series attribute controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditSeriesAttributeController extends AbstractBase
{
    /**
     * Display a list of types
     *
     * @return mixed
     */
    public function listAction()
    {
        $view = $this->getGenericList(
            'seriesattribute',
            'attributes',
            'geeby-deeby/edit-series-attribute/render-series-attributes'
        );
        // If this is not an AJAX request, we also want to display relationships:
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $view->relationships
                = $this->forwardTo(__NAMESPACE__ . '\EditSeriesRelationship', 'list')
                ->relationships;
        }
        return $view;
    }

    /**
     * Operate on a single type
     *
     * @return mixed
     */
    public function indexAction()
    {
        $assignMap = [
            'attribute_name' => 'Series_Attribute_Name',
            'rdf_property' => 'Series_Attribute_RDF_Property',
            'allow_html' => 'Allow_HTML',
            'value_link' => 'Value_Link',
            'priority' => 'Display_Priority',
        ];
        [$response] = $this
            ->handleGenericItem('seriesattribute', $assignMap, 'attribute');

        return $response;
    }
}
