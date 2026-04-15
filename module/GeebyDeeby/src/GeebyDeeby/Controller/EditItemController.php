<?php

/**
 * Edit item controller
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

use GeebyDeeby\Db\Service\CitationService;
use GeebyDeeby\Db\Service\EditionsCreditService;
use GeebyDeeby\Db\Service\EditionService;
use GeebyDeeby\Db\Service\ItemsAdaptationService;
use GeebyDeeby\Db\Service\ItemsAltTitleService;
use GeebyDeeby\Db\Service\ItemsAttributeService;
use GeebyDeeby\Db\Service\ItemsAttributesValueService;
use GeebyDeeby\Db\Service\ItemsBibliographyService;
use GeebyDeeby\Db\Service\ItemsCreatorsCitationService;
use GeebyDeeby\Db\Service\ItemsCreatorService;
use GeebyDeeby\Db\Service\ItemsDescriptionService;
use GeebyDeeby\Db\Service\ItemService;
use GeebyDeeby\Db\Service\ItemsInCollectionService;
use GeebyDeeby\Db\Service\ItemsRelationshipService;
use GeebyDeeby\Db\Service\ItemsRelationshipsValueService;
use GeebyDeeby\Db\Service\ItemsTagService;
use GeebyDeeby\Db\Service\ItemsTranslationService;
use GeebyDeeby\Db\Service\MaterialTypeService;
use GeebyDeeby\Db\Service\PeopleBibliographyService;
use GeebyDeeby\Db\Service\RoleService;
use GeebyDeeby\Db\Service\SeriesBibliographyService;
use GeebyDeeby\Db\Service\SeriesService;
use Throwable;

use function count;
use function intval;

/**
 * Edit item controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditItemController extends AbstractBase
{
    /**
     * Display a list of items
     *
     * @return mixed
     */
    public function listAction()
    {
        return $this->getGenericList(
            ItemService::class,
            'items',
            'geeby-deeby/edit-item/render-items'
        );
    }

    /**
     * Save attributes for the current item.
     *
     * @param int   $itemId  Item ID
     * @param array $attribs Attribute values
     *
     * @return void
     */
    protected function saveAttributes($itemId, $attribs)
    {
        $service = $this->getDbService(ItemsAttributesValueService::class);
        // Delete old values:
        $service->deleteByItem($itemId);
        // Save new values:
        foreach ($attribs as $id => $val) {
            if (!empty($val)) {
                $entity = $service->createEntity()
                    ->setItem($itemId)
                    ->setAttribute($id)
                    ->setValue($val);
                $service->persistEntity($entity);
            }
        }
    }

    /**
     * Operate on a single item
     *
     * @return mixed
     */
    public function indexAction()
    {
        $assignMap = [
            'name' => 'setItemName',
            'errata' => 'setErrata',
            'thanks' => 'setThanks',
            'material' => 'setMaterialType',
        ];
        [$view, $ok] = $this->handleGenericItem(ItemService::class, $assignMap, 'item');
        if (!$ok) {
            return $view;
        }
        $itemId = $view->affectedEntity?->getId();

        // Special handling for saving attributes:
        if (
            $this->getRequest()->isPost()
            && ($attribs = $this->params()->fromPost('attribs'))
        ) {
            $this->saveAttributes($itemId, $attribs);
        }

        // Add attribute details if we have an Item_ID.
        if ($itemId) {
            $view->attributes = $this->getDbService(ItemsAttributeService::class)->getList();
            $attributeValues = [];
            $values = $this->getDbService(ItemsAttributesValueService::class)->getAttributesForItem($itemId);
            foreach ($values as $current) {
                $attributeValues[$current['Items_Attribute_ID']] = $current['Items_Attribute_Value'];
            }
            $view->attributeValues = $attributeValues;
        }

        $view->materials = $this->getDbService(MaterialTypeService::class)->getList();

        // Add extra fields/controls if outside of a lightbox:
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $view->adaptedInto = $this->getDbService(ItemsAdaptationService::class)->getAdaptedFrom($itemId);
            $view->adaptedFrom = $this->getDbService(ItemsAdaptationService::class)->getAdaptedInto($itemId);
            $view->roles = $this->getDbService(RoleService::class)->getList();
            $view->creators = $this->getDbService(ItemsCreatorService::class)->getCreatorsForItem($itemId);
            $view->credits = $this->getDbService(EditionsCreditService::class)->getCreditsForItem($itemId, true);
            $view->itemsBib = $this->getDbService(ItemsBibliographyService::class)
                ->getItemsDescribedByItem($itemId);
            $view->peopleBib = $this->getDbService(PeopleBibliographyService::class)
                ->getPeopleDescribedByItem($itemId);
            $view->seriesBib = $this->getDbService(SeriesBibliographyService::class)
                ->getSeriesDescribedByItem($itemId);
            $view->item_list = $this->getDbService(ItemsInCollectionService::class)->getItemsForCollection($itemId);
            $view->translatedInto = $this->getDbService(ItemsTranslationService::class)->getTranslatedFrom($itemId);
            $view->descriptions = $this->getDbService(ItemsDescriptionService::class)->getDescriptions($itemId);
            $view->tags = $this->getDbService(ItemsTagService::class)->getTagsForItem($itemId);
            $view->item_alt_titles = $this->getDbService(ItemsAltTitleService::class)->getAltTitles($itemId);
            $view->relationships = $this->getDbService(ItemsRelationshipService::class)->getOptionList();
            $view->relationshipsValues = $this->getDbService(ItemsRelationshipsValueService::class)
                ->getRelationshipsForItem($itemId);
            $view->translatedFrom = $this->getDbService(ItemsTranslationService::class)->getTranslatedInto($itemId);
            $view->editions = $this->getDbService(EditionService::class)->getEditionsForItem($itemId);
            $view->setTemplate('geeby-deeby/edit-item/edit-full');
        }

        // Process series ID linkage if necessary:
        if ($this->getRequest()->isPost()) {
            if ($editionID = $this->params()->fromPost('edition_id', false)) {
                $editionService = $this->getDbService(EditionService::class);
                $parentEdition = $editionService->getByPrimaryKey($editionID);
                $newEdition = $editionService->createEntity()
                    ->setEditionName($parentEdition->getEditionName())
                    ->setItem($itemId)
                    ->setSeries($parentEdition->getSeries())
                    ->setLength($this->params()->fromPost('len'))
                    ->setEndings($this->params()->fromPost('endings'))
                    ->setParentEdition($parentEdition);
                $editionService->persistEntity($newEdition);
            } elseif ($seriesID = $this->params()->fromPost('series_id', false)) {
                $series = $this->getDbService(SeriesService::class)->getByPrimaryKey($seriesID);
                $edName = $this->serviceLocator->get('GeebyDeeby\Articles')
                    ->articleAwareAppend($series->getSeriesName(), ' edition');
                $editionService = $this->getDbService(EditionService::class);
                $newEdition = $editionService->createEntity()
                    ->setEditionName($edName)
                    ->setItem($itemId)
                    ->setSeries($series)
                    ->setLength($this->params()->fromPost('len'))
                    ->setEndings($this->params()->fromPost('endings'));
                $editionService->persistEntity($newEdition);
            }
        }

        return $view;
    }

    /**
     * Deal with item references
     *
     * @return mixed
     */
    public function aboutitemAction()
    {
        return $this->handleGenericLink(
            ItemsBibliographyService::class,
            'setBibliographyItem',
            'setItem',
            'itemsBib',
            'getItemsDescribedByItem',
            'geeby-deeby/edit-item/item-ref-list.phtml',
            retrieveLinkMethod: 'getByBibliographyItemAndItem',
        );
    }

    /**
     * Deal with series references
     *
     * @return mixed
     */
    public function aboutseriesAction()
    {
        return $this->handleGenericLink(
            SeriesBibliographyService::class,
            'setItem',
            'setSeries',
            'seriesBib',
            'getSeriesDescribedByItem',
            'geeby-deeby/edit-item/series-ref-list.phtml',
            retrieveLinkMethod: 'getByItemAndSeries'
        );
    }

    /**
     * Deal with person references
     *
     * @return mixed
     */
    public function aboutpersonAction()
    {
        return $this->handleGenericLink(
            PeopleBibliographyService::class,
            'setItem',
            'setPerson',
            'peopleBib',
            'getPeopleDescribedByItem',
            'geeby-deeby/edit-item/person-ref-list.phtml',
            retrieveLinkMethod: 'getByItemAndPerson'
        );
    }

    /**
     * Deal with adaptations
     *
     * @return mixed
     */
    public function adaptationintoAction()
    {
        return $this->handleGenericLink(
            ItemsAdaptationService::class,
            'setSourceItem',
            'setAdaptedItem',
            'adaptedInto',
            'getAdaptedFrom',
            'geeby-deeby/edit-item/adapted-into-list.phtml',
            retrieveLinkMethod: 'getBySourceItemAndAdaptedItem'
        );
    }

    /**
     * Deal with adaptation sources
     *
     * @return mixed
     */
    public function adaptationfromAction()
    {
        return $this->handleGenericLink(
            ItemsAdaptationService::class,
            'setAdaptedItem',
            'setSourceItem',
            'adaptedFrom',
            'getAdaptedInto',
            'geeby-deeby/edit-item/adapted-from-list.phtml',
            retrieveLinkMethod: 'getBySourceItemAndAdaptedItem',
            invertRetrieveLinkParams: true
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
            $service = $this->getDbService(ItemsAltTitleService::class);
            $note = $this->params()->fromPost('note_id');
            $title = trim((string)$this->params()->fromPost('title'));
            if (empty($title)) {
                return $this->jsonDie('Title must not be empty.');
            }
            $entity = $service->createEntity()
                ->setItem($this->params()->fromRoute('id'))
                ->setNote(empty($note) ? null : $note)
                ->setAltName($title);
            $service->persistEntity($entity);
            return $this->jsonReportSuccess();
        }
        // Prevent deletion of alt titles that are linked up:
        if ($this->getRequest()->isDelete()) {
            $extra = $this->params()->fromRoute('extra');
            $result = $this->getDbService(EditionService::class)->getByItemAltTitleId($extra);
            if (count($result) > 0) {
                $ed = $result[0];
                $msg = 'You cannot delete this title; it is assigned to Edition ' . $ed->getId() . '.';
                return $this->jsonDie($msg);
            }
        }
        // Otherwise, treat this as a generic link:
        return $this->handleGenericLink(
            ItemsAltTitleService::class,
            null,
            null,
            'item_alt_titles',
            'getAltTitles',
            'geeby-deeby/edit-item/alt-title-list.phtml',
            retrieveLinkMethod: 'getByItemAndId'
        );
    }

    /**
     * Deal with attached items
     *
     * @return mixed
     */
    public function attachmentAction()
    {
        if ($this->getRequest()->isDelete()) {
            $collection = $this->params()->fromRoute('id');
            [$item, $pos] = explode(',', $this->params()->fromRoute('extra'));
            $service = $this->getDbService(ItemsInCollectionService::class);
            if ($entity = $service->getByCollectionItemAndItemAndPosition($collection, $item, $pos)) {
                $service->deleteEntity($entity);
            }
            return $this->jsonReportSuccess();
        }
        $note = $this->params()->fromPost('note_id');
        $extras = ['setPosition' => 0, 'setNote' => empty($note) ? null : intval($note)];
        return $this->handleGenericLink(
            ItemsInCollectionService::class,
            'setCollectionItem',
            'setItem',
            'item_list',
            'getItemsForCollection',
            'geeby-deeby/edit-item/list.phtml',
            $extras,
            retrieveLinkMethod: 'getByCollectionItemAndItemAndPosition'
        );
    }

    /**
     * Deal with editions
     *
     * @return mixed
     */
    public function editionsAction()
    {
        return $this->handleGenericLink(
            EditionService::class,
            null,
            null,
            'editions',
            'getEditionsForItem',
            'geeby-deeby/edit-item/edition-list.phtml'
        );
    }

    /**
     * Set the order of an attached item
     *
     * @return mixed
     */
    public function attachmentorderAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->jsonDie('Unexpected method');
        }
        $collection = $this->params()->fromRoute('id');
        $item = $this->params()->fromPost('item_id');
        $pos = $this->params()->fromPost('pos');
        $service = $this->getDbService(ItemsInCollectionService::class);
        if ($entity = $service->getByCollectionItemAndItemAndPosition($collection, $item)) {
            $entity->setPosition(intval($pos));
            try {
                $service->persistEntity($entity);
            } catch (Throwable $e) {
                // Laminas throws a strange error sometimes; let's ignore it as long as the
                // data updated successfully...
                if (!$service->getByCollectionItemAndItemAndPosition($collection, $item, $pos)) {
                    return $this->jsonDie($e->getMessage());
                }
            }
        }
        return $this->jsonReportSuccess();
    }

    /**
     * Get list of creators
     *
     * @return mixed
     */
    public function creatorAction()
    {
        $ok = $this->checkPermission('Content_Editor');
        if ($ok !== true) {
            return $ok;
        }
        // Modify the publisher if it's a GET/POST and has an extra set.
        if (
            ($this->getRequest()->isPost() || $this->getRequest()->isGet())
            && null !== $this->params()->fromRoute('extra')
            && 'NEW' !== $this->params()->fromRoute('extra')
        ) {
            return $this->modifyCreator();
        }
        if ($this->getRequest()->isPost()) {
            return $this->addCreator();
        }
        if ($this->getRequest()->isDelete()) {
            return $this->deleteCreator();
        }
        // Default action: display list:
        $view = $this->createViewModel();
        $primary = $this->params()->fromRoute('id');
        $view->creators = $this->getDbService(ItemsCreatorService::class)->getCreatorsForItem($primary);
        $view->setTemplate('geeby-deeby/edit-item/creators.phtml');
        $view->setTerminal(true);
        return $view;
    }

    /**
     * Support method for creatorAction()
     *
     * @return mixed
     */
    protected function modifyCreator()
    {
        $rowId = $this->params()->fromRoute('extra');
        $view = $this->createViewModel();
        $view->row = $this->getDbService(ItemsCreatorService::class)->getByPrimaryKey($rowId);
        $view->citations = $this->getDbService(CitationService::class)->getList();
        $view->selectedCitations = $this->getDbService(ItemsCreatorsCitationService::class)->getCitations($rowId);
        $view->setTemplate('geeby-deeby/edit-item/modify-creator');

        // If this is an AJAX request, render the core list only, not the
        // framing layout and buttons.
        if ($this->getRequest()->isXmlHttpRequest()) {
            $view->setTerminal(true);
        }

        return $view;
    }

    /**
     * Add a creator
     *
     * @return mixed
     */
    protected function addCreator()
    {
        $service = $this->getDbService(ItemsCreatorService::class);
        $entity = $service->createEntity()
            ->setItem($this->params()->fromRoute('id'))
            ->setPerson($this->params()->fromPost('person_id'))
            ->setRole($this->params()->fromPost('role_id'));
        $service->persistEntity($entity);
        return $this->jsonReportSuccess();
    }

    /**
     * Remove a creator
     *
     * @return mixed
     */
    protected function deleteCreator()
    {
        $service = $this->getDbService(ItemsCreatorService::class);
        [$person, $role] = explode(',', $this->params()->fromRoute('extra'));
        try {
            $entity = $service->getByItemAndPersonAndRole($this->params()->fromRoute('id'), $person, $role);
            $service->deleteEntity($entity);
        } catch (\Exception $e) {
            return $this->jsonDie($e->getMessage());
        }
        return $this->jsonReportSuccess();
    }

    /**
     * Get list of credits
     *
     * @return mixed
     */
    public function creditAction()
    {
        $ok = $this->checkPermission('Content_Editor');
        if ($ok !== true) {
            return $ok;
        }
        if ($this->getRequest()->isPost()) {
            return $this->addCredit();
        }
        if ($this->getRequest()->isDelete()) {
            return $this->deleteCredit();
        }
        // Default action: display list:
        $view = $this->createViewModel();
        $primary = $this->params()->fromRoute('id');
        $view->credits = $this->getDbService(EditionsCreditService::class)->getCreditsForItem($primary, true);
        $view->setTemplate('geeby-deeby/edit-item/credits.phtml');
        $view->setTerminal(true);
        return $view;
    }

    /**
     * Add a credit
     *
     * @return mixed
     */
    protected function addCredit()
    {
        $editionService = $this->getDbService(EditionService::class);
        $creditService = $this->getDbService(EditionsCreditService::class);
        $item = $this->params()->fromRoute('id');
        $note = $this->params()->fromPost('note_id');
        $editions = $editionService->getEditionsForItem($item);
        foreach ($editions as $current) {
            $editionId = $current['Edition_ID'];
            $credit = $creditService->createEntity()
                ->setEdition($editionId)
                ->setPerson((int)$this->params()->fromPost('person_id'))
                ->setRole((int)$this->params()->fromPost('role_id'))
                ->setPosition((int)$this->params()->fromPost('pos'))
                ->setNote($note ? (int)$note : null);
            $creditService->persistEntity($credit);
        }
        return $this->jsonReportSuccess();
    }

    /**
     * Remove a credit
     *
     * @return mixed
     */
    protected function deleteCredit()
    {
        $editionService = $this->getDbService(EditionService::class);
        $creditService = $this->getDbService(EditionsCreditService::class);
        [$person, $role] = explode(',', $this->params()->fromRoute('extra'));
        $editions = $editionService->getEditionsForItem($this->params()->fromRoute('id'));
        foreach ($editions as $current) {
            if ($credit = $creditService->getByEditionAndPersonAndRole($current['Edition_ID'], $person, $role)) {
                $creditService->deleteEntity($credit);
            }
        }
        return $this->jsonReportSuccess();
    }

    /**
     * Set the order of an attached credit
     *
     * @return mixed
     */
    public function creditorderAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->jsonDie('Unexpected method');
        }
        $editionService = $this->getDbService(EditionService::class);
        $creditService = $this->getDbService(EditionsCreditService::class);
        $editions = $editionService->getEditionsForItem($this->params()->fromRoute('id'));
        foreach ($editions as $current) {
            $credit = $creditService->getByEditionAndPersonAndRole(
                $current['Edition_ID'],
                $this->params()->fromPost('person_id'),
                $this->params()->fromPost('role_id')
            );
            if ($credit) {
                $credit->setPosition((int)$this->params()->fromPost('pos'));
                $creditService->persistEntity($credit);
            }
        }
        return $this->jsonReportSuccess();
    }

    /**
     * Work with descriptions
     *
     * @return mixed
     */
    public function descriptionAction()
    {
        // Special case: new description:
        if ($this->getRequest()->isPost()) {
            $service = $this->getDbService(ItemsDescriptionService::class);
            $entity = $service->createEntity()
                ->setItem($this->params()->fromRoute('id'))
                ->setSource($this->params()->fromPost('type'))
                ->setDescription($this->params()->fromPost('desc'));
            try {
                $service->persistEntity($entity);
            } catch (\Exception $e) {
                return $this->jsonDie($e->getMessage());
            }
            return $this->jsonReportSuccess();
        } else {
            // Otherwise, treat this as a generic link:
            return $this->handleGenericLink(
                ItemsDescriptionService::class,
                null,
                null,
                'descriptions',
                'getDescriptions',
                'geeby-deeby/edit-item/description-list.phtml',
                retrieveLinkMethod: 'getByItemAndSource'
            );
        }
    }

    /**
     * Set the order of an edition
     *
     * @return mixed
     */
    public function editionsorderAction()
    {
        $ok = $this->checkPermission('Content_Editor');
        if ($ok !== true) {
            return $ok;
        }
        if ($this->getRequest()->isPost()) {
            $edition = $this->params()->fromPost('edition_id');
            $pos = $this->params()->fromPost('pos');
            $entityService = $this->getDbService(EditionService::class);
            $entity = $entityService->getByPrimaryKey($edition);
            $entity->setItemDisplayOrder(intval($pos));
            $entityService->persistEntity($entity);
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
            'edit/item',
            [
                'action' => 'index',
                'id' => $this->params()->fromRoute('id'),
                'extra' => $this->params()->fromRoute('extra'),
            ]
        );
    }

    /**
     * Deal with arbitrary relationships.
     *
     * @return mixed
     */
    public function relationshipAction()
    {
        // The relationship ID may have a leading 'i' indicating an inverse
        // relationship; if we find this, we should handle it here to keep
        // the standard behavior consistent.
        $rid = $this->params()->fromRoute('relationship_id');
        if (substr($rid, 0, 1) === 'i') {
            $linkFrom = 'setObject';
            $linkTo = 'setSubject';
            $rid = substr($rid, 1);
            $invertRetrieve = true;
        } else {
            $linkFrom = 'setSubject';
            $linkTo = 'setObject';
            $invertRetrieve = false;
        }
        $extras = ['setRelationship' => $rid];
        return $this->handleGenericLink(
            ItemsRelationshipsValueService::class,
            $linkFrom,
            $linkTo,
            'relationshipsValues',
            'getRelationshipsForItem',
            'geeby-deeby/edit-item/relationship-list.phtml',
            $extras,
            retrieveLinkMethod: 'getBySubjectAndObjectAndRelationship',
            invertRetrieveLinkParams: $invertRetrieve
        );
    }

    /**
     * Deal with tags
     *
     * @return mixed
     */
    public function tagAction()
    {
        return $this->handleGenericLink(
            ItemsTagService::class,
            'setItem',
            'setTag',
            'tags',
            'getTagsForItem',
            'geeby-deeby/edit-item/tag-list.phtml',
            retrieveLinkMethod: 'getByItemAndTag'
        );
    }

    /**
     * Deal with translations (into)
     *
     * @return mixed
     */
    public function translationintoAction()
    {
        return $this->handleGenericLink(
            ItemsTranslationService::class,
            'setSourceItem',
            'setTranslatedItem',
            'translatedInto',
            'getTranslatedFrom',
            'geeby-deeby/edit-item/trans-into-list.phtml',
            retrieveLinkMethod: 'getBySourceItemAndTranslatedItem'
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
            ItemsTranslationService::class,
            'setTranslatedItem',
            'setSourceItem',
            'translatedFrom',
            'getTranslatedInto',
            'geeby-deeby/edit-item/trans-from-list.phtml',
            retrieveLinkMethod: 'getBySourceItemAndTranslatedItem',
            invertRetrieveLinkParams: true
        );
    }
}
