<?php
/**
 * Edit link controller
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
 * Edit link controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditLinkController extends AbstractBase
{
    /**
     * Display a list of links
     *
     * @return mixed
     */
    public function listAction()
    {
        $view = $this->getGenericList(
            'link', 'links', 'geeby-deeby/edit-link/render-links'
        );
        // If this is not an AJAX request, we also want to display types:
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $view->linkTypes = $this->typelistAction()->linkTypes;
        }
        return $view;
    }

    /**
     * Operate on a single link
     *
     * @return mixed
     */
    public function indexAction()
    {
        $assignMap = array(
            'link_name' => 'Link_Name', 'url' => 'URL',
            'desc' => 'Description', 'date_checked' => 'Date_Checked',
            'type_id' => 'Link_Type_ID'
        );
        $view = $this->handleGenericItem('link', $assignMap, 'link');
        $view->linkTypes = $this->typelistAction()->linkTypes;
        // Add extra fields/controls if outside of a lightbox:
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $view->itemsLinks = $this->getDbTable('itemslinks')
                ->getItemsForLink($view->linkObj->Link_ID);
            $view->peopleLinks = $this->getDbTable('peoplelinks')
                ->getPeopleForLink($view->linkObj->Link_ID);
            $view->seriesLinks = $this->getDbTable('serieslinks')
                ->getSeriesForLink($view->linkObj->Link_ID);
            $view->setTemplate('geeby-deeby/edit-link/edit-full');
        }
        return $view;
    }

    /**
     * Display a list of types
     *
     * @return mixed
     */
    public function typelistAction()
    {
        return $this->getGenericList(
            'linkType', 'linkTypes', 'geeby-deeby/edit-link/render-types'
        );
    }

    /**
     * Operate on a single type
     *
     * @return mixed
     */
    public function typeAction()
    {
        $assignMap = array('linkType' => 'Link_Type');
        return $this->handleGenericItem('linkType', $assignMap, 'linkType');
    }

    /**
     * Deal with link/item links
     *
     * @return mixed
     */
    public function itemAction()
    {
        return $this->handleGenericLink(
            'itemslinks', 'Link_ID', 'Item_ID',
            'itemsLinks', 'getItemsForLink',
            'geeby-deeby/edit-link/item-list.phtml'
        );
    }

    /**
     * Deal with link/item links
     *
     * @return mixed
     */
    public function personAction()
    {
        return $this->handleGenericLink(
            'peoplelinks', 'Link_ID', 'Person_ID',
            'peopleLinks', 'getPeopleForLink',
            'geeby-deeby/edit-link/person-list.phtml'
        );
    }

    /**
     * Deal with link/series links
     *
     * @return mixed
     */
    public function seriesAction()
    {
        return $this->handleGenericLink(
            'serieslinks', 'Link_ID', 'Series_ID',
            'seriesLinks', 'getSeriesForLink',
            'geeby-deeby/edit-link/series-list.phtml'
        );
    }
}
