<?php

/**
 * Edit file controller
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

use GeebyDeeby\Db\Service\FileService;
use GeebyDeeby\Db\Service\FileTypeService;

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
            FileService::class,
            'files',
            'geeby-deeby/edit-file/render-files'
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
            'file_name' => 'setFileName',
            'path' => 'setFilePath',
            'desc' => 'setDescription',
            'type_id' => 'setFileType',
        ];
        [$view, $ok] = $this->handleGenericItem(FileService::class, $assignMap, 'file');
        if (!$ok) {
            return $view;
        }
        $view->fileTypes = $this->typelistAction()->fileTypes;
        // Add extra fields/controls if outside of a lightbox:
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $fileId = $view->affectedEntity->getId();
            $view->itemsFiles = $this->getDbTable('itemsfiles')->getItemsForFile($fileId);
            $view->peopleFiles = $this->getDbTable('peoplefiles')->getPeopleForFile($fileId);
            $view->seriesFiles = $this->getDbTable('seriesfiles')->getSeriesForFile($fileId);
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
            FileTypeService::class,
            'fileTypes',
            'geeby-deeby/edit-file/render-types'
        );
    }

    /**
     * Operate on a single type
     *
     * @return mixed
     */
    public function typeAction()
    {
        $assignMap = ['fileType' => 'setFileTypeName'];
        [$response] = $this->handleGenericItem(FileTypeService::class, $assignMap, 'fileType');
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
            'itemsfiles',
            'File_ID',
            'Item_ID',
            'itemsFiles',
            'getItemsForFile',
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
            'peoplefiles',
            'File_ID',
            'Person_ID',
            'peopleFiles',
            'getPeopleForFile',
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
            'seriesfiles',
            'File_ID',
            'Series_ID',
            'seriesFiles',
            'getSeriesForFile',
            'geeby-deeby/edit-file/series-list.phtml'
        );
    }
}
