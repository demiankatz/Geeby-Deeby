<?php

/**
 * Edit edition controller
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

use Exception;
use GeebyDeeby\Db\Service\EditionsAttributeService;
use GeebyDeeby\Db\Service\EditionsAttributesValueService;
use GeebyDeeby\Db\Service\EditionsCreditService;
use GeebyDeeby\Db\Service\EditionService;
use GeebyDeeby\Db\Service\EditionsFullTextAttributeService;
use GeebyDeeby\Db\Service\EditionsFullTextAttributesValueService;
use GeebyDeeby\Db\Service\EditionsFullTextService;
use GeebyDeeby\Db\Service\EditionsIsbnService;
use GeebyDeeby\Db\Service\EditionsOclcNumberService;
use GeebyDeeby\Db\Service\FullTextSourceService;
use GeebyDeeby\Db\Service\ItemsAltTitleService;
use GeebyDeeby\Db\Service\ItemService;
use GeebyDeeby\Db\Service\PlatformService;
use GeebyDeeby\Db\Service\RoleService;
use GeebyDeeby\Db\Service\SeriesAltTitleService;
use GeebyDeeby\Db\Service\SeriesPublisherService;
use GeebyDeeby\Db\Service\SeriesService;

use function count;
use function is_object;

/**
 * Edit edition controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditEditionController extends AbstractBase
{
    /**
     * Display a list of editions
     *
     * @return mixed
     */
    public function listAction()
    {
        return $this->getGenericList(
            EditionService::class,
            'editions',
            'geeby-deeby/edit-edition/render-editions'
        );
    }

    /**
     * Save attributes for the current edition.
     *
     * @param int   $editionId Edition ID
     * @param array $attribs   Attribute values
     *
     * @return void
     */
    protected function saveAttributes($editionId, $attribs)
    {
        $service = $this->getDbService(EditionsAttributesValueService::class);
        // Delete old values:
        $service->deleteByEdition($editionId);
        // Save new values:
        foreach ($attribs as $id => $val) {
            if (!empty($val)) {
                $entity = $service->createEntity()
                    ->setEdition((int)$editionId)
                    ->setAttribute((int)$id)
                    ->setValue($val);
                $service->persistEntity($entity);
            }
        }
    }

    /**
     * Save attributes for the current full text item.
     *
     * @param int   $rowId   Full text sequence ID
     * @param array $attribs Attribute values
     *
     * @return void
     */
    protected function saveFullTextAttributes($rowId, $attribs)
    {
        $service = $this->getDbService(EditionsFullTextAttributesValueService::class);
        // Delete old values:
        $service->deleteByEditionFullText($rowId);
        // Save new values:
        foreach ($attribs as $id => $val) {
            if (!empty($val)) {
                $entity = $service->createEntity()
                    ->setEditionFullText($rowId)
                    ->setAttribute($id)
                    ->setValue($val);
                $service->persistEntity($entity);
            }
        }
    }

    /**
     * Operate on a single edition
     *
     * @return mixed
     */
    public function indexAction()
    {
        $assignMap = [
            'name' => 'setEditionName',
            'desc' => 'setDescription',
            'item_id' => 'setItem',
            'series_id' => 'setSeries',
            'volume' => 'setVolume',
            'position' => 'setPosition',
            'replacement_number' => 'setReplacementNumber',
            'len' => 'setLength',
            'endings' => 'setEndings',
            'parent_edition_id' => 'setParentEdition',
            'position_in_parent' => 'setPositionInParent',
            'extent_in_parent' => 'setExtentInParent',
            'item_display_order' => 'setItemDisplayOrder',
        ];
        [$view, $ok] = $this->handleGenericItem(EditionService::class, $assignMap, 'edition');
        if (!$ok) {
            return $view;
        }
        $editionId = $view->affectedEntity?->getId();

        // Special handling for saving attributes:
        if (
            $this->getRequest()->isPost()
            && ($attribs = $this->params()->fromPost('attribs'))
        ) {
            $this->saveAttributes($editionId, $attribs);
        }

        // Add attribute details if we have an Edition_ID.
        if ($editionId) {
            $view->attributes = $this->getDbService(EditionsAttributeService::class)->getList();
            $attributeValues = [];
            $values = $this->getDbService(EditionsAttributesValueService::class)->getAttributesForEdition($editionId);
            foreach ($values as $current) {
                $attributeValues[$current['Editions_Attribute_ID']] = $current['Editions_Attribute_Value'];
            }
            $view->attributeValues = $attributeValues;
        }

        $ItemService = $this->getDbService(ItemService::class);

        // Add item/series details if necessary:
        if (isset($view->edition['Item_ID']) && !empty($view->edition['Item_ID'])) {
            $view->item = $ItemService->getByPrimaryKey($view->edition['Item_ID']);
            $view->itemAltTitles = $this->getDbService(ItemsAltTitleService::class)
                ->getAltTitles($view->edition['Item_ID']);
        }
        if (
            isset($view->edition['Series_ID'])
            && !empty($view->edition['Series_ID'])
        ) {
            $view->series = $this->getDbService(SeriesService::class)
                ->getByPrimaryKey($view->edition['Series_ID']);
            $view->seriesAltTitles = $this->getDbService(SeriesAltTitleService::class)
                ->getAltTitles($view->edition['Series_ID']);
            $view->publishers = $this->getDbService(SeriesPublisherService::class)
                ->getPublishersForSeries($view->edition['Series_ID']);
        }
        // Add extra fields/controls if outside of a lightbox:
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $view->roles = $this->getDbService(RoleService::class)->getList();
            $view->credits = $this->getDbService(EditionsCreditService::class)->getCreditsForEdition($editionId);
            $view->images = $this->getDbTable('editionsimages')
                ->getImagesForEdition($editionId);
            $view->ISBNs = $this->getDbService(EditionsIsbnService::class)->getISBNsForEdition($editionId);
            $view->oclcNumbers = $this->getDbService(EditionsOclcNumberService::class)
                ->getOCLCNumbersForEdition($editionId);
            $view->editionPlatforms = $this->getDbTable('editionsplatforms')
                ->getPlatformsForEdition($editionId);
            $view->platforms = $this->getDbService(PlatformService::class)->getList();
            $view->productCodes = $this->getDbTable('editionsproductcodes')
                ->getProductCodesForEdition($editionId);
            $view->releaseDates = $this->getDbTable('editionsreleasedates')
                ->getDatesForEdition($editionId);
            $view->setTemplate('geeby-deeby/edit-edition/edit-full');
            $view->fullText = $this->getDbService(EditionsFullTextService::class)
                ->getFullTextForEdition($editionId);
            $view->fullTextSources = $this->getDbService(FullTextSourceService::class)->getList();
            if (is_object($view->affectedEntity)) {
                $editionService = $this->getDbService(EditionService::class);
                $view->next = $editionService->getNextInSeries($view->affectedEntity);
                $view->previous = $editionService->getPreviousInSeries($view->affectedEntity);
            }
            $view->item_list = $ItemService->getItemsForEdition($editionId);
        }
        return $view;
    }

    /**
     * Manage preferred publisher functionality.
     *
     * @return mixed
     */
    public function preferredpublisherAction()
    {
        $ok = $this->checkPermission('Content_Editor');
        if ($ok !== true) {
            return $ok;
        }
        if ($this->getRequest()->isPost()) {
            return $this->setPreferredPublisher();
        }
        $view = $this->createViewModel();
        $view->edition = $this->getDbService(EditionService::class)
            ->getByPrimaryKey($this->params()->fromRoute('id'))
            ->toArray();
        $view->publishers = $this->getDbService(SeriesPublisherService::class)
            ->getPublishersForSeries($view->edition['Series_ID']);
        $view->selected = $view->edition['Preferred_Series_Publisher_ID'];
        $view->setTemplate('geeby-deeby/edit-edition/series-publisher-select.phtml');
        $view->setTerminal(true);
        return $view;
    }

    /**
     * Set a preferred publisher
     *
     * @return mixed
     */
    protected function setPreferredPublisher()
    {
        $editionId = $this->params()->fromRoute('id');
        $editionService = $this->getDbService(EditionService::class);
        $pubId = $this->params()->fromPost('pub_id');
        $edition = $editionService->getByPrimaryKey($editionId);
        $edition->setPreferredPublisher(empty($pubId) ? null : (int)$pubId);
        $editionService->persistEntity($edition);
        return $this->jsonReportSuccess();
    }

    /**
     * Manage the drop-down of item alt titles
     *
     * @return mixed
     */
    public function preferreditemtitleAction()
    {
        $ok = $this->checkPermission('Content_Editor');
        if ($ok !== true) {
            return $ok;
        }
        if ($this->getRequest()->isPost()) {
            return $this->setPreferredItemTitle();
        }
        if ($this->getRequest()->isDelete()) {
            return $this->clearPreferredItemTitle();
        }
        $view = $this->createViewModel();
        $view->edition = $this->getDbService(EditionService::class)
            ->getByPrimaryKey($this->params()->fromRoute('id'))
            ->toArray();
        $view->itemAltTitles = $this->getDbService(ItemsAltTitleService::class)
            ->getAltTitles($view->edition['Item_ID']);
        $view->selected = $view->edition['Preferred_Item_AltName_ID'];
        $view->setTemplate('geeby-deeby/edit-edition/item-alt-title-select.phtml');
        $view->setTerminal(true);
        return $view;
    }

    /**
     * Set a preferred item title
     *
     * @return mixed
     */
    protected function setPreferredItemTitle()
    {
        $editionService = $this->getDbService(EditionService::class);
        $editionId = $this->params()->fromRoute('id');
        $edition = $editionService->getByPrimaryKey($editionId);
        $titleId = $this->params()->fromPost('title_id', 'NEW');
        $titleText = trim($this->params()->fromPost('title_text'));

        if ($titleId == 'NEW') {
            if (empty($titleText)) {
                return $this->jsonDie('Title cannot be empty.');
            } else {
                $service = $this->getDbService(ItemsAltTitleService::class);
                $item = $edition->getItem();
                $result = $service->getByItemAndTitle($item, $titleText);
                if (!$result) {
                    $result = $service->createEntity()
                        ->setItem($item)
                        ->setAltName($titleText);
                    $service->persistEntity($result);
                }
                $titleId = $result->getId();
                if (!$titleId) {
                    return $this->jsonDie('Problem inserting title.');
                }
            }
        }
        $edition->setPreferredItemAlternateTitle($titleId);
        $editionService->persistEntity($edition);
        return $this->jsonReportSuccess();
    }

    /**
     * Clear a preferred item title
     *
     * @return mixed
     */
    protected function clearPreferredItemTitle()
    {
        $editionService = $this->getDbService(EditionService::class);
        $editionId = $this->params()->fromRoute('id');
        $edition = $editionService->getByPrimaryKey($editionId);
        $edition->setPreferredItemAlternateTitle(null);
        $editionService->persistEntity($edition);
        return $this->jsonReportSuccess();
    }

    /**
     * Handle the preferred series title controls.
     *
     * @return mixed
     */
    public function preferredseriestitleAction()
    {
        $ok = $this->checkPermission('Content_Editor');
        if ($ok !== true) {
            return $ok;
        }
        if ($this->getRequest()->isPost()) {
            return $this->setPreferredSeriesTitle();
        }
        if ($this->getRequest()->isDelete()) {
            return $this->clearPreferredSeriesTitle();
        }
        $view = $this->createViewModel();
        $view->edition = $this->getDbService(EditionService::class)
            ->getByPrimaryKey($this->params()->fromRoute('id'))
            ->toArray();
        $view->seriesAltTitles = $this->getDbService(SeriesAltTitleService::class)
            ->getAltTitles($view->edition['Series_ID']);
        $view->selected = $view->edition['Preferred_Series_AltName_ID'];
        $view->setTemplate('geeby-deeby/edit-edition/series-alt-title-select.phtml');
        $view->setTerminal(true);
        return $view;
    }

    /**
     * Set a preferred series title
     *
     * @return mixed
     */
    protected function setPreferredSeriesTitle()
    {
        $editionService = $this->getDbService(EditionService::class);
        $editionId = $this->params()->fromRoute('id');
        $edition = $editionService->getByPrimaryKey($editionId);
        $titleId = $this->params()->fromPost('title_id', 'NEW');
        $titleText = trim($this->params()->fromPost('title_text'));

        if ($titleId == 'NEW') {
            if (empty($titleText)) {
                return $this->jsonDie('Title cannot be empty.');
            } else {
                $service = $this->getDbService(SeriesAltTitleService::class);
                $series = $edition->getSeries();
                if (!$series) {
                    return $this->jsonDie(
                        'Edition must be attached to a Series.'
                    );
                }
                $entity  = $service->getBySeriesAndTitle($series, $titleText);
                if (!$entity) {
                    $entity = $service->createEntity()
                        ->setSeries($edition->Series_ID)
                        ->setAltName($titleText);
                    $service->persistEntity($entity);
                }
                $titleId = $entity->getId();
                if (empty($titleId)) {
                    return $this->jsonDie('Problem inserting title.');
                }
            }
        }
        $edition->setPreferredSeriesAlternateTitle($titleId);
        $editionService->persistEntity($edition);
        return $this->jsonReportSuccess();
    }

    /**
     * Clear a preferred series title
     *
     * @return mixed
     */
    protected function clearPreferredSeriesTitle()
    {
        $editionService = $this->getDbService(EditionService::class);
        $editionId = $this->params()->fromRoute('id');
        $edition = $editionService->getByPrimaryKey($editionId);
        $edition->setPreferredSeriesAlternateTitle(null);
        $editionService->persistEntity($edition);
        return $this->jsonReportSuccess();
    }

    /**
     * Copy an edition
     *
     * @return mixed
     */
    public function copyAction()
    {
        $ok = $this->checkPermission('Content_Editor');
        if ($ok !== true) {
            return $ok;
        }
        if ($this->getRequest()->isPost()) {
            $editionId = $this->params()->fromRoute('id');
            $service = $this->getDbService(EditionService::class);
            $old = $service->getByPrimaryKey($editionId);
            if (!$old) {
                return $this->jsonDie('Cannot load edition ' . $editionId);
            }
            return $service->copyEdition($old)
                ? $this->jsonReportSuccess()
                : $this->jsonDie('Copy operation failed.');
        }
        return $this->jsonDie('Unexpected method');
    }

    /**
     * Manage date links.
     *
     * @return mixed
     */
    public function dateAction()
    {
        // Only content editors are allowed to do this....
        $ok = $this->checkPermission('Content_Editor');
        if ($ok !== true) {
            return $ok;
        }
        // If this is a POST, we're adding a date....
        if ($this->getRequest()->isPost()) {
            return $this->addDate();
        }
        // If this is a DELETE, we're removing a date....
        if ($this->getRequest()->isDelete()) {
            return $this->deleteDate();
        }
        // Default action: list dates:
        $table = $this->getDbTable('editionsreleasedates');
        $view = $this->createViewModel();
        $primary = $this->params()->fromRoute('id');
        $view->releaseDates = $table->getDatesForEdition($primary);
        $view->setTemplate('geeby-deeby/edit-edition/date-list.phtml');
        $view->setTerminal(true);
        return $view;
    }

    /**
     * Get next/previous links
     *
     * @return mixed
     */
    public function nextandprevAction()
    {
        $primary = $this->params()->fromRoute('id');
        $editionService = $this->getDbService(EditionService::class);
        $edition = $editionService->getByPrimaryKey($primary);
        $view = $this->createViewModel();
        $view->next = $editionService->getNextInSeries($edition);
        $view->previous = $editionService->getPreviousInSeries($edition);
        $view->setTemplate('geeby-deeby/edit-edition/next-and-prev.phtml');
        $view->setTerminal(true);
        return $view;
    }

    /**
     * Add a date
     *
     * @return mixed
     */
    protected function addDate()
    {
        $table = $this->getDbTable('editionsreleasedates');
        $row = $table->createRow();
        $row->Edition_ID = $this->params()->fromRoute('id');
        $row->Year = $this->params()->fromPost('year');
        $row->Month = $this->params()->fromPost('month');
        $row->Day = $this->params()->fromPost('day');
        $row->Note_ID = $this->params()->fromPost('note_id');
        if (empty($row->Note_ID)) {
            $row->Note_ID = null;
        }
        try {
            $table->insert($row->toArray());
        } catch (\Exception $e) {
            return $this->jsonDie($e->getMessage());
        }
        return $this->jsonReportSuccess();
    }

    /**
     * Remove a date
     *
     * @return mixed
     */
    protected function deleteDate()
    {
        [$year, $month, $day]
            = explode(',', $this->params()->fromRoute('extra'));
        $this->getDbTable('editionsreleasedates')->delete(
            [
                'Edition_ID' => $this->params()->fromRoute('id'),
                'Year' => $year,
                'Month' => $month,
                'Day' => $day,
            ]
        );
        return $this->jsonReportSuccess();
    }

    /**
     * Manage credits
     *
     * @return mixed
     */
    public function creditAction()
    {
        $ok = $this->checkPermission('Content_Editor');
        if ($ok !== true) {
            return $ok;
        }
        $service = $this->getDbService(EditionsCreditService::class);
        // POST action:
        if ($this->getRequest()->isPost()) {
            $note = $this->params()->fromPost('note_id');
            $entity = $service->createEntity()
                ->setEdition((int)$this->params()->fromRoute('id'))
                ->setPerson((int)$this->params()->fromPost('person_id'))
                ->setRole((int)$this->params()->fromPost('role_id'))
                ->setPosition((int)$this->params()->fromPost('pos'))
                ->setNote($note ? (int)$note : null);
            try {
                $service->persistEntity($entity);
            } catch (Exception $e) {
                return $this->jsonDie($e->getMessage());
            }
            return $this->jsonReportSuccess();
        }
        // DELETE action:
        if ($this->getRequest()->isDelete()) {
            [$person, $role] = explode(',', $this->params()->fromRoute('extra'));
            if ($entity = $service->getByEditionAndPersonAndRole($this->params()->fromRoute('id'), $person, $role)) {
                $service->deleteEntity($entity);
            }
            return $this->jsonReportSuccess();
        }
        // Default behavior: show list:
        $view = $this->createViewModel();
        $primary = $this->params()->fromRoute('id');
        $view->credits = $service->getCreditsForEdition($primary);
        $view->setTemplate('geeby-deeby/edit-edition/credits.phtml');
        $view->setTerminal(true);
        return $view;
    }

    /**
     * Set the order of an attached credit
     *
     * @return mixed
     */
    public function creditorderAction()
    {
        $ok = $this->checkPermission('Content_Editor');
        if ($ok !== true) {
            return $ok;
        }
        if ($this->getRequest()->isPost()) {
            $service = $this->getDbService(EditionsCreditService::class);
            $entity = $service->getByEditionAndPersonAndRole(
                $this->params()->fromRoute('id'),
                $this->params()->fromPost('person_id'),
                $this->params()->fromPost('role_id')
            );
            if ($entity) {
                $entity->setPosition($this->params()->fromPost('pos'));
                $service->persistEntity($entity);
            }
            return $this->jsonReportSuccess();
        }
        return $this->jsonDie('Unexpected method');
    }

    /**
     * Support method for fulltextAction()
     *
     * @return mixed
     */
    protected function modifyFullText()
    {
        $rowId = $this->params()->fromRoute('extra');
        $service = $this->getDbService(EditionsFullTextService::class);
        $entity = $service->getByPrimaryKey($rowId);
        if ($this->getRequest()->isPost()) {
            $entity->setFullTextSource($this->params()->fromPost('source_id'))
                ->setUrl(trim($this->params()->fromPost('url')));
            if (!$entity->getUrl()) {
                return $this->jsonDie('URL must not be empty.');
            }
            $service->persistEntity($entity);
            if ($attribs = $this->params()->fromPost('attribs')) {
                $this->saveFullTextAttributes($rowId, $attribs);
            }
            return $this->jsonReportSuccess();
        }
        $view = $this->createViewModel();
        $view->fullTextSources = $this->getDbService(FullTextSourceService::class)->getList();
        $view->row = $entity->toArray();
        $view->attributes = $this->getDbService(EditionsFullTextAttributeService::class)->getList();
        $attributeValues = [];
        $values = $this->getDbService(EditionsFullTextAttributesValueService::class)
            ->getAttributesForFullTextIDs($rowId);
        foreach ($values as $current) {
            $attributeValues[$current->Editions_Full_Text_Attribute_ID]
                = $current->Editions_Full_Text_Attribute_Value;
        }
        $view->attributeValues = $attributeValues;
        $view->setTemplate('geeby-deeby/edit-edition/modify-full-text');

        // If this is an AJAX request, render the core list only, not the
        // framing layout and buttons.
        if ($this->getRequest()->isXmlHttpRequest()) {
            $view->setTerminal(true);
        }

        return $view;
    }

    /**
     * Deal with full text
     *
     * @return mixed
     */
    public function fulltextAction()
    {
        $ok = $this->checkPermission('Content_Editor');
        if ($ok !== true) {
            return $ok;
        }
        // Modify the full text if it's a GET/POST and has an extra set.
        if (
            ($this->getRequest()->isPost() || $this->getRequest()->isGet())
            && null !== $this->params()->fromRoute('extra')
            && 'NEW' !== $this->params()->fromRoute('extra')
        ) {
            return $this->modifyFullText();
        }

        $service = $this->getDbService(EditionsFullTextService::class);
        if ($this->getRequest()->isPost()) {
            $entity = $service->createEntity()
                ->setFullTextSource($this->params()->fromPost('source_id'))
                ->setEdition($this->params()->fromRoute('id'))
                ->setUrl(trim($this->params()->fromPost('url')));
            if (!$entity->getUrl()) {
                return $this->jsonDie('URL must not be empty.');
            }
            $service->persistEntity($entity);
            return $this->jsonReportSuccess();
        } elseif ($this->getRequest()->isDelete()) {
            $delete = $this->params()->fromRoute('extra');
            try {
                $service->deleteEntity($service->getByPrimaryKey($delete));
            } catch (Exception $e) {
                return $this->jsonDie($e->getMessage());
            }
            return $this->jsonReportSuccess();
        }
        // Default behavior: display list:
        $view = $this->createViewModel();
        $primary = $this->params()->fromRoute('id');
        $view->fullText = $service->getFullTextForEdition($primary);
        $view->setTemplate('geeby-deeby/edit-edition/fulltext-list.phtml');
        $view->setTerminal(true);
        return $view;
    }

    /**
     * Work with ISBNs
     *
     * @return mixed
     */
    public function isbnAction()
    {
        // Special case: new ISBN:
        if ($this->getRequest()->isPost()) {
            $ok = $this->checkPermission('Content_Editor');
            if ($ok !== true) {
                return $ok;
            }
            $isbn = new \VuFindCode\ISBN($this->params()->fromPost('isbn'));
            if (!$isbn->isValid()) {
                return $this->jsonDie('Invalid ISBN -- cannot save.');
            }
            $service = $this->getDbService(EditionsIsbnService::class);
            $note = $this->params()->fromPost('note_id');
            $entity = $service->createEntity()
                ->setEdition($this->params()->fromRoute('id'))
                ->setNote(empty($note) ? null : (int)$note);
            $isbn10 = $isbn->get10();
            if (!empty($isbn10)) {
                $entity->setIsbn10($isbn10);
            }
            $entity->setIsbn13($isbn->get13());
            $service->persistEntity($entity);
            return $this->jsonReportSuccess();
        } else {
            // Otherwise, treat this as a generic link:
            return $this->handleGenericLink(
                EditionsIsbnService::class,
                null,
                null,
                'ISBNs',
                'getISBNsForEdition',
                'geeby-deeby/edit-edition/isbn-list.phtml',
                retrieveLinkMethod: 'getByPrimaryKey',
                invertRetrieveLinkParams: true
            );
        }
    }

    /**
     * Work with OCLC numbers
     *
     * @return mixed
     */
    public function oclcnumberAction()
    {
        // Special case: new code:
        if ($this->getRequest()->isPost()) {
            $ok = $this->checkPermission('Content_Editor');
            if ($ok !== true) {
                return $ok;
            }
            $service = $this->getDbService(EditionsOclcNumberService::class);
            $note = $this->params()->fromPost('note_id');
            $entity = $service->createEntity()
                ->setEdition($this->params()->fromRoute('id'))
                ->setNote(empty($note) ? null : (int)$note)
                ->setOclcNumber(trim($this->params()->fromPost('oclc_number')));
            if (!$entity->getOclcNumber()) {
                return $this->jsonDie('OCLC number must not be empty.');
            }
            $service->persistEntity($entity);
            return $this->jsonReportSuccess();
        } else {
            // Otherwise, treat this as a generic link:
            return $this->handleGenericLink(
                EditionsOclcNumberService::class,
                null,
                null,
                'oclcNumbers',
                'getOCLCNumbersForEdition',
                'geeby-deeby/edit-edition/oclc-number-list.phtml',
                retrieveLinkMethod: 'getByPrimaryKey',
                invertRetrieveLinkParams: true
            );
        }
    }

    /**
     * Work with product codes
     *
     * @return mixed
     */
    public function productcodeAction()
    {
        // Special case: new code:
        if ($this->getRequest()->isPost()) {
            $ok = $this->checkPermission('Content_Editor');
            if ($ok !== true) {
                return $ok;
            }
            $table = $this->getDbTable('editionsproductcodes');
            $row = $table->createRow();
            $row->Edition_ID = $this->params()->fromRoute('id');
            $row->Note_ID = $this->params()->fromPost('note_id');
            if (empty($row->Note_ID)) {
                $row->Note_ID = null;
            }
            $row->Product_Code = $this->params()->fromPost('code');
            if (empty($row->Product_Code)) {
                return $this->jsonDie('Product code must not be empty.');
            }
            $table->insert($row->toArray());
            return $this->jsonReportSuccess();
        } else {
            // Otherwise, treat this as a generic link:
            return $this->handleGenericLink(
                'editionsproductcodes',
                'Edition_ID',
                'Sequence_ID',
                'productCodes',
                'getProductCodesForEdition',
                'geeby-deeby/edit-edition/product-code-list.phtml'
            );
        }
    }

    /**
     * Work with images
     *
     * @return mixed
     */
    public function imageAction()
    {
        // Special case: new image:
        if ($this->getRequest()->isPost()) {
            $ok = $this->checkPermission('Content_Editor');
            if ($ok !== true) {
                return $ok;
            }
            $table = $this->getDbTable('editionsimages');
            $row = $table->createRow();
            $row->Edition_ID = $this->params()->fromRoute('id');
            $row->Note_ID = $this->params()->fromPost('note_id');
            if (empty($row->Note_ID)) {
                $row->Note_ID = null;
            }
            $row->Image_Path = $this->params()->fromPost('image');
            $row->IIIF_URI = $this->params()->fromPost('iiif');
            if (empty($row->Image_Path) && empty($row->IIIF_URI)) {
                return $this->jsonDie('Image path or IIIF URI must be set.');
            }
            $row->Thumb_Path = $this->params()->fromPost('thumb');
            // Build thumb path if none was provided:
            if (
                empty($row->Thumb_Path) && empty($row->IIIF_URI)
                && !empty($row->Image_Path)
            ) {
                $parts = explode('.', $row->Image_Path);
                $nextToLast = count($parts) - 2;
                $parts[$nextToLast] .= 'thumb';
                $row->Thumb_Path = implode('.', $parts);
            }
            $row->Position = $this->params()->fromPost('pos');
            $table->insert($row->toArray());
            return $this->jsonReportSuccess();
        } else {
            // Otherwise, treat this as a generic link:
            return $this->handleGenericLink(
                'editionsimages',
                'Edition_ID',
                'Sequence_ID',
                'images',
                'getImagesForEdition',
                'geeby-deeby/edit-edition/image-list.phtml'
            );
        }
    }

    /**
     * Set the order of an attached image
     *
     * @return mixed
     */
    public function imageorderAction()
    {
        $ok = $this->checkPermission('Content_Editor');
        if ($ok !== true) {
            return $ok;
        }
        if ($this->getRequest()->isPost()) {
            $image = $this->params()->fromPost('sequence_id');
            $pos = $this->params()->fromPost('pos');
            $this->getDbTable('editionsimages')->update(
                ['Position' => $pos],
                ['Sequence_ID' => $image]
            );
            return $this->jsonReportSuccess();
        }
        return $this->jsonDie('Unexpected method');
    }

    /**
     * Deal with platforms
     *
     * @return mixed
     */
    public function platformAction()
    {
        return $this->handleGenericLink(
            'editionsplatforms',
            'Edition_ID',
            'Platform_ID',
            'editionPlatforms',
            'getPlatformsForEdition',
            'geeby-deeby/edit-edition/platform-list.phtml'
        );
    }

    /**
     * Deal with items
     *
     * @return mixed
     */
    public function itemAction()
    {
        $editionService = $this->getDbService(EditionService::class);

        // Special case: delete editions differently from other links:
        if ($this->getRequest()->isDelete()) {
            $ok = $this->checkPermission('Content_Editor');
            if ($ok !== true) {
                return $ok;
            }
            try {
                $editionService->safeDelete($this->params()->fromRoute('extra'));
            } catch (\Exception $e) {
                return $this->jsonDie($e->getMessage());
            }
            return $this->jsonReportSuccess();
        }

        $parentEdition = $editionService->getByPrimaryKey($this->params()->fromRoute('id'));
        $edName = $parentEdition->getEditionName();
        $series = $parentEdition->getSeries();
        return $this->handleGenericLink(
            EditionService::class,
            'setParentEdition',
            'setItem',
            'item_list',
            'getItemsForEdition',
            'geeby-deeby/edit-edition/item-list.phtml',
            ['setEditionName' => $edName, 'setSeries' => $series],
            [$editionService, 'insertChildEditionCallback']
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
            $pos = $this->params()->fromPost('pos');
            $editionService = $this->getDbService(EditionService::class);
            $edition = $editionService->getByPrimaryKey($this->params()->fromPost('edition_id'))
                ->setPositionInParent((int)$pos);
            $editionService->persistEntity($edition);
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
            'edit/edition',
            [
                'action' => 'index',
                'id' => $this->params()->fromRoute('id'),
                'extra' => $this->params()->fromRoute('extra'),
            ]
        );
    }
}
