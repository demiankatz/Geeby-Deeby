<?php

/**
 * Legacy controller (for compatibility with old gamebooks.org filenames)
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

namespace GeebyDeebyLegacy\Controller;

/**
 * Legacy controller (for compatibility with old gamebooks.org filenames)
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class LegacyController extends \GeebyDeeby\Controller\AbstractBase
{
    /**
     * Legacy router action
     *
     * @return mixed
     */
    public function indexAction()
    {
        $path = explode('/', $this->params()->fromRoute('filename'));
        $file = array_pop($path);
        switch ($file) {
            case 'list_articles.php':
            case 'list_autos.php':
            case 'list_scans.php':
            case 'show_ads.php': // TODO: make this visual again
                return $this->redirect()->toRoute('files');
            case 'list_categories.php':
                return $this->redirect()->toRoute('categories');
            case 'list_countries.php':
                return $this->redirect()->toRoute('countries');
            case 'list_items.php':
                return $this->redirect()->toRoute('items');
            case 'list_languages.php':
                return $this->redirect()->toRoute('languages');
            case 'list_people.php':
                return $this->redirect()->toRoute('people');
            case 'list_people_bios.php':
                return $this->redirect()->toRoute('people', ['extra' => 'Bios']);
            case 'list_publishers.php':
                return $this->redirect()->toRoute('publishers');
            case 'list_series.php':
                return $this->redirect()->toRoute('series');
            case 'list_types.php':
                return $this->redirect()->toRoute('materials');
            case 'list_users.php':
                return $this->redirect()->toRoute('users');
            case 'list_years.php':
                return $this->redirect()->toRoute('items', ['extra' => 'ByYear']);
            case 'new_reviews.php':
                return $this->redirect()->toRoute('reviews');
            case 'show_category.php':
                return $this->redirect()->toRoute(
                    'category',
                    ['id' => $this->params()->fromQuery('id')]
                );
            case 'show_checklist.php':
                // TODO
                break;
            case 'show_country.php':
                return $this->redirect()->toRoute(
                    'country',
                    ['id' => $this->params()->fromQuery('id')]
                );
            case 'show_faqs.php':
                return $this->redirect()->toRoute('faqs');
            case 'show_isbn_links.php':
                return $this->redirect()->toRoute(
                    'item',
                    [
                    'id' => 'Unknown',
                    'action' => 'ISBNDetails',
                    'extra' => $this->params()->fromQuery('isbn'),
                    ]
                );
            case 'show_item.php':
                return $this->redirect()
                ->toRoute('item', ['id' => $this->params()->fromQuery('id')]);
            case 'show_language.php':
                return $this->redirect()->toRoute(
                    'language',
                    ['id' => $this->params()->fromQuery('id')]
                );
            case 'show_links.php':
                return $this->redirect()->toRoute('links');
            case 'show_person.php':
                return $this->redirect()->toRoute(
                    'person',
                    ['id' => $this->params()->fromQuery('id')]
                );
            case 'show_platform.php':
                return $this->redirect()->toRoute(
                    'platform',
                    ['id' => $this->params()->fromQuery('id')]
                );
            case 'show_publisher.php':
                return $this->redirect()->toRoute(
                    'publisher',
                    ['id' => $this->params()->fromQuery('id')]
                );
            case 'show_series.php':
                $id = $this->params()->fromQuery('id');
                if (!$id) {
                    $name = $this->params()->fromQuery('name');
                    $table = $this->getDbTable('series');
                    $record = $table->select(['Series_Name' => $name])->toArray();
                    if (isset($record[0]['Series_ID'])) {
                        $id = $record[0]['Series_ID'];
                    } else {
                        break;
                    }
                }
                return $this->redirect()
                ->toRoute('series', ['id' => $id]);
            case 'show_series_images.php':
                return $this->redirect()->toRoute(
                    'series',
                    [
                    'id' => $this->params()->fromQuery('id'),
                    'extra' => 'Images',
                    ]
                );
            case 'show_type.php':
                return $this->redirect()->toRoute(
                    'material',
                    ['id' => $this->params()->fromQuery('id')]
                );
            case 'show_user.php':
                return $this->redirect()->toRoute(
                    'user',
                    ['id' => $this->params()->fromQuery('id')]
                );
            case 'show_user_buyers.php':
                return $this->redirect()->toRoute(
                    'user',
                    [
                    'id' => $this->params()->fromQuery('id'),
                    'extra' => 'Buyers',
                    ]
                );
            case 'show_user_collection.php':
                return $this->redirect()->toRoute(
                    'user',
                    [
                    'id' => $this->params()->fromQuery('id'),
                    'extra' => 'Collection',
                    ]
                );
            case 'show_user_comments.php':
                return $this->redirect()->toRoute(
                    'user',
                    [
                    'id' => $this->params()->fromQuery('id'),
                    'extra' => 'Comments',
                    ]
                );
            case 'show_user_reviews.php':
                $id = $this->params()->fromQuery('id', 'all');
                if ($id === 'all') {
                    return $this->redirect()
                    ->toRoute('items', ['extra' => 'Reviews']);
                }
                return $this->redirect()
                ->toRoute('user', ['id' => $id, 'extra' => 'Reviews']);
            case 'show_user_sales.php':
                return $this->redirect()->toRoute(
                    'user',
                    [
                    'id' => $this->params()->fromQuery('id'),
                    'extra' => 'Extras',
                    ]
                );
            case 'show_user_sellers.php':
                return $this->redirect()->toRoute(
                    'user',
                    [
                    'id' => $this->params()->fromQuery('id'),
                    'extra' => 'Sellers',
                    ]
                );
            case 'thanks.php':
                return $this->redirect()->toRoute(
                    'thanks'
                );
        }
        return $this->forwardTo(__NAMESPACE__ . '\Legacy', 'notfound');
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
