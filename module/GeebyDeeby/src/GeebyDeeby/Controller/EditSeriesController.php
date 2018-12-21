<?php
/**
 * Edit series controller
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
 * Edit series controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditSeriesController extends AbstractBase
{
    /**
     * Display a list of series
     *
     * @return mixed
     */
    public function listAction()
    {
        return $this->getGenericList(
            'series', 'series', 'geeby-deeby/edit-series/render-series'
        );
    }

    /**
     * Save attributes for the current series.
     *
     * @param int   $seriesId Series ID
     * @param array $attribs  Attribute values
     *
     * @return void
     */
    protected function saveAttributes($seriesId, $attribs)
    {
        $table = $this->getDbTable('seriesattributesvalues');
        // Delete old values:
        $table->delete(['Series_ID' => $seriesId]);
        // Save new values:
        foreach ($attribs as $id => $val) {
            if (!empty($val)) {
                $table->insert(
                    [
                        'Series_ID' => $seriesId,
                        'Series_Attribute_ID' => $id,
                        'Series_Attribute_Value' => $val
                    ]
                );
            }
        }
    }

    /**
     * Operate on a single series
     *
     * @return mixed
     */
    public function indexAction()
    {
        $assignMap = array(
            'name' => 'Series_Name',
            'desc' => 'Series_Description',
            'lang' => 'Language_ID'
        );
        $view = $this->handleGenericItem('series', $assignMap, 'series');
        $seriesId = isset($view->seriesObj->Series_ID)
            ? $view->seriesObj->Series_ID
            : (isset($view->affectedRow->Series_ID) ? $view->affectedRow->Series_ID : null);

        // Special handling for saving attributes:
        if ($this->getRequest()->isPost() && $this->params()->fromPost('attribs')) {
            $this->saveAttributes(
                $seriesId, $this->params()->fromPost('attribs')
            );
        }

        $languages = $this->getDbTable('language');
        $view->languages = $languages->getList();
        $view->attributes = $this->getDbTable('seriesattribute')->getList();
        $attributeValues = [];
        $values = $this->getDbTable('seriesattributesvalues')
            ->getAttributesForSeries($seriesId);
        foreach ($values as $current) {
            $attributeValues[$current->Series_Attribute_ID]
                = $current->Series_Attribute_Value;
        }
        $view->attributeValues = $attributeValues;

        // Add extra fields/controls if outside of a lightbox:
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $view->materials = $this->getDbTable('materialtype')->getList();
            $view->countries = $this->getDbTable('country')->getList();
            $view->categories = $this->getDbTable('category')->getList();
            $config = $this->getServiceLocator()->get('config');
            $groupByMaterial = isset($config['geeby-deeby']['groupSeriesByMaterialType'])
                ? $config['geeby-deeby']['groupSeriesByMaterialType'] : true;
            $view->item_list = $this->getDbTable('item')
                ->getItemsForSeries($seriesId, true, $groupByMaterial);
            $view->series_alt_titles = $this->getDbTable('seriesalttitles')
                ->getAltTitles($seriesId);
            $view->series_materials = $this->getDbTable('seriesmaterialtypes')
                ->getMaterials($seriesId);
            $view->series_publishers = $this->getDbTable('seriespublishers')
                ->getPublishers($seriesId);
            $view->translatedInto = $this->getDbTable('seriestranslations')
                ->getTranslatedFrom($seriesId);
            $view->translatedFrom = $this->getDbTable('seriestranslations')
                ->getTranslatedInto($seriesId);
            $view->setTemplate('geeby-deeby/edit-series/edit-full');
        }

        return $view;
    }

    /**
     * Work with categories
     *
     * @return mixed
     */
    public function categoriesAction()
    {
        $ok = $this->checkPermission('Content_Editor');
        if ($ok !== true) {
            return $ok;
        }
        if ($this->getRequest()->isPost()) {
            $table = $this->getDbTable('seriescategories');
            $series = $this->params()->fromRoute('id');
            $categories = $this->params()->fromPost('categories', array());
            $table->delete(array('Series_ID' => $series));
            foreach ($categories as $cat) {
                $table->insert(array('Series_ID' => $series, 'Category_ID' => $cat));
            }
            return $this->jsonReportSuccess();
        }
        return $this->jsonError('Unexpected action');
    }

    /**
     * Work with material types
     *
     * @return mixed
     */
    public function materialAction()
    {
        return $this->handleGenericLink(
            'seriesmaterialtypes', 'Series_ID', 'Material_Type_ID',
            'series_materials', 'getMaterials',
            'geeby-deeby/edit-series/material-type-list'
        );
    }

    /**
     * Work with alternate titles
     *
     * @return mixed
     */
    public function alttitleAction()
    {
        // Special case: new title:
        if ($this->getRequest()->isPost()) {
            $ok = $this->checkPermission('Content_Editor');
            if ($ok !== true) {
                return $ok;
            }
            $table = $this->getDbTable('seriesalttitles');
            $row = $table->createRow();
            $row->Series_ID = $this->params()->fromRoute('id');
            $row->Note_ID = $this->params()->fromPost('note_id');
            if (empty($row->Note_ID)) {
                $row->Note_ID = null;
            }
            $row->Series_AltName = $this->params()->fromPost('title');
            if (empty($row->Series_AltName)) {
                return $this->jsonDie('Title must not be empty.');
            }
            $table->insert((array)$row);
            return $this->jsonReportSuccess();
        } else {
            // Prevent deletion of alttitles that are linked up:
            if ($this->getRequest()->isDelete()) {
                $extra = $this->params()->fromRoute('extra');
                $result = $this->getDbTable('edition')->select(
                    array('Preferred_Series_AltName_ID' => $extra)
                );
                if (count($result) > 0) {
                    $ed = $result->current();
                    $msg = 'You cannot delete this title; it is assigned to Edition '
                        . $ed->Edition_ID . '.';
                    return $this->jsonDie($msg);
                }
            }
            // Otherwise, treat this as a generic link:
            return $this->handleGenericLink(
                'seriesalttitles', 'Series_ID', 'Sequence_ID',
                'series_alt_titles', 'getAltTitles',
                'geeby-deeby/edit-series/alt-title-list.phtml'
            );
        }
    }

    /**
     * Support method for publisherAction()
     *
     * @return mixed
     */
    protected function modifyPublisher()
    {
        $rowId = $this->params()->fromRoute('extra');
        $table = $this->getDbTable('seriespublishers');
        if ($this->getRequest()->isPost()) {
            $imprint = $this->params()->fromPost('imprint');
            if (empty($imprint)) {
                $imprint = null;
            }
            $address = $this->params()->fromPost('address');
            if (empty($address)) {
                $address = null;
            }
            $fields = array(
                'Imprint_ID' => $imprint, 'Address_ID' => $address
            );
            $table->update($fields, array('Series_Publisher_ID' => $rowId));
            return $this->jsonReportSuccess();
        }
        $view = $this->createViewModel();
        $view->row = $table->getByPrimaryKey($rowId);
        $view->addresses = $this->getDbTable('publishersaddresses')
            ->getAddressesForPublisher($view->row->Publisher_ID);
        $view->imprints = $this->getDbTable('publishersimprints')
            ->getImprintsForPublisher($view->row->Publisher_ID);
        $view->setTemplate('geeby-deeby/edit-series/modify-publisher');

        // If this is an AJAX request, render the core list only, not the
        // framing layout and buttons.
        if ($this->getRequest()->isXmlHttpRequest()) {
            $view->setTerminal(true);
        }

        return $view;
    }

    /**
     * Work with publishers
     *
     * @return mixed
     */
    public function publisherAction()
    {
        // Modify the publisher if it's a GET/POST and has an extra set.
        if (($this->getRequest()->isPost() || $this->getRequest()->isGet())
            && null !== $this->params()->fromRoute('extra')
            && 'NEW' !== $this->params()->fromRoute('extra')
        ) {
            return $this->modifyPublisher();
        }

        // Special case: new publisher:
        if ($this->getRequest()->isPost()) {
            $ok = $this->checkPermission('Content_Editor');
            if ($ok !== true) {
                return $ok;
            }
            $table = $this->getDbTable('seriespublishers');
            $row = $table->createRow();
            $row->Series_ID = $this->params()->fromRoute('id');
            $row->Publisher_ID = $this->params()->fromPost('publisher_id');
            $row->Note_ID = $this->params()->fromPost('note_id');
            if (empty($row->Note_ID)) {
                $row->Note_ID = null;
            }
            $row->save();
            return $this->jsonReportSuccess();
        } else {
            if ($this->getRequest()->isDelete()) {
                $extra = $this->params()->fromRoute('extra');
                $result = $this->getDbTable('edition')->select(
                    array('Preferred_Series_Publisher_ID' => $extra)
                );
                if (count($result) > 0) {
                    $ed = $result->current();
                    $msg = 'You cannot delete this publisher; '
                        . 'it is assigned to Edition '
                        . $ed->Edition_ID . '.';
                    return $this->jsonDie($msg);
                }
            }
            // Otherwise, treat this as a generic link:
            return $this->handleGenericLink(
                'seriespublishers', 'Series_ID', 'Series_Publisher_ID',
                'series_publishers', 'getPublishers',
                'geeby-deeby/edit-series/publisher-list.phtml'
            );
        }
    }

    /**
     * Deal with items
     *
     * @return mixed
     */
    public function itemAction()
    {
        // Special case: delete editions differently from other links:
        if ($this->getRequest()->isDelete()) {
            $ok = $this->checkPermission('Content_Editor');
            if ($ok !== true) {
                return $ok;
            }
            try {
                $this->getDbTable('edition')
                    ->safeDelete($this->params()->fromRoute('extra'));
            } catch (\Exception $e) {
                return $this->jsonDie($e->getMessage());
            }
            return $this->jsonReportSuccess();
        }

        $series = $this->getDbTable('series')->getByPrimaryKey(
            $this->params()->fromRoute('id')
        );
        $edName = $this->getServiceLocator()->get('GeebyDeeby\Articles')
            ->articleAwareAppend($series->Series_Name, ' edition');
        $insertCallback = function ($new, $row, $sm) {
            $edsTable = $sm->get('GeebyDeeby\DbTablePluginManager')
                ->get('edition');
            $rows = $edsTable->select(array('Item_ID' => $row['Item_ID']));
            foreach ($rows as $row) {
                $row = $row->toArray();
                if ($row['Edition_ID'] != $new) {
                    break;
                }
            }
            if (isset($row['Edition_ID']) && $row['Edition_ID'] != $new) {
                $edsTable->copyAssociatedInfo($row['Edition_ID'], $new);
            }
        };
        $config = $this->getServiceLocator()->get('config');
        $groupByMaterial = isset($config['geeby-deeby']['groupSeriesByMaterialType'])
            ? $config['geeby-deeby']['groupSeriesByMaterialType'] : true;
        $listCallback = $groupByMaterial
            ? 'getItemsForSeriesGroupedByMaterial' : 'getItemsForSeries';
        return $this->handleGenericLink(
            'edition', 'Series_ID', 'Item_ID',
            'item_list', $listCallback,
            'geeby-deeby/edit-series/item-list.phtml',
            array('Edition_Name' => $edName), $insertCallback
        );
    }

    /**
     * Set the order of an item
     *
     * @return mixed
     */
    public function itemorderAction()
    {
        $ok = $this->checkPermission('Content_Editor');
        if ($ok !== true) {
            return $ok;
        }
        if ($this->getRequest()->isPost()) {
            $edition = $this->params()->fromPost('edition_id');
            $raw = $this->params()->fromPost('pos');
            $parts = explode(',', $raw);
            if (count($parts) < 2) {
                $vol = '0';
                $pos = $parts[0];
            } else {
                list($vol, $pos) = $parts;
            }
            $this->getDbTable('edition')->update(
                array('Position' => intval($pos), 'Volume' => intval($vol)),
                array('Edition_ID' => $edition)
            );
            return $this->jsonReportSuccess();
        }
        return $this->jsonDie('Unexpected method');
    }

    /**
     * Show action -- allows tolerance of URLs where the user has inserted 'edit'
     * into an existing front-end link.
     *
     * @return mixed
     */
    public function showAction()
    {
        return $this->redirect()->toRoute(
            'edit/series',
            [
                'action' => 'index',
                'id' => $this->params()->fromRoute('id'),
                'extra' => $this->params()->fromRoute('extra')
            ]
        );
    }

    /**
     * Deal with translations
     *
     * @return mixed
     */
    public function translationintoAction()
    {
        return $this->handleGenericLink(
            'seriestranslations', 'Source_Series_ID', 'Trans_Series_ID',
            'translatedInto', 'getTranslatedFrom',
            'geeby-deeby/edit-series/trans-into-list.phtml'
        );
    }

    /**
     * Deal with translation sources
     *
     * @return mixed
     */
    public function translationfromAction()
    {
        return $this->handleGenericLink(
            'seriestranslations', 'Trans_Series_ID', 'Source_Series_ID',
            'translatedFrom', 'getTranslatedInto',
            'geeby-deeby/edit-series/trans-from-list.phtml'
        );
    }
}
