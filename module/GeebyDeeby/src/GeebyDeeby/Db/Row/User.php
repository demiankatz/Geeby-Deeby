<?php
/**
 * Row Definition for Users
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
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\Db\Row;

/**
 * Row Definition for Users
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class User extends TableAwareGateway
{
    /**
     * Permissions
     *
     * @var \GeebyDeeby\Db\Row\UserGroup
     */
    protected $permissions = null;

    /**
     * Constructor
     *
     * @param \Zend\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('User_ID', 'Users', $adapter);
    }

    /**
     * Check if the user has the specified permission.
     *
     * @param string $permission The name of the permission to check.
     *
     * @return bool              True if action permitted, false otherwise.
     */
    public function hasPermission($permission)
    {
        // Make sure we have permissions available:
        $this->loadPermissions();

        // Check the permission:
        return isset($this->permissions->$permission)
            && !empty($this->permissions->$permission);
    }

    /**
     * Load permission data if not already available.
     *
     * @return void
     */
    public function loadPermissions()
    {
        // If permissions are already loaded, we're done here:
        if ($this->permissions !== null) {
            return;
        } elseif (isset($this->User_Group_ID) && !empty($this->User_Group_ID)) {
            $table = $this->getDbTable('usergroup');
            $this->permissions = $table->getByPrimaryKey($this->User_Group_ID);
            // Unset non-permission related fields:
            unset($this->permissions->User_Group_ID);
            unset($this->permissions->Group_Name);
            return;
        }

        // If we got this far, we were unable to find permissions -- default to
        // "no permissions."
        $this->permissions = new \ArrayObject();
    }
}
