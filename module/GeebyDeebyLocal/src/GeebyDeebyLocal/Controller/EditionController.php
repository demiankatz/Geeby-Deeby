<?php
/**
 * Edition controller
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeebyLocal\Controller;

/**
 * Edition controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionController extends \GeebyDeeby\Controller\EditionController
{
    /**
     * Get a view model containing an edition object (or return false if missing)
     *
     * @param array $extras Extra parameters to send to view model
     *
     * @return mixed
     */
    protected function getViewModelWithEdition($extras = array())
    {
        $view = parent::getViewModelWithEdition($extras);
        if (!$view) {
            return $view;
        }
        // we don't want a geeby-deeby-local template here!!
        $view->setTemplate('geeby-deeby/edition/show');
        return $view;
    }

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
        $articleHelper = $this->getServiceLocator()->get('GeebyDeeby\Articles');
        $class[] = 'dime:Edition';
        $edition = parent::addPrimaryResourceToGraph($graph, $view, $class);
        foreach ($view->credits as $credit) {
            $personUri = $this->getServerUrl('person', ['id' => $credit['Person_ID']]);
            $edition->add('dime:HasCredit', $personUri);
        }
        if (!empty($view->item)) {
            $itemUri = $this->getServerUrl('item', ['id' => $view->item['Item_ID']]);
            $itemType = $this->getDbTable('materialtype')->getByPrimaryKey(
                $view->item['Material_Type_ID']
            );
            $predicate = $itemType['Material_Type_Name'] == 'Issue'
                ? 'dime:IsEditionOf' : 'dime:IsRealizationOfCreativeWork';
            $edition->set($predicate, $itemUri);
            $itemTitle = empty($view->item['Item_AltName'])
                ? $view->item['Item_Name'] : $view->item['Item_AltName'];
            if (!empty($itemTitle)) {
                $edition->set('rda:titleProper', $itemTitle);
            }
        }
        if (isset($view->series)) {
            $seriesUri = $this->getServerUrl('series', ['id' => $view->series['Series_ID']]);
            $edition->add('rda:HasSeries', $seriesUri);
            if ($view->edition['Position'] > 0) {
                $edition->add('rda:numberingWithinSeries', (int)$view->edition['Position']);
            }
            $seriesTitle = empty($view->series['Series_AltName'])
                ? $view->series['Series_Name'] : $view->series['Series_AltName'];
            if (!empty($seriesTitle)) {
                $edition->set('rda:titleProperOfSeries', $seriesTitle);
            }
        }
        foreach ($view->publishers as $publisher) {
            $pubUri = $this->getServerUrl('publisher', ['id' => $publisher['Publisher_ID']]);
            $edition->add('rda:publisher', $pubUri);
        }
        foreach ($view->children as $child) {
            $childUri = $this->getServerUrl('edition', ['id' => $child['Edition_ID']]);
            $edition->add('rda:containerOf', $childUri);
        }
        if ($view->parent) {
            $parentUri = $this->getServerUrl('edition', ['id' => $view->parent['Edition_ID']]);
            $edition->add('rda:containedIn', $parentUri);
        }
        if (!empty($view->edition['Edition_Length'])) {
            $edition->add('rda:extent', $view->edition['Edition_Length']);
        }
        foreach ($view->dates as $date) {
            if ($date['Year'] > 0) {
                $dateStr = $date['Year'];
                foreach (['Month', 'Day'] as $field) {
                    if (!empty($date[$field])) {
                        $dateStr .= '-' . substr('0' . $date[$field], -2);
                    }
                }
                $edition->add('rda:dateOfPublication', ['type' => 'literal', 'value' => $dateStr, 'datatype' => 'xsd:date']);
            }
        }
        return $edition;
    }
}
