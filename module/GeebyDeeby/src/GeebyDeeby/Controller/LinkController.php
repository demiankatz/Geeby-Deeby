<?php

/**
 * Link controller
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
 * Link controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class LinkController extends AbstractBase
{
    /**
     * Link list
     *
     * @return mixed
     */
    public function listAction()
    {
        // Get link group configuration:
        $config = $this->serviceLocator->get('config');
        $groups = $config['geeby-deeby']['link_groups'] ?? [];
        $extra = $this->params()->fromRoute('extra');
        $group = ($extra && isset($groups[$extra]))
            ? $groups[$extra] : false;

        // Initialize values:
        $table = $this->getDbTable('link');

        // Retrieve the relevant links:
        $links = $table->getListByType(
            $group['typeMatch'] ?? null
        );

        // Send back the details:
        return $this->createViewModel(
            [
                'links' => $links,
                'title' => $group['title'] ?? 'Link List',
                'desc' => $group['desc'] ?? '',
                'typeTrim' => $group['typeTrim'] ?? 0,
            ]
        );
    }
}
