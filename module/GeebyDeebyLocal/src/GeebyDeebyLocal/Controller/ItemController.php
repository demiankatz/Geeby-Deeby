<?php
/**
 * Item controller
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
namespace GeebyDeebyLocal\Controller;

/**
 * Item controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemController extends \GeebyDeeby\Controller\ItemController
{
    /**
     * Build the primary resource in an RDF graph.
     *
     * @param \EasyRdf\Graph $graph Graph to populate
     * @param object         $view  View model populated with information.
     * @param mixed          $class Class(es) for resource.
     *
     * @return \EasyRdf\Resource
     */
    protected function addPrimaryResourceToGraph($graph, $view, $class = array())
    {
        $articleHelper = $this->serviceLocator->get('GeebyDeeby\Articles');
        $item = parent::addPrimaryResourceToGraph($graph, $view, $class);
        $item->set(
            'rda:preferredTitleForTheResource',
            $articleHelper->formatTrailingArticles($view->item['Item_Name'])
        );
        $itemType = $this->getDbTable('materialtype')->getByPrimaryKey(
            $view->item['Material_Type_ID']
        );
        $relationship = ($itemType['Material_Type_Name'] == 'Issue')
            ? 'dime:HasEdition' : 'dime:HasRealizationOfCreativeWork';
        foreach ($view->editions as $edition) {
            $editionUri = $this->getServerUrl('edition', ['id' => $edition['Edition_ID']]);
            $item->add($relationship, $graph->resource($editionUri));
        }
        foreach ($view->creators as $creator) {
            $personUri = $this->getServerUrl('person', ['id' => $creator['Person_ID']]);
            $item->add('rda:author', $graph->resource($personUri));
        }
        foreach ($view->altTitles as $altTitle) {
            $item->add('rda:variantTitle', $altTitle['Item_AltName']);
        }
        return $item;
    }

    /**
     * Get the module namespace for use in template resolution. See
     * \GeebyDeebyLocal\View\InjectTemplateListener. This allows us to extend core
     * controllers without duplicating templates.
     *
     * @return string
     */
    public static function getModuleTemplateNamespace()
    {
        return 'geeby-deeby';
    }
}
