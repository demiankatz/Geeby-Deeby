<?php

/**
 * Edit item creator controller
 *
 * PHP version 8
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

use GeebyDeeby\Db\Service\ItemsCreatorsCitationService;

/**
 * Edit item creator controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditItemCreatorController extends AbstractBase
{
    /**
     * Deal with citations
     *
     * @return mixed
     */
    public function citationAction()
    {
        $ok = $this->checkPermission('Content_Editor');
        if ($ok !== true) {
            return $ok;
        }
        $service = $this->getDbService(ItemsCreatorsCitationService::class);
        $creator = $this->params()->fromRoute('id');
        $citation = $this->params()->fromRoute('extra');
        try {
            if ($this->getRequest()->isPost()) {
                $service->persistEntity(
                    $service->createEntity()->setCitation((int)$citation)->setCreator((int)$creator)
                );
                return $this->jsonReportSuccess();
            } elseif ($this->getRequest()->isDelete()) {
                $service->deleteEntity($service->getByCreatorAndCitation($creator, $citation));
                return $this->jsonReportSuccess();
            }
        } catch (\Exception $e) {
            return $this->jsonDie($e->getMessage());
        }
        // Default behavior: display list:
        $view = $this->createViewModel();
        $primary = $this->params()->fromRoute('id');
        $view->selectedCitations = $service->getCitations($primary);
        $view->setTemplate('geeby-deeby/edit-item-creator/citation-list.phtml');
        $view->setTerminal(true);
        return $view;
    }
}
