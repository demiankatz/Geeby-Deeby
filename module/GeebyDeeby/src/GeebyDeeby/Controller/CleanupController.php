<?php

/**
 * Cleanup controller
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

use GeebyDeeby\Db\Service\EditionService;

use function is_object;

/**
 * Cleanup controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class CleanupController extends AbstractBase
{
    /**
     * Main action
     *
     * @return mixed
     */
    public function indexAction()
    {
        $ok = $this->checkPermission('Data_Manager');
        if ($ok !== true) {
            return $ok;
        }
        return $this->createViewModel();
    }

    /**
     * Support method for processHierarchy() -- process a single parent-child
     * pairing.
     *
     * @param array $parent Parent Edition information
     * @param array $child  Child Item information
     *
     * @return void
     */
    protected function processHierarchyItem($parent, $child)
    {
        $editionService = $this->getDbService(EditionService::class);
        $seriesEditions = $editionService->getByItemAndSeries($child['Item_ID'], $parent['Series_ID']);

        // Search editions in the current series to see if we have one that
        // can be assigned the current edition as its parent.
        foreach ($seriesEditions as $edition) {
            $currentParent = $edition->getParentEdition();
            if ($currentParent?->getId() == $parent['Edition_ID']) {
                throw new \Exception('Duplicate encountered!');
            }
            if (!$currentParent) {
                $edition->setPreferredPublisher($parent['Preferred_Series_Publisher_ID'])
                    ->setParentEdition($parent['Edition_ID'])
                    ->setPositionInParent($child['Position'])
                    ->setExtentInParent($child['Note']);
                $editionService->persistEntity($edition);
                return;
            }
        }

        // Favor a series match over a general match for copying associated
        // data....
        if (isset($edition) && is_object($edition)) {
            $templateEdition = $edition;
        } else {
            $anyEditions = $editionService->getByItem($child['Item_ID']);
            $templateEdition = $anyEditions[0] ?? null;
        }

        // If we got this far, we need to create a new edition:
        $newEdition = $editionService->createEntity()
            ->setEditionName($parent['Edition_Name'])
            ->setSeries($parent['Series_ID'])
            ->setItem($child['Item_ID'])
            ->setPreferredPublisher($parent['Preferred_Series_Publisher_ID'])
            ->setParentEdition($parent['Edition_ID'])
            ->setPositionInParent($child['Position'])
            ->setExtentInParent($child['Note']);
        $editionService->persistEntity($newEdition);
        if ($templateEdition) {
            $editionService->copyAssociatedInfo($templateEdition, $newEdition);
        }
    }

    /**
     * Support method for hierarchiesAction() -- process a single item.
     *
     * @param int $item Item ID
     *
     * @return void
     */
    protected function processHierarchy($item)
    {
        $table = $this->getDbTable('itemsincollections');
        $targets = $table->getItemsForCollection($item);
        $editions = $this->getDbService(EditionService::class)->getByItem($item);
        foreach ($editions as $edition) {
            foreach ($targets as $target) {
                $this->processHierarchyItem($edition->toArray(), $target);
            }
        }
        $table->delete(['Collection_Item_ID' => $item]);
    }

    /**
     * Migrate item hierarchies to edition hierarchies
     *
     * @return mixed
     */
    public function hierarchiesAction()
    {
        $ok = $this->checkPermission('Data_Manager');
        if ($ok !== true) {
            return $ok;
        }
        $process = $this->params()->fromPost('items');
        if (!empty($process)) {
            foreach ($process as $id) {
                $this->processHierarchy($id);
            }
        }
        $table = $this->getDbTable('itemsincollections');
        return $this->createViewModel(['details' => $table->getAllCollections()]);
    }

    /**
     * Duplicate image cleanup action
     *
     * @return mixed
     */
    public function imagedupesAction()
    {
        $ok = $this->checkPermission('Data_Manager');
        if ($ok !== true) {
            return $ok;
        }
        $table = $this->getDbTable('editionsimages');
        $thumbs = $table->getDuplicateThumbs();
        $details = [];
        foreach ($thumbs as $current) {
            $details[$current['Thumb_Path']]
                = $table->getEditionsForThumb($current['Thumb_Path']);
        }
        return $this->createViewModel(['details' => $details]);
    }
}
