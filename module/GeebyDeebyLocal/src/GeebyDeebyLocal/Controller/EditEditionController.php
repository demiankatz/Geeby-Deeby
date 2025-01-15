<?php

/**
 * Edit Edition controller
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2025.
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
 * Edit Edition controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditEditionController extends \GeebyDeeby\Controller\EditEditionController
{
    /**
     * Convert to an issue
     *
     * @return mixed
     */
    public function convertToIssueAction()
    {
        $ok = $this->checkPermission('Data_Manager');
        if ($ok !== true) {
            return $ok;
        }
        $view = $this->createViewModel();
        $editionId = $this->params()->fromRoute('id');
        $edition = $this->getDbTable('edition')->getByPrimaryKey($editionId);

        if ($this->params()->fromPost('convert')) {
            $prefix = $this->params()->fromPost('prefix');
            if (empty($prefix)) {
                $view->result = 'Prefix cannot be blank!';
                return $view;
            }
            $issueMaker = $this->serviceLocator->get(\GeebyDeebyLocal\Ingest\IssueMaker::class);
            if ($issueMaker->createIssueForWork($edition, $prefix)) {
                $view->result = 'Conversion successful';
            } else {
                $view->result = 'Conversion unsuccessful: ' . $issueMaker->getLastMessage();
            }
        } else {
            $series = $this->getDbTable('series')->getByPrimaryKey($edition->Series_ID);
            $view->prefix = $series->Series_Name . ', no. ';
        }

        $view->parent = $edition->Parent_Edition_ID;
        return $view;
    }
}
