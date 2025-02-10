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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Controller;

use function strlen;

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
     * Tokenize a search query.
     *
     * @param string $q Query to tokenize
     *
     * @return array
     */
    protected function tokenize($q)
    {
        $q = preg_replace('/\s+/', ' ', $q);
        return explode(' ', $q);
    }

    /**
     * Retrieve creator details by AJAX.
     *
     * @return mixed
     */
    public function creatorAjaxAction()
    {
        $nameHelper = $this->serviceLocator->get('ViewHelperManager')->get('showPerson');
        $post = $this->params()->fromPost();
        $ids = explode(',', $post['ids']);
        $index = [];
        foreach ($this->getDbTable('person')->getListForItemIds($ids) as $row) {
            $index[$row['Item_ID']] ??= [];
            $index[$row['Item_ID']][] = $row;
        }
        $formatPerson = function ($person) use ($nameHelper) {
            return htmlspecialchars(($nameHelper)($person));
        };
        $response = [];
        foreach ($index as $id => $data) {
            $response[$id] = implode('; ', array_map($formatPerson, $data));
        }
        return $this->jsonDie($response, true);
    }

    /**
     * Retrieve edition attribute details by AJAX.
     *
     * @return mixed
     */
    public function editionAttributeAjaxAction()
    {
        $post = $this->params()->fromPost();
        $attributeId = $post['attribute_id'];
        $ids = explode(',', $post['ids']);
        $result = [];
        foreach ($this->getDbTable('editionsattributesvalues')->getAttributesForItem($ids, $attributeId) as $row) {
            // We only want to display one value per item, so it doesn't matter if we overwrite existing data here:
            $result[$row['Item_ID']] = '<b>' . htmlspecialchars($row['Editions_Attribute_Name']) . '</b>: '
                . htmlspecialchars($row['Editions_Attribute_Value']);
        }
        return $this->jsonDie($result, true);
    }

    /**
     * Keyword results
     *
     * @return mixed
     */
    public function keywordAction()
    {
        $tokens = $this->tokenize($this->layout()->query);
        $view = $this->createViewModel();
        $view->series = $this->getDbTable('series')->keywordSearch($tokens);
        $view->seriesAltTitles = $this->getDbTable('seriesalttitles')
            ->keywordSearch($tokens);
        $view->items = $this->getDbTable('item')->keywordSearch($tokens);
        $view->itemsAltTitles = $this->getDbTable('itemsalttitles')
            ->keywordSearch($tokens);
        $view->categories = $this->getDbTable('category')->keywordSearch($tokens);
        $view->people = $this->getDbTable('person')->keywordSearch($tokens);
        $view->tags = $this->getDbTable('tag')->keywordSearch($tokens);
        return $view;
    }

    /**
     * Search results routing action
     *
     * @return mixed
     */
    public function indexAction()
    {
        // Grab input and send it to the layout:
        $this->layout()->type = $this->params()->fromQuery('SearchType');
        $this->layout()->query = $this->params()->fromQuery('SearchQuery');

        // Validate query length:
        if (strlen($this->layout()->query) < 3) {
            return $this->createViewModel(
                ['error' => 'Search string must be at least 3 characters long.']
            );
        }

        // Whitelist of valid type actions:
        $type = strtolower($this->layout()->type);
        switch ($type) {
            case 'isbn':
            case 'keyword':
            case 'person':
            case 'title':
                return $this->forwardTo(__NAMESPACE__ . '\Search', $type);
        }

        // If we got this far, no valid type was found:
        return $this->createViewModel(
            ['error' => 'This search type is unsupported.']
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
        $view->results = $this->getDbTable('editionsisbns')->searchForItems($q);
        return $view;
    }

    /**
     * Person results
     *
     * @return mixed
     */
    public function personAction()
    {
        // Strip commas to prevent problems:
        $q = str_replace(',', ' ', $this->layout()->query);
        $tokens = $this->tokenize($q);
        $view = $this->createViewModel();
        $view->people = $this->getDbTable('person')->keywordSearch($tokens);
        return $view;
    }

    /**
     * Title results
     *
     * @return mixed
     */
    public function titleAction()
    {
        // Rather than tokenizing, we'll do a substring search after normalizing
        // for leading articles:
        $q = $this->serviceLocator->get('GeebyDeeby\Articles')
            ->stripLeadingArticles($this->layout()->query);
        $tokens = [$q];
        $view = $this->createViewModel();
        $view->series = $this->getDbTable('series')->keywordSearch($tokens);
        $view->seriesAltTitles = $this->getDbTable('seriesalttitles')
            ->keywordSearch($tokens);
        $view->items = $this->getDbTable('item')->keywordSearch($tokens);
        $view->itemsAltTitles = $this->getDbTable('itemsalttitles')
            ->keywordSearch($tokens);
        return $view;
    }
}
