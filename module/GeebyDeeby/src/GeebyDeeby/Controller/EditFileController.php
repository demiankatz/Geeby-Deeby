<?php
/**
 * Edit file controller
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
 * Edit file controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditFileController extends AbstractBase
{
    /**
     * Display a list of files
     *
     * @return mixed
     */
    public function listAction()
    {
        $view = $this->getGenericList(
            'file', 'files', 'geeby-deeby/edit-file/render-files'
        );
        // If this is not an AJAX request, we also want to display roles:
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $view->fileTypes = $this->typelistAction()->fileTypes;
        }
        return $view;
    }

    /**
     * Operate on a single person
     *
     * @return mixed
     */
    public function indexAction()
    {
        $assignMap = [
            'file_name' => 'File_Name', 'path' => 'File_Path',
            'desc' => 'Description', 'type_id' => 'File_Type_ID'
        ];
        [$view, $ok] = $this->handleGenericItem('file', $assignMap, 'file');
        if (!$ok) {
            return $view;
        }
        $view->fileTypes = $this->typelistAction()->fileTypes;
        // Add extra fields/controls if outside of a lightbox:
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $view->itemsFiles = $this->getDbTable('itemsfiles')
                ->getItemsForFile($view->fileObj->File_ID);
            $view->peopleFiles = $this->getDbTable('peoplefiles')
                ->getPeopleForFile($view->fileObj->File_ID);
            $view->seriesFiles = $this->getDbTable('seriesfiles')
                ->getSeriesForFile($view->fileObj->File_ID);
            $view->setTemplate('geeby-deeby/edit-file/edit-full');
        }
        return $view;
    }

    /**
     * Display a list of types
     *
     * @return mixed
     */
    public function typelistAction()
    {
        return $this->getGenericList(
            'fileType', 'fileTypes', 'geeby-deeby/edit-file/render-types'
        );
    }

    /**
     * Operate on a single type
     *
     * @return mixed
     */
    public function typeAction()
    {
        $assignMap = ['fileType' => 'File_Type'];
        [$response] = $this->handleGenericItem('fileType', $assignMap, 'fileType');
        return $response;
    }

    /**
     * Deal with file/item links
     *
     * @return mixed
     */
    public function itemAction()
    {
        return $this->handleGenericLink(
            'itemsfiles', 'File_ID', 'Item_ID',
            'itemsFiles', 'getItemsForFile',
            'geeby-deeby/edit-file/item-list.phtml'
        );
    }

    /**
     * Deal with file/item links
     *
     * @return mixed
     */
    public function personAction()
    {
        return $this->handleGenericLink(
            'peoplefiles', 'File_ID', 'Person_ID',
            'peopleFiles', 'getPeopleForFile',
            'geeby-deeby/edit-file/person-list.phtml'
        );
    }

    /**
     * Deal with file/series links
     *
     * @return mixed
     */
    public function seriesAction()
    {
        return $this->handleGenericLink(
            'seriesfiles', 'File_ID', 'Series_ID',
            'seriesFiles', 'getSeriesForFile',
            'geeby-deeby/edit-file/series-list.phtml'
        );
    }
}
