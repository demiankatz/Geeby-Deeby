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
            $view->translatedInto = $this->getDbTable('itemstranslations')
                ->getTranslatedFrom($view->itemObj->Item_ID);
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
