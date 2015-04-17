<?php
/**
 * Edition controller
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
 * Edition controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionController extends AbstractBase
{
    /**
     * Get a view model containing an edition object (or return false if missing)
     *
     * @param array $extras Extra parameters to send to view model
     *
     * @return mixed
     */
    protected function getViewModelWithEdition($extras = array())
    {
        $id = $this->params()->fromRoute('id');
        $table = $this->getDbTable('edition');
        $rowObj = (null === $id) ? null : $table->getByPrimaryKey($id);
        if (!is_object($rowObj)) {
            return false;
        }
        if (!empty($rowObj->Item_ID)) {
            $itemTable = $this->getDbTable('item');
            $itemObj = $itemTable->getByPrimaryKey($rowObj->Item_ID);
            $item = $itemObj->toArray();
            if (!empty($rowObj->Preferred_Item_AltName_ID)) {
                $ian = $this->getDbTable('itemsalttitles');
                $tmpRow = $ian->select(
                    array('Sequence_ID' => $rowObj->Preferred_Item_AltName_ID)
                )->current();
                $item['Item_AltName'] = $tmpRow['Item_AltName'];
            }
        } else {
            $item = array();
        }
        if (!empty($rowObj->Series_ID)) {
            $seriesTable = $this->getDbTable('series');
            $seriesObj = $seriesTable->getByPrimaryKey($rowObj->Series_ID);
            $series = $seriesObj->toArray();
            if (!empty($rowObj->Preferred_Series_AltName_ID)) {
                $ian = $this->getDbTable('seriesalttitles');
                $tmpSeriesRow = $ian->select(
                    array('Sequence_ID' => $rowObj->Preferred_Series_AltName_ID)
                )->current();
                $series['Series_AltName'] = $tmpSeriesRow['Series_AltName'];
            }
        } else {
            $series = array();
        }
        return $this->createViewModel(
            array('edition' => $rowObj->toArray(), 'item' => $item, 'series' => $series)
            + $extras
        );
    }

    /**
     * "Show item" page
     *
     * @return mixed
     */
    public function indexAction()
    {
        $view = $this->getViewModelWithEdition();
        if (!$view) {
            return $this->forwardTo(__NAMESPACE__ . '\Edition', 'notfound');
        }
        $id = $view->edition['Edition_ID'];
        $view->credits = $this->getDbTable('editionscredits')
            ->getCreditsForEdition($id);
        $view->realNames = $this->getDbTable('pseudonyms')
            ->getRealNamesBatch($view->credits);
        $view->images = $this->getDbTable('editionsimages')->getImagesForEdition($id);
        $view->platforms = $this->getDbTable('editionsplatforms')
            ->getPlatformsForEdition($id);
        $view->dates = $this->getDbTable('editionsreleasedates')->getDatesForEdition($id);
        $view->isbns = $this->getDbTable('editionsisbns')->getISBNsForEdition($id);
        $view->codes = $this->getDbTable('editionsproductcodes')
            ->getProductCodesForEdition($id);
        $view->oclcNumbers = $this->getDbTable('editionsoclcnumbers')
            ->getOCLCNumbersForEdition($id);
        $view->fullText = $this->getDbTable('editionsfulltext')
            ->getFullTextForEdition($id);
        $view->publishers = $this->getDbTable('edition')->getPublishersForEdition($id);
        return $view;
    }

    /**
     * Not found page
     *
     * @return mixed
     */
    public function notfoundAction()
    {
        return $this->createViewModel();
    }
}
