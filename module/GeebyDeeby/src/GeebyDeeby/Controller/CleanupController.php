<?php
/**
 * Cleanup controller
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
     * @param int   $parent Parent Edition ID
     * @param array $child  Child Item information
     *
     * @return void
     */
    protected function processHierarchyItem($parent, $child)
    {
        $editionTable = $this->getDbTable('edition');
        $seriesEditions = $editionTable->select(
            ['Item_ID' => $child['Item_ID'], 'Series_ID' => $parent->Series_ID]
        );

        // Search editions in the current series to see if we have one that
        // can be assigned the current edition as its parent.
        foreach ($seriesEditions as $edition) {
            if ($edition->Parent_Edition_ID == $parent->Edition_ID) {
                throw new \Exception('Duplicate encountered!');
            }
            if (empty($edition->Parent_Edition_ID)) {
                $edition->Preferred_Series_Publisher_ID
                    = $parent->Preferred_Series_Publisher_ID;
                $edition->Parent_Edition_ID = $parent->Edition_ID;
                $edition->Position_In_Parent = $child->Position;
                $edition->Extent_In_Parent = $child->Note;
                $edition->save();
                return;
            }
        }

        // Favor a series match over a general match for copying associated
        // data....
        if (isset($edition) && is_object($edition)) {
            $templateEdition = $edition;
        } else {
            $anyEditions = $editionTable->select(
                ['Item_ID' => $child['Item_ID']]
            );
            $templateEdition = count($anyEditions) > 0
                ? $anyEditions->current() : false;
        }

        // If we got this far, we need to create a new edition:
        $newEditionData = [
            'Edition_Name' => $parent->Edition_Name,
            'Series_ID' => $parent->Series_ID,
            'Item_ID' => $child->Item_ID,
            'Preferred_Series_Publisher_ID' =>
                $parent->Preferred_Series_Publisher_ID,
            'Parent_Edition_ID' => $parent->Edition_ID,
            'Position_In_Parent' => $child->Position,
            'Extent_In_Parent' => $child->Note,
        ];
        $editionTable->insert($newEditionData);
        $newEdition = $editionTable->select($newEditionData)->current();
        if (!is_object($newEdition)) {
            throw new \Exception('Problem creating edition.');
        }
        if ($templateEdition) {
            $editionTable->copyAssociatedInfo($templateEdition, $newEdition);
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
        $editions = $this->getDbTable('edition')->select(['Item_ID' => $item]);
        foreach ($editions as $edition) {
            foreach ($targets as $target) {
                $this->processHierarchyItem($edition, $target);
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
        return $this->createViewModel(array('details' => $table->getAllCollections()));
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
        $details = array();
        foreach ($thumbs as $current) {
            $details[$current['Thumb_Path']]
                = $table->getEditionsForThumb($current['Thumb_Path']);
        }
        return $this->createViewModel(array('details' => $details));
    }
}
