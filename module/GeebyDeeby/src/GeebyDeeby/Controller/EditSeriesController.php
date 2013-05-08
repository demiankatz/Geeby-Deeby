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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
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
        $languages = $this->getDbTable('language');
        $view->languages = $languages->getList();

        // Add extra fields/controls if outside of a lightbox:
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $view->materials = $this->getDbTable('materialtype')->getList();
            $view->countries = $this->getDbTable('country')->getList();
            $view->categories = $this->getDbTable('category')->getList();
            $view->item_list = $this->getDbTable('item')
                ->getItemsForSeries($view->seriesObj->Series_ID);
            $view->series_alt_titles = $this->getDbTable('seriesalttitles')
                ->getAltTitles($view->seriesObj->Series_ID);
            $view->series_materials = $this->getDbTable('seriesmaterialtypes')
                ->getMaterials($view->seriesObj->Series_ID);
            $view->series_publishers = $this->getDbTable('seriespublishers')
                ->getPublishers($view->seriesObj->Series_ID);
            $view->translatedInto = $this->getDbTable('seriestranslations')
                ->getTranslatedFrom($view->seriesObj->Series_ID);
            $view->translatedFrom = $this->getDbTable('seriestranslations')
                ->getTranslatedInto($view->seriesObj->Series_ID);
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
        // Special case: new publisher:
        if ($this->getRequest()->isPost()) {
            $table = $this->getDbTable('seriesalttitles');
            $row = $table->createRow();
            $row->Series_ID = $this->params()->fromRoute('id');
            $row->Note_ID = $this->params()->fromPost('note_id');
            $row->Series_AltName = $this->params()->fromPost('title');
            if (empty($row->Series_AltName)) {
                return $this->jsonDie('Title must not be empty.');
            }
            $table->insert((array)$row);
            return $this->jsonReportSuccess();
        } else {
            // Otherwise, treat this as a generic link:
            return $this->handleGenericLink(
                'seriesalttitles', 'Series_ID', 'Sequence_ID',
                'series_alt_titles', 'getAltTitles',
                'geeby-deeby/edit-series/alt-title-list.phtml'
            );
        }
    }

    /**
     * Work with publishers
     *
     * @return mixed
     */
    public function publisherAction()
    {
        // Special case: new publisher:
        if ($this->getRequest()->isPost()) {
            $table = $this->getDbTable('seriespublishers');
            $row = $table->createRow();
            $row->Series_ID = $this->params()->fromRoute('id');
            $row->Publisher_ID = $this->params()->fromPost('publisher_id');
            $row->Country_ID = $this->params()->fromPost('country_id');
            $row->Note_ID = $this->params()->fromPost('note_id');
            $row->Imprint = $this->params()->fromPost('imprint');
            $table->insert((array)$row);
            return $this->jsonReportSuccess();
        } else {
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
            $this->getDbTable('edition')
                ->delete(array('Edition_ID' => $this->params()->fromRoute('extra')));
            return $this->jsonReportSuccess();
        }

        $series = $this->getDbTable('series')->getByPrimaryKey(
            $this->params()->fromRoute('id')
        );
        return $this->handleGenericLink(
            'edition', 'Series_ID', 'Item_ID',
            'item_list', 'getItemsForSeries',
            'geeby-deeby/edit-series/item-list.phtml',
            array('Edition_Name' => $series->Series_Name . ' edition')
        );
    }

    /**
     * Set the order of an item
     *
     * @return mixed
     */
    public function itemorderAction()
    {
        if ($this->getRequest()->isPost()) {
            $edition = $this->params()->fromPost('edition_id');
            $pos = $this->params()->fromPost('pos');
            $this->getDbTable('edition')->update(
                array('Position' => $pos), array('Edition_ID' => $edition)
            );
            return $this->jsonReportSuccess();
        }
        return $this->jsonDie('Unexpected method');
    }

    /**
     * Deal with translations
     *
     * @return mixed
     */
    public function translationAction()
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
    public function translatedfromAction()
    {
        return $this->handleGenericLink(
            'seriestranslations', 'Trans_Series_ID', 'Source_Series_ID',
            'translatedFrom', 'getTranslatedInto',
            'geeby-deeby/edit-series/trans-from-list.phtml'
        );
    }
}
