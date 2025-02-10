<?php

/**
 * Edit edition controller
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
            'edition',
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
        $table = $this->getDbTable('editionsattributesvalues');
        // Delete old values:
        $table->delete(['Edition_ID' => $editionId]);
        // Save new values:
        foreach ($attribs as $id => $val) {
            if (!empty($val)) {
                $table->insert(
                    [
                        'Edition_ID' => $editionId,
                        'Editions_Attribute_ID' => $id,
                        'Editions_Attribute_Value' => $val,
                    ]
                );
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
        $table = $this->getDbTable('editionsfulltextattributesvalues');
        // Delete old values:
        $table->delete(['Editions_Full_Text_ID' => $rowId]);
        // Save new values:
        foreach ($attribs as $id => $val) {
            if (!empty($val)) {
                $table->insert(
                    [
                        'Editions_Full_Text_ID' => $rowId,
                        'Editions_Full_Text_Attribute_ID' => $id,
                        'Editions_Full_Text_Attribute_Value' => $val,
                    ]
                );
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
            'name' => 'Edition_Name',
            'desc' => 'Edition_Description',
            'item_id' => 'Item_ID',
            'series_id' => 'Series_ID',
            'volume' => 'Volume',
            'position' => 'Position',
            'replacement_number' => 'Replacement_Number',
            'len' => 'Edition_Length',
            'endings' => 'Edition_Endings',
            'parent_edition_id' => 'Parent_Edition_ID',
            'position_in_parent' => 'Position_In_Parent',
            'extent_in_parent' => 'Extent_In_Parent',
            'item_display_order' => 'Item_Display_Order',
        ];
        [$view, $ok] = $this->handleGenericItem('edition', $assignMap, 'edition');
        if (!$ok) {
            return $view;
        }
        $editionId = $view->edition['Edition_ID']
            ?? $view->affectedRow->Edition_ID
            ?? null;

        // Special handling for saving attributes:
        if (
            $this->getRequest()->isPost()
            && ($attribs = $this->params()->fromPost('attribs'))
        ) {
            $this->saveAttributes($editionId, $attribs);
        }

        // Add attribute details if we have an Edition_ID.
        if ($editionId) {
            $view->attributes = $this->getDbTable('editionsattribute')->getList();
            $attributeValues = [];
            $values = $this->getDbTable('editionsattributesvalues')
                ->getAttributesForEdition($editionId);
            foreach ($values as $current) {
                $attributeValues[$current->Editions_Attribute_ID]
                    = $current->Editions_Attribute_Value;
            }
            $view->attributeValues = $attributeValues;
        }

        $itemTable = $this->getDbTable('item');

        // Add item/series details if necessary:
        if (isset($view->edition['Item_ID']) && !empty($view->edition['Item_ID'])) {
            $view->item = $itemTable->getByPrimaryKey($view->edition['Item_ID']);
            $view->itemAltTitles = $this->getDbTable('itemsalttitles')
                ->getAltTitles($view->edition['Item_ID']);
        }
        if (
            isset($view->edition['Series_ID'])
            && !empty($view->edition['Series_ID'])
        ) {
            $view->series = $this->getDbTable('series')
                ->getByPrimaryKey($view->edition['Series_ID']);
            $view->seriesAltTitles = $this->getDbTable('seriesalttitles')
                ->getAltTitles($view->edition['Series_ID']);
            $view->publishers = $this->getDbTable('seriespublishers')
                ->getPublishers($view->edition['Series_ID']);
        }
        // Add extra fields/controls if outside of a lightbox:
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $view->roles = $this->getDbTable('role')->getList();
            $view->credits = $this->getDbTable('editionscredits')
                ->getCreditsForEdition($editionId);
            $view->images = $this->getDbTable('editionsimages')
                ->getImagesForEdition($editionId);
            $view->ISBNs = $this->getDbTable('editionsisbns')
                ->getISBNsForEdition($editionId);
            $view->oclcNumbers = $this->getDbTable('editionsoclcnumbers')
                ->getOCLCNumbersForEdition($editionId);
            $view->editionPlatforms = $this->getDbTable('editionsplatforms')
                ->getPlatformsForEdition($editionId);
            $view->platforms = $this->getDbTable('platform')->getList();
            $view->productCodes = $this->getDbTable('editionsproductcodes')
                ->getProductCodesForEdition($editionId);
            $view->releaseDates = $this->getDbTable('editionsreleasedates')
                ->getDatesForEdition($editionId);
            $view->setTemplate('geeby-deeby/edit-edition/edit-full');
            $view->fullText = $this->getDbTable('editionsfulltext')
                ->getFullTextForEdition($editionId);
            $view->fullTextSources = $this->getDbTable('fulltextsource')
                ->getList();
            if (is_object($view->editionObj)) {
                $view->next = $view->editionObj->getNextInSeries();
                $view->previous = $view->editionObj->getPreviousInSeries();
            }
            $view->item_list = $itemTable
                ->getItemsForEdition($editionId);
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
        $view->edition = $this->getDbTable('edition')
            ->getByPrimaryKey($this->params()->fromRoute('id'));
        $view->publishers = $this->getDbTable('seriespublishers')
            ->getPublishers($view->edition['Series_ID']);
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
        $edition = $this->getDbTable('edition')->getByPrimaryKey($editionId);
        $pubId = $this->params()->fromPost('pub_id');
        $edition->Preferred_Series_Publisher_ID = empty($pubId) ? null : $pubId;
        $edition->save();
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
        $view->edition = $this->getDbTable('edition')
            ->getByPrimaryKey($this->params()->fromRoute('id'));
        $view->itemAltTitles = $this->getDbTable('itemsalttitles')
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
        $editionId = $this->params()->fromRoute('id');
        $edition = $this->getDbTable('edition')->getByPrimaryKey($editionId);
        $title = $this->params()->fromPost('title_id', 'NEW');
        $titleText = trim($this->params()->fromPost('title_text'));

        if ($title == 'NEW') {
            if (empty($titleText)) {
                return $this->jsonDie('Title cannot be empty.');
            } else {
                $table = $this->getDbTable('itemsalttitles');
                $results = $table->select(
                    [
                        'Item_AltName' => $titleText,
                        'Item_ID' => $edition->Item_ID,
                    ]
                );
                if (count($results) == 0) {
                    $row = $table->createRow();
                    $row->Item_ID = $edition->Item_ID;
                    if (empty($row->Item_ID)) {
                        return $this->jsonDie(
                            'Edition must be attached to an Item.'
                        );
                    }
                    $row->Item_AltName = $titleText;
                    $table->insert((array)$row);
                    $results = $table->select((array)$row);
                }
                foreach ($results as $result) {
                    $result = (array)$result;
                    $title = $result['Sequence_ID'];
                }
                if (empty($title)) {
                    return $this->jsonDie('Problem inserting title.');
                }
            }
        }
        $edition->Preferred_Item_AltName_ID = $title;
        $edition->save();
        return $this->jsonReportSuccess();
    }

    /**
     * Clear a preferred item title
     *
     * @return mixed
     */
    protected function clearPreferredItemTitle()
    {
        $editionId = $this->params()->fromRoute('id');
        $edition = $this->getDbTable('edition')->getByPrimaryKey($editionId);
        $edition->Preferred_Item_AltName_ID = null;
        $edition->save();
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
        $view->edition = $this->getDbTable('edition')
            ->getByPrimaryKey($this->params()->fromRoute('id'));
        $view->seriesAltTitles = $this->getDbTable('seriesalttitles')
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
        $editionId = $this->params()->fromRoute('id');
        $edition = $this->getDbTable('edition')->getByPrimaryKey($editionId);
        $title = $this->params()->fromPost('title_id', 'NEW');
        $titleText = trim($this->params()->fromPost('title_text'));

        if ($title == 'NEW') {
            if (empty($titleText)) {
                return $this->jsonDie('Title cannot be empty.');
            } else {
                $table = $this->getDbTable('seriesalttitles');
                $results = $table->select(
                    [
                        'Series_AltName' => $titleText,
                        'Series_ID' => $edition->Series_ID,
                    ]
                );
                if (count($results) == 0) {
                    $row = $table->createRow();
                    $row->Series_ID = $edition->Series_ID;
                    if (empty($row->Series_ID)) {
                        return $this->jsonDie(
                            'Edition must be attached to a Series.'
                        );
                    }
                    $row->Series_AltName = $titleText;
                    $table->insert((array)$row);
                    $results = $table->select((array)$row);
                }
                foreach ($results as $result) {
                    $result = (array)$result;
                    $title = $result['Sequence_ID'];
                }
                if (empty($title)) {
                    return $this->jsonDie('Problem inserting title.');
                }
            }
        }
        $edition->Preferred_Series_AltName_ID = $title;
        $edition->save();
        return $this->jsonReportSuccess();
    }

    /**
     * Clear a preferred series title
     *
     * @return mixed
     */
    protected function clearPreferredSeriesTitle()
    {
        $editionId = $this->params()->fromRoute('id');
        $edition = $this->getDbTable('edition')->getByPrimaryKey($editionId);
        $edition->Preferred_Series_AltName_ID = null;
        $edition->save();
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
            $table = $this->getDbTable('edition');
            $old = $table->getByPrimaryKey($editionId);
            if (!$old) {
                return $this->jsonDie('Cannot load edition ' . $editionId);
            }
            if ($old->copy()) {
                return $this->jsonReportSuccess();
            } else {
                return $this->jsonDie('Copy operation failed.');
            }
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
        $table = $this->getDbTable('edition');
        $view = $this->createViewModel();
        $primary = $this->params()->fromRoute('id');
        $edition = $table->getByPrimaryKey($primary);
        $view->next = $edition->getNextInSeries();
        $view->previous = $edition->getPreviousInSeries();
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
            $table->insert((array)$row);
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
        // POST action:
        if ($this->getRequest()->isPost()) {
            $table = $this->getDbTable('editionscredits');
            $row = $table->createRow();
            $row->Edition_ID = $this->params()->fromRoute('id');
            $row->Person_ID = $this->params()->fromPost('person_id');
            $row->Role_ID = $this->params()->fromPost('role_id');
            $row->Position = $this->params()->fromPost('pos');
            $row->Note_ID = $this->params()->fromPost('note_id');
            if (empty($row->Note_ID)) {
                $row->Note_ID = null;
            }
            $table->insert((array)$row);
            return $this->jsonReportSuccess();
        }
        // DELETE action:
        if ($this->getRequest()->isDelete()) {
            [$person, $role] = explode(',', $this->params()->fromRoute('extra'));
            $this->getDbTable('editionscredits')->delete(
                [
                    'Edition_ID' => $this->params()->fromRoute('id'),
                    'Person_ID' => $person,
                    'Role_ID' => $role,
                ]
            );
            return $this->jsonReportSuccess();
        }
        // Default behavior: show list:
        $table = $this->getDbTable('editionscredits');
        $view = $this->createViewModel();
        $primary = $this->params()->fromRoute('id');
        $view->credits = $table->getCreditsForEdition($primary);
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
            $this->getDbTable('editionscredits')->update(
                ['Position' => $this->params()->fromPost('pos')],
                [
                    'Edition_ID' => $this->params()->fromRoute('id'),
                    'Person_ID' => $this->params()->fromPost('person_id'),
                    'Role_ID' => $this->params()->fromPost('role_id'),
                ]
            );
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
        $table = $this->getDbTable('editionsfulltext');
        if ($this->getRequest()->isPost()) {
            $fields = [
                'Full_Text_Source_ID' => $this->params()->fromPost('source_id'),
                'Full_Text_URL' => trim($this->params()->fromPost('url')),
            ];
            $table->update($fields, ['Sequence_ID' => $rowId]);
            if ($attribs = $this->params()->fromPost('attribs')) {
                $this->saveFullTextAttributes($rowId, $attribs);
            }
            return $this->jsonReportSuccess();
        }
        $view = $this->createViewModel();
        $view->fullTextSources = $this->getDbTable('fulltextsource')
            ->getList();
        foreach ($table->select(['Sequence_ID' => $rowId]) as $current) {
            $view->row = $current;
        }
        $view->attributes = $this->getDbTable('editionsfulltextattribute')
            ->getList();
        $attributeValues = [];
        $values = $this->getDbTable('editionsfulltextattributesvalues')
            ->getAttributesForFullTextIDs([$rowId]);
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

        $table = $this->getDbTable('editionsfulltext');
        if ($this->getRequest()->isPost()) {
            $insert = [
                'Full_Text_Source_ID' => $this->params()->fromPost('source_id'),
                'Edition_ID' => $this->params()->fromRoute('id'),
                'Full_Text_URL' => trim($this->params()->fromPost('url')),
            ];
            if (empty($insert['Full_Text_URL'])) {
                return $this->jsonDie('URL must not be empty.');
            }
            $table->insert($insert);
            return $this->jsonReportSuccess();
        } elseif ($this->getRequest()->isDelete()) {
            $delete = $this->params()->fromRoute('extra');
            $table->delete(['Sequence_ID' => $delete]);
            return $this->jsonReportSuccess();
        }
        // Default behavior: display list:
        $view = $this->createViewModel();
        $primary = $this->params()->fromRoute('id');
        $view->fullText = $this->getDbTable('editionsfulltext')
            ->getFullTextForEdition($primary);
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
            $table = $this->getDbTable('editionsisbns');
            $row = $table->createRow();
            $row->Edition_ID = $this->params()->fromRoute('id');
            $row->Note_ID = $this->params()->fromPost('note_id');
            if (empty($row->Note_ID)) {
                $row->Note_ID = null;
            }
            $isbn10 = $isbn->get10();
            if (!empty($isbn10)) {
                $row->ISBN = $isbn10;
            }
            $row->ISBN13 = $isbn->get13();
            $table->insert((array)$row);
            return $this->jsonReportSuccess();
        } else {
            // Otherwise, treat this as a generic link:
            return $this->handleGenericLink(
                'editionsisbns',
                'Edition_ID',
                'Sequence_ID',
                'ISBNs',
                'getISBNsForEdition',
                'geeby-deeby/edit-edition/isbn-list.phtml'
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
            $table = $this->getDbTable('editionsoclcnumbers');
            $row = $table->createRow();
            $row->Edition_ID = $this->params()->fromRoute('id');
            $row->Note_ID = $this->params()->fromPost('note_id');
            if (empty($row->Note_ID)) {
                $row->Note_ID = null;
            }
            $row->OCLC_Number = $this->params()->fromPost('oclc_number');
            if (empty($row->OCLC_Number)) {
                return $this->jsonDie('OCLC number must not be empty.');
            }
            $table->insert((array)$row);
            return $this->jsonReportSuccess();
        } else {
            // Otherwise, treat this as a generic link:
            return $this->handleGenericLink(
                'editionsoclcnumbers',
                'Edition_ID',
                'Sequence_ID',
                'oclcNumbers',
                'getOCLCNumbersForEdition',
                'geeby-deeby/edit-edition/oclc-number-list.phtml'
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
            $table->insert((array)$row);
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
            $table->insert((array)$row);
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

        $parentEdition = $this->getDbTable('edition')->getByPrimaryKey(
            $this->params()->fromRoute('id')
        );
        $edName = $parentEdition->Edition_Name;
        $seriesID = $parentEdition->Series_ID;
        $insertCallback = function ($new, $row, $sm) {
            $edsTable = $sm->get('GeebyDeeby\Db\Table\PluginManager')
                ->get('edition');
            $newObj = $edsTable->getByPrimaryKey($new);
            if ($error = $newObj->validate()) {
                $newObj->delete();
                throw new \Exception($error);
            }
            $rows = $edsTable->select(['Item_ID' => $row['Item_ID']]);
            foreach ($rows as $row) {
                $row = $row->toArray();
                if ($row['Edition_ID'] != $new) {
                    break;
                }
            }
            if (isset($row['Edition_ID']) && $row['Edition_ID'] != $new) {
                $edsTable->getByPrimaryKey($new)->copyCredits($row['Edition_ID']);
            }
        };
        return $this->handleGenericLink(
            'edition',
            'Parent_Edition_ID',
            'Item_ID',
            'item_list',
            'getItemsForEdition',
            'geeby-deeby/edit-edition/item-list.phtml',
            ['Edition_Name' => $edName, 'Series_ID' => $seriesID],
            $insertCallback
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
            $pos = $this->params()->fromPost('pos');
            $this->getDbTable('edition')->update(
                ['Position_in_Parent' => $pos],
                ['Edition_ID' => $edition]
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
            'edit/edition',
            [
                'action' => 'index',
                'id' => $this->params()->fromRoute('id'),
                'extra' => $this->params()->fromRoute('extra'),
            ]
        );
    }
}
