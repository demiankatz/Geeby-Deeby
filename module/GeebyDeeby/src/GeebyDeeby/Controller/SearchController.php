<?php
/**
 * Search controller
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
 * Search controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SearchController extends AbstractBase
{
    /**
     * Search results routing action
     *
     * @return mixed
     */
    public function indexAction()
    {
        // Grab input and send it to the layout:
        $this->layout()->type = $this->params()->fromPost('SearchType');
        $this->layout()->query = $this->params()->fromPost('SearchQuery');

        // Validate query length:
        if (strlen($this->layout()->query) < 3) {
            return $this->createViewModel(
                array('error' => 'Search string must be at least 3 characters long.')
            );
        }

        // Whitelist of valid type actions:
        $type = strtolower($this->layout()->type);
        switch ($type) {
        case 'isbn':
        // TODO -- remaining search options:
        //case 'keyword':
        //case 'person':
        //case 'title':
            return $this->forwardTo(__NAMESPACE__ . '\Search', $type);
        }

        // If we got this far, no valid type was found:
        return $this->createViewModel(
            array('error' => 'This search type is unsupported.')
        );
    }

    /**
     * ISBN search results
     *
     * @return mixed
     */
    protected function isbnAction()
    {
        $q = $this->layout()->query;
        $view = $this->createViewModel();
        $view->results = $this->getDbTable('itemsisbns')->searchForItems($q);
        return $view;
    }
}