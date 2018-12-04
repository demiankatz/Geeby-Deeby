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
     * Display a list of items
     *
     * @return mixed
     */
    public function listAction()
    {
        return $this->getGenericList(
            'edition', 'editions', 'geeby-deeby/edit-edition/render-editions'
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
            'name' => 'Edition_Name',
            'item_id' => 'Item_ID',
            'series_id' => 'Series_ID',
            'position' => 'Position'
        );
        $view = $this->handleGenericItem('edition', $assignMap, 'edition');

        // Add item/series details if necessary:
        if (isset($view->edition['Item_ID']) && !empty($view->edition['Item_ID'])) {
            $view->item = $this->getDbTable('item')
                ->getByPrimaryKey($view->edition['Item_ID']);
        }
        if (isset($view->edition['Series_ID'])
            && !empty($view->edition['Series_ID'])
        ) {
            $view->series = $this->getDbTable('series')
                ->getByPrimaryKey($view->edition['Series_ID']);
        }
        // Add extra fields/controls if outside of a lightbox:
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $view->setTemplate('geeby-deeby/edit-edition/edit-full');
        }
        return $view;
    }

    /**
     * Copy an edition
     *
     * @return mixed
     */
    public function copyAction()
    {
        if ($this->getRequest()->isPost()) {
            $editionId = $this->params()->fromRoute('id');
            $table = $this->getDbTable('edition');
            $old = $table->getByPrimaryKey($editionId);
            if (!$old) {
                return $this->jsonDie('Cannot load edition ' . $editionId);
            }
            $new = $table->createRow();
            foreach ($old->toArray() as $key => $value) {
                if ($key != 'Edition_ID') {
                    $new->$key = $value;
                }
            }
            $new->Edition_Name = 'Copy of ' . $new->Edition_Name;
            $new->save();
            return $this->jsonReportSuccess();
        }
        return $this->jsonDie('Unexpected method');
    }
}
