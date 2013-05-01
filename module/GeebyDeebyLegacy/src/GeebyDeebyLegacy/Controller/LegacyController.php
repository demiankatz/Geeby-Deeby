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
            return $this->redirect()->toRoute('people', array('extra' => 'Bios'));
        case 'list_publishers.php':
            return $this->redirect()->toRoute('publishers');
        case 'list_series.php':
            return $this->redirect()->toRoute('series');
        case 'list_types.php':
            return $this->redirect()->toRoute('materials');
        case 'list_users.php':
            return $this->redirect()->toRoute('users');
        case 'list_years.php':
            return $this->redirect()->toRoute('items', array('extra' => 'ByYear'));
        case 'new_reviews.php':
            return $this->redirect()->toRoute('reviews');
        case 'show_series.php':
            return $this->redirect()->toRoute(
                'series', array('id' => $this->params()->fromQuery('id'))
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
