<?php

/**
 * Generate "toggle link" view helper.
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2020.
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
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\View\Helper;

use GeebyDeeby\Db\Table\User;

/**
 * Generate "toggle link" view helper.
 *
 * @category GeebyDeeby
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ToggleLink extends \Laminas\View\Helper\AbstractHelper
{
    /**
     * User database table object
     *
     * @var User
     */
    protected $userTable;

    /**
     * Constructor.
     *
     * @param User $userTable User database table object
     */
    public function __construct(User $userTable)
    {
        $this->userTable = $userTable;
    }

    /**
     * Generate a "toggle link" for editing or viewing a record.
     *
     * @param string $route      Name of route to link to.
     * @param int    $id         Record ID to pass to route
     * @param string $label      Text of link (null to default based on route name)
     * @param string $permission Permission required to see link (null for none)
     *
     * @return string
     */
    public function __invoke(
        $route,
        $id,
        $label = null,
        $permission = 'Content_Editor'
    ) {
        // Not logged in? No link.
        if (!$this->view->auth()->hasIdentity()) {
            return '';
        }

        // Missing permission? No link.
        if (!$permission || !$this->checkPermission($permission)) {
            return '';
        }

        $url = $this->view->url($route, compact('id'));
        if ($label === null) {
            $label = (!str_contains($route, 'edit/') && $route !== 'edit')
                ? '[switch to public view]'
                : '[edit]';
        }
        return '<a class="toggleLink" href="' . $url . '">' . $label . '</a>';
    }

    /**
     * Does the current logged in user have the specified permission?
     *
     * @param string $permission Permission to check.
     *
     * @return bool
     */
    protected function checkPermission($permission)
    {
        $user = $this->userTable->getByPrimaryKey(
            $this->view->auth()->getIdentity()
        );
        return $user && $user->hasPermission($permission);
    }
}
