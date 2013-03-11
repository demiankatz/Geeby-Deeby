<?php
/**
 * Item controller
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
 * Item controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemController extends AbstractBase
{
    /**
     * "Show item" page
     *
     * @return mixed
     */
    public function indexAction()
    {
        $id = $this->params()->fromRoute('id');
        $table = $this->getDbTable('item');
        $rowObj = (null === $id) ? null : $table->getByPrimaryKey($id);
        if (!is_object($rowObj)) {
            return $this->forwardTo(__NAMESPACE__ . '\Item', 'notfound');
        }
        $view = $this->createViewModel(
            array('item' => $rowObj->toArray())
        );
        $view->credits = $this->getDbTable('itemscredits')->getCreditsForItem($id);
        $view->realNames = $this->getDbTable('pseudonyms')
            ->getRealNamesBatch($view->credits);
        $view->images = $this->getDbTable('itemsimages')->getImagesForItem($id);
        $view->series = $this->getDbTable('itemsinseries')->getSeriesForItem($id);
        $view->altTitles = $this->getDbTable('itemsalttitles')->getAltTitles($id);
        $view->platforms = $this->getDbTable('itemsplatforms')
            ->getPlatformsForItem($id);
        $collections = $this->getDbTable('itemsincollections');
        $view->contains = $collections->getItemsForCollection($id);
        $view->containedIn = $collections->getCollectionsForItem($id);
        $trans = $this->getDbTable('itemstranslations');
        $adapt = $this->getDbTable('itemsadaptations');
        // The variable/function names are a bit unintuitive here --
        // $view->translatedInto is a list of books that $id was translated into;
        // we obtain these by calling $trans->getTranslatedFrom(), which gives
        // us a list of books that $id was translated from.
        $view->translatedInto = $trans->getTranslatedFrom($id, true);
        $view->translatedFrom = $trans->getTranslatedInto($id, true);
        $view->adaptedInto = $adapt->getAdaptedFrom($id);
        $view->adaptedFrom = $adapt->getAdaptedInto($id);
        $view->dates = $this->getDbTable('itemsreleasedates')->getDatesForItem($id);
        return $view;
    }

    /**
     * Item list
     *
     * @return mixed
     */
    public function listAction()
    {
        return $this->createViewModel(
            array('items' => $this->getDbTable('item')->getList())
        );
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
