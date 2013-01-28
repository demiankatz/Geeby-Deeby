<?php
/**
 * Edit item controller
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
            'item', 'items', 'geeby-deeby/edit-item/render-items'
        );
    }

    /**
     * Operate on a single item
     *
     * @return mixed
     */
    public function indexAction()
    {
        $assignMap = array(
            'name' => 'Item_Name',
            'len' => 'Item_Length',
            'endings' => 'Item_Endings',
            'errata' => 'Item_Errata',
            'thanks' => 'Item_Thanks',
            'material' => 'Material_Type_ID'
        );
        $view = $this->handleGenericItem('item', $assignMap, 'item');

        $view->materials = $this->getDbTable('materialtype')->getList();

        // Add extra fields/controls if outside of a lightbox:
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $view->adaptedInto = $this->getDbTable('itemsadaptations')
                ->getAdaptedFrom($view->itemObj->Item_ID);
            $view->adaptedFrom = $this->getDbTable('itemsadaptations')
                ->getAdaptedInto($view->itemObj->Item_ID);
            $view->roles = $this->getDbTable('role')->getList();
            $view->credits= $this->getDbTable('itemscredits')
                ->getCreditsForItem($view->itemObj->Item_ID);
            $view->itemsBib = $this->getDbTable('itemsbibliography')
                ->getItemsDescribedByItem($view->itemObj->Item_ID);
            $view->peopleBib = $this->getDbTable('peoplebibliography')
                ->getPeopleDescribedByItem($view->itemObj->Item_ID);
            $view->seriesBib = $this->getDbTable('seriesbibliography')
                ->getSeriesDescribedByItem($view->itemObj->Item_ID);
            $view->images = $this->getDbTable('itemsimages')
                ->getImagesForItem($view->itemObj->Item_ID);
            $view->item_list = $this->getDbTable('itemsincollections')
                ->getItemsForCollection($view->itemObj->Item_ID);
            $view->translatedInto = $this->getDbTable('itemstranslations')
                ->getTranslatedFrom($view->itemObj->Item_ID);
            $view->descriptions = $this->getDbTable('itemsdescriptions')
                ->getDescriptions($view->itemObj->Item_ID);
            $view->item_alt_titles = $this->getDbTable('itemsalttitles')
                ->getAltTitles($view->itemObj->Item_ID);
            $view->releaseDates = $this->getDbTable('itemsreleasedates')
                ->getDatesForItem($view->itemObj->Item_ID);
            $view->translatedFrom = $this->getDbTable('itemstranslations')
                ->getTranslatedInto($view->itemObj->Item_ID);
            $view->setTemplate('geeby-deeby/edit-item/edit-full');
        }

        // Process series ID linkage if necessary:
        if ($this->getRequest()->isPost()) {
            if ($series = $this->params()->fromPost('series_id', false)) {
                $this->getDbTable('itemsinseries')->insert(
                    array(
                        'Item_ID' => $view->affectedRow->Item_ID,
                        'Series_ID' => $series
                    )
                );
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
            'itemsbibliography', 'Bib_Item_ID', 'Item_ID',
            'itemsBib', 'getItemsDescribedByItem',
            'geeby-deeby/edit-item/item-ref-list.phtml'
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
            'seriesbibliography', 'Item_ID', 'Series_ID',
            'seriesBib', 'getSeriesDescribedByItem',
            'geeby-deeby/edit-item/series-ref-list.phtml'
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
            'peoplebibliography', 'Item_ID', 'Person_ID',
            'peopleBib', 'getPeopleDescribedByItem',
            'geeby-deeby/edit-item/person-ref-list.phtml'
        );
    }

    /**
     * Deal with adaptations
     *
     * @return mixed
     */
    public function adaptationAction()
    {
        return $this->handleGenericLink(
            'itemsadaptations', 'Source_Item_ID', 'Adapted_Item_ID',
            'adaptedInto', 'getAdaptedFrom',
            'geeby-deeby/edit-item/adapted-into-list.phtml'
        );
    }

    /**
     * Deal with adaptation sources
     *
     * @return mixed
     */
    public function adaptedfromAction()
    {
        return $this->handleGenericLink(
            'itemsadaptations', 'Adapted_Item_ID', 'Source_Item_ID',
            'adaptedFrom', 'getAdaptedInto',
            'geeby-deeby/edit-item/adapted-from-list.phtml'
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
            $table = $this->getDbTable('itemsalttitles');
            $row = $table->createRow();
            $row->Item_ID = $this->params()->fromRoute('id');
            $row->Note_ID = $this->params()->fromPost('note_id');
            $row->Item_AltName = $this->params()->fromPost('title');
            $table->insert((array)$row);
            return $this->jsonReportSuccess();
        } else {
            // Otherwise, treat this as a generic link:
            return $this->handleGenericLink(
                'itemsalttitles', 'Item_ID', 'Sequence_ID',
                'item_alt_titles', 'getAltTitles',
                'geeby-deeby/edit-item/alt-title-list.phtml'
            );
        }
    }

    /**
     * Deal with attached items
     *
     * @return mixed
     */
    public function attachmentAction()
    {
        return $this->handleGenericLink(
            'itemsincollections', 'Collection_Item_ID', 'Item_ID',
            'item_list', 'getItemsForCollection',
            'geeby-deeby/edit-item/list.phtml'
        );
    }

    /**
     * Set the order of an attached item
     *
     * @return mixed
     */
    public function attachmentorderAction()
    {
        if ($this->getRequest()->isPost()) {
            $collection = $this->params()->fromRoute('id');
            $item = $this->params()->fromPost('attach_id');
            $pos = $this->params()->fromPost('pos');
            $this->getDbTable('itemsincollections')->update(
                array('Position' => $pos),
                array('Item_ID' => $item, 'Collection_Item_ID' => $collection)
            );
            return $this->jsonReportSuccess();
        }
        return $this->jsonDie('Unexpected method');
    }

    /**
     * Get list of dates
     *
     * @return mixed
     */
    public function datesAction()
    {
        $table = $this->getDbTable('itemsreleasedates');
        $view = $this->createViewModel();
        $primary = $this->params()->fromRoute('id');
        $view->releaseDates = $table->getDatesForItem($primary);
        $view->setTemplate('geeby-deeby/edit-item/date-list.phtml');
        $view->setTerminal(true);
        return $view;
    }

    /**
     * Add a date
     *
     * @return mixed
     */
    public function adddateAction()
    {
        if ($this->getRequest()->isPost()) {
            $table = $this->getDbTable('itemsreleasedates');
            $row = $table->createRow();
            $row->Item_ID = $this->params()->fromRoute('id');
            $row->Year = $this->params()->fromPost('year');
            $row->Month = $this->params()->fromPost('month');
            $row->Day = $this->params()->fromPost('day');
            $row->Note_ID = $this->params()->fromPost('note_id');
            $table->insert((array)$row);
            return $this->jsonReportSuccess();
        }
        return $this->jsonDie('Unexpected method');
    }

    /**
     * Remove a date
     *
     * @return mixed
     */
    public function deletedateAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->getDbTable('itemsreleasedates')->delete(
                array(
                    'Item_ID' => $this->params()->fromRoute('id'),
                    'Year' => $this->params()->fromPost('year'),
                    'Month' => $this->params()->fromPost('month'),
                    'Day' => $this->params()->fromPost('day'),
                )
            );
            return $this->jsonReportSuccess();
        }
        return $this->jsonDie('Unexpected method');
    }

    /**
     * Work with images
     *
     * @return mixed
     */
    public function imageAction()
    {
        // Special case: new publisher:
        if ($this->getRequest()->isPost()) {
            $table = $this->getDbTable('itemsimages');
            $row = $table->createRow();
            $row->Item_ID = $this->params()->fromRoute('id');
            $row->Note_ID = $this->params()->fromPost('note_id');
            $row->Image_Path = $this->params()->fromPost('image');
            if (empty($row->Image_Path)) {
                return $this->jsonDie('Image path must be set.');
            }
            $row->Thumb_Path = $this->params()->fromPost('thumb');
            // Build thumb path if none was provided:
            if (empty($row->Thumb_Path)) {
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
                'itemsimages', 'Item_ID', 'Sequence_ID',
                'images', 'getImagesForItem',
                'geeby-deeby/edit-item/image-list.phtml'
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
        if ($this->getRequest()->isPost()) {
            $collection = $this->params()->fromRoute('id');
            $image = $this->params()->fromRoute('extra');
            $pos = $this->params()->fromPost('pos');
            $this->getDbTable('itemsimages')->update(
                array('Position' => $pos), array('Sequence_ID' => $image)
            );
            return $this->jsonReportSuccess();
        }
        return $this->jsonDie('Unexpected method');
    }

    /**
     * Get list of credits
     *
     * @return mixed
     */
    public function creditsAction()
    {
        $table = $this->getDbTable('itemscredits');
        $view = $this->createViewModel();
        $primary = $this->params()->fromRoute('id');
        $view->credits = $table->getCreditsForItem($primary);
        $view->setTemplate('geeby-deeby/edit-item/credits.phtml');
        $view->setTerminal(true);
        return $view;
    }

    /**
     * Add a credit
     *
     * @return mixed
     */
    public function addcreditAction()
    {
        if ($this->getRequest()->isPost()) {
            $table = $this->getDbTable('itemscredits');
            $row = $table->createRow();
            $row->Item_ID = $this->params()->fromRoute('id');
            $row->Person_ID = $this->params()->fromPost('person_id');
            $row->Role_ID = $this->params()->fromPost('role_id');
            $row->Position = $this->params()->fromPost('pos');
            $row->Note_ID = $this->params()->fromPost('note_id');
            $table->insert((array)$row);
            return $this->jsonReportSuccess();
        }
        return $this->jsonDie('Unexpected method');
    }

    /**
     * Remove a credit
     *
     * @return mixed
     */
    public function deletecreditAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->getDbTable('itemscredits')->delete(
                array(
                    'Item_ID' => $this->params()->fromRoute('id'),
                    'Person_ID' => $this->params()->fromPost('person_id'),
                    'Role_ID' => $this->params()->fromPost('role_id')
                )
            );
            return $this->jsonReportSuccess();
        }
        return $this->jsonDie('Unexpected method');
    }

    /**
     * Set the order of an attached credit
     *
     * @return mixed
     */
    public function creditorderAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->getDbTable('itemscredits')->update(
                array('Position' => $this->params()->fromPost('pos')),
                array(
                    'Item_ID' => $this->params()->fromRoute('id'),
                    'Person_ID' => $this->params()->fromPost('person_id'),
                    'Role_ID' => $this->params()->fromPost('role_id')
                )
            );
            return $this->jsonReportSuccess();
        }
        return $this->jsonDie('Unexpected method');
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
            $table = $this->getDbTable('itemsdescriptions');
            $row = $table->createRow();
            $row->Item_ID = $this->params()->fromRoute('id');
            $row->Source = $this->params()->fromRoute('extra');
            $row->Description = $this->params()->fromPost('desc');
            try {
                $table->insert((array)$row);
            } catch (\Exception $e) {
                return $this->jsonDie($e->getMessage());
            }
            return $this->jsonReportSuccess();
        } else {
            // Otherwise, treat this as a generic link:
            return $this->handleGenericLink(
                'itemsdescriptions', 'Item_ID', 'Source', 'descriptions',
                'getDescriptions', 'geeby-deeby/edit-item/description-list.phtml'
            );
        }
    }

    /**
     * Deal with translations
     *
     * @return mixed
     */
    public function translationAction()
    {
        return $this->handleGenericLink(
            'itemstranslations', 'Source_Item_ID', 'Trans_Item_ID',
            'translatedInto', 'getTranslatedFrom',
            'geeby-deeby/edit-item/trans-into-list.phtml'
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
            'itemstranslations', 'Trans_Item_ID', 'Source_Item_ID',
            'translatedFrom', 'getTranslatedInto',
            'geeby-deeby/edit-item/trans-from-list.phtml'
        );
    }
}
