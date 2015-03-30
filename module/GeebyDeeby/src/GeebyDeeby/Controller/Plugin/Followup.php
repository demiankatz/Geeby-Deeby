<?php
/**
 * Action Helper - Followup
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
 * @package  Controller_Plugins
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 */
namespace GeebyDeeby\Controller\Plugin;
use Zend\Mvc\Controller\Plugin\AbstractPlugin, Zend\Session\Container;

/**
 * Zend action helper to deal with login followup; responsible for remembering URLs
 * before login and then redirecting the user to the appropriate place afterwards.
 *
 * @category GeebyDeeby
 * @package  Controller_Plugins
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 */
class Followup extends AbstractPlugin
{
    /**
     * Container for followup information
     *
     * @var Container
     */
    protected $session;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->session = new Container('Followup');
    }

    /**
     * Retrieve the stored followup information.
     *
     * @return \Zend\Session\Container
     */
    public function retrieve()
    {
        return $this->session;
    }

    /**
     * Store the current URL (and optional additional information) in the session
     * for use following a successful login.
     *
     * @param array  $extras      Associative array of extra fields to store.
     * @param string $overrideUrl URL to store in place of current server URL (null
     * for no override)
     *
     * @return void
     */
    public function store($extras = array(), $overrideUrl = null)
    {
        // Store the current URL:
        $this->session->url = !empty($overrideUrl)
            ? $overrideUrl : $this->getController()->getServerUrl();

        // Store the extra parameters:
        foreach ($extras as $key => $value) {
            $this->session->$key = $value;
        }
    }
}