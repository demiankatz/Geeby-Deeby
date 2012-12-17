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
            $view->series_alt_titles = $this->getDbTable('seriesalttitles')
                ->getAltTitles($view->seriesObj->Series_ID);
            $view->series_materials = $this->getDbTable('seriesmaterialtypes')
                ->getMaterials($view->seriesObj->Series_ID);
            $view->series_publishers = $this->getDbTable('seriespublishers')
                ->getPublishers($view->seriesObj->Series_ID);
            $view->setTemplate('geeby-deeby/edit-series/edit-full');
        }

        return $view;
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
}
