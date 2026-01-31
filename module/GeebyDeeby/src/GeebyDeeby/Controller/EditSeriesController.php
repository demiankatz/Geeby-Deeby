<?php

/**
 * Edit series controller
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

use GeebyDeeby\Articles;
use GeebyDeeby\Db\Service\CategoryService;
use GeebyDeeby\Db\Service\CountryService;
use GeebyDeeby\Db\Service\EditionService;
use GeebyDeeby\Db\Service\ItemService;
use GeebyDeeby\Db\Service\LanguageService;
use GeebyDeeby\Db\Service\MaterialTypeService;
use GeebyDeeby\Db\Service\PublishersAddressService;
use GeebyDeeby\Db\Service\PublishersImprintService;
use GeebyDeeby\Db\Service\SeriesAltTitleService;
use GeebyDeeby\Db\Service\SeriesAttributeService;
use GeebyDeeby\Db\Service\SeriesAttributesValueService;
use GeebyDeeby\Db\Service\SeriesCategoryService;
use GeebyDeeby\Db\Service\SeriesMaterialTypeService;
use GeebyDeeby\Db\Service\SeriesPublisherService;
use GeebyDeeby\Db\Service\SeriesRelationshipService;
use GeebyDeeby\Db\Service\SeriesService;

use function count;
use function intval;

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
            SeriesService::class,
            'series',
            'geeby-deeby/edit-series/render-series'
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
        $service = $this->getDbService(SeriesAttributesValueService::class);
        // Delete old values:
        $service->deleteBySeries($seriesId);
        // Save new values:
        foreach ($attribs as $id => $val) {
            if (!empty($val)) {
                $entity = $service->createEntity()
                    ->setSeries($seriesId)
                    ->setAttribute($id)
                    ->setValue($val);
                $service->persistEntity($entity);
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
        $assignMap = [
            'name' => 'setSeriesName',
            'desc' => 'setDescription',
            'lang' => 'setLanguage',
        ];
        [$view, $ok] = $this->handleGenericItem(SeriesService::class, $assignMap, 'series');
        if (!$ok) {
            return $view;
        }
        $seriesId = $view->affectedEntity?->getId();

        // Special handling for saving attributes:
        if ($this->getRequest()->isPost() && $this->params()->fromPost('attribs')) {
            $this->saveAttributes(
                $seriesId,
                $this->params()->fromPost('attribs')
            );
        }

        $view->languages = $this->getDbService(LanguageService::class)->getList();
        $view->attributes = $this->getDbService(SeriesAttributeService::class)->getList();
        $attributeValues = [];
        $values = $seriesId
            ? $this->getDbService(SeriesAttributesValueService::class)->getAttributesForSeries($seriesId)
            : [];
        foreach ($values as $current) {
            $attributeValues[$current['Series_Attribute_ID']] = $current['Series_Attribute_Value'];
        }
        $view->attributeValues = $attributeValues;

        // Add extra fields/controls if outside of a lightbox:
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $view->materials = $this->getDbService(MaterialTypeService::class)->getList();
            $view->countries = $this->getDbService(CountryService::class)->getList();
            $view->categories = $this->getDbService(CategoryService::class)->getList();
            $view->selectedCategories = array_map(
                fn ($category) => $category['Category_ID'],
                $this->getDbService(SeriesCategoryService::class)->getCategoriesForSeries($seriesId)
            );

            $config = $this->serviceLocator->get('config');
            $groupByMaterial = $config['geeby-deeby']['groupSeriesByMaterialType']
                ?? true;
            $view->item_list = $this->getDbService(ItemService::class)
                ->getItemsForSeries($seriesId, true, $groupByMaterial);
            $view->series_alt_titles = $this->getDbService(SeriesAltTitleService::class)->getAltTitles($seriesId);
            $view->series_materials = $this->getDbService(SeriesMaterialTypeService::class)
                ->getMaterialTypesForSeries($seriesId);
            $view->series_publishers = $this->getDbService(SeriesPublisherService::class)
                ->getPublishersForSeries($seriesId);
            $view->relationships = $this->getDbService(SeriesRelationshipService::class)->getOptionList();
            $view->relationshipsValues = $this
                ->getDbTable('seriesrelationshipsvalues')
                ->getRelationshipsForSeries($seriesId);
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
            $series = $this->params()->fromRoute('id');
            $categories = $this->params()->fromPost('categories', []);
            $this->getDbService(SeriesCategoryService::class)->setCategoriesForSeries($series, $categories);
            return $this->jsonReportSuccess();
        }
        return $this->jsonDie('Unexpected action');
    }

    /**
     * Work with material types
     *
     * @return mixed
     */
    public function materialAction()
    {
        return $this->handleGenericLink(
            SeriesMaterialTypeService::class,
            'setSeries',
            'setMaterialType',
            'series_materials',
            'getMaterialTypesForSeries',
            'geeby-deeby/edit-series/material-type-list',
            retrieveLinkMethod: 'getBySeriesAndMaterialType'
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
            $service = $this->getDbService(SeriesAltTitleService::class);
            $note = $this->params()->fromPost('note_id');
            $title = trim((string)$this->params()->fromPost('title'));
            if (empty($title)) {
                return $this->jsonDie('Title must not be empty.');
            }
            $entity = $service->createEntity()
                ->setSeries($this->params()->fromRoute('id'))
                ->setNote(empty($note) ? null : $note)
                ->setAltName($title);
            $service->persistEntity($entity);
            return $this->jsonReportSuccess();
        }
        // Prevent deletion of alt titles that are linked up:
        if ($this->getRequest()->isDelete()) {
            $extra = $this->params()->fromRoute('extra');
            $result = $this->getDbService(EditionService::class)->getBySeriesAltTitleId($extra);
            if (count($result) > 0) {
                $ed = $result[0];
                $msg = 'You cannot delete this title; it is assigned to Edition ' . $ed->getId() . '.';
                return $this->jsonDie($msg);
            }
        }
        // Otherwise, treat this as a generic link:
        return $this->handleGenericLink(
            SeriesAltTitleService::class,
            null,
            null,
            'series_alt_titles',
            'getAltTitles',
            'geeby-deeby/edit-series/alt-title-list.phtml',
            retrieveLinkMethod: 'getBySeriesAndId'
        );
    }

    /**
     * Support method for publisherAction()
     *
     * @return mixed
     */
    protected function modifyPublisher()
    {
        $rowId = $this->params()->fromRoute('extra');
        $service = $this->getDbService(SeriesPublisherService::class);
        if ($this->getRequest()->isPost()) {
            $imprint = $this->params()->fromPost('imprint');
            $address = $this->params()->fromPost('address');
            $entity = $service->getByPrimaryKey($rowId);
            $entity->setImprint(empty($imprint) ? null : $imprint)
                ->setAddress(empty($address) ? null : $address);
            $service->persistEntity($entity);
            return $this->jsonReportSuccess();
        }
        $view = $this->createViewModel();
        $view->row = $service->getByPrimaryKey($rowId)->toArray();
        $view->addresses = $this->getDbService(PublishersAddressService::class)
            ->getAddressesForPublisher($view->row['Publisher_ID']);
        $view->imprints = $this->getDbService(PublishersImprintService::class)
            ->getImprintsForPublisher($view->row['Publisher_ID']);
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
        if (
            ($this->getRequest()->isPost() || $this->getRequest()->isGet())
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
            $service = $this->getDbService(SeriesPublisherService::class);
            $note = $this->params()->fromPost('note_id');
            $entity = $service->createEntity()
                ->setSeries($this->params()->fromRoute('id'))
                ->setPublisher($this->params()->fromPost('publisher_id'))
                ->setNote($note ? $note : null);
            $service->persistEntity($entity);
            return $this->jsonReportSuccess();
        }

        if ($this->getRequest()->isDelete()) {
            $extra = $this->params()->fromRoute('extra');
            $result = $this->getDbService(EditionService::class)->getByPreferredPublisherId($extra);
            if (count($result) > 0) {
                $ed = $result[0];
                $msg = 'You cannot delete this publisher; it is assigned to Edition ' . $ed->getId() . '.';
                return $this->jsonDie($msg);
            }
        }
        // Otherwise, treat this as a generic link:
        return $this->handleGenericLink(
            SeriesPublisherService::class,
            null,
            null,
            'series_publishers',
            'getPublishersForSeries',
            'geeby-deeby/edit-series/publisher-list.phtml',
            retrieveLinkMethod: 'getBySeriesAndId'
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

        $series = $this->getDbService(SeriesService::class)->getByPrimaryKey(
            $this->params()->fromRoute('id')
        );
        $edName = $this->serviceLocator->get(Articles::class)
            ->articleAwareAppend($series->getSeriesName(), ' edition');
        $config = $this->serviceLocator->get('config');
        $groupByMaterial = $config['geeby-deeby']['groupSeriesByMaterialType'] ?? true;
        $listCallback = $groupByMaterial ? 'getItemsForSeriesGroupedByMaterial' : 'getItemsForSeries';
        return $this->handleGenericLink(
            EditionService::class,
            'setSeries',
            'setItem',
            'item_list',
            $listCallback,
            'geeby-deeby/edit-series/item-list.phtml',
            ['setEditionName' => $edName],
            [$editionService, 'insertSeriesEditionCallback']
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
                [$vol, $pos] = $parts;
            }
            $service = $this->getDbService(EditionService::class);
            $entity = $service->getByPrimaryKey($edition)
                ->setPosition(intval($pos))
                ->setVolume(intval($vol));
            $service->persistEntity($entity);
            return $this->jsonReportSuccess();
        }
        return $this->jsonDie('Unexpected method');
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
            $linkFrom = 'Object_Series_ID';
            $linkTo = 'Subject_Series_ID';
            $rid = substr($rid, 1);
        } else {
            $linkFrom = 'Subject_Series_ID';
            $linkTo = 'Object_Series_ID';
        }
        $extras = ['Series_Relationship_ID' => $rid];
        return $this->handleGenericLink(
            'seriesrelationshipsvalues',
            $linkFrom,
            $linkTo,
            'relationshipsValues',
            'getRelationshipsForSeries',
            'geeby-deeby/edit-series/relationship-list.phtml',
            $extras
        );
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
                'extra' => $this->params()->fromRoute('extra'),
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
            'seriestranslations',
            'Source_Series_ID',
            'Trans_Series_ID',
            'translatedInto',
            'getTranslatedFrom',
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
            'seriestranslations',
            'Trans_Series_ID',
            'Source_Series_ID',
            'translatedFrom',
            'getTranslatedInto',
            'geeby-deeby/edit-series/trans-from-list.phtml'
        );
    }
}
