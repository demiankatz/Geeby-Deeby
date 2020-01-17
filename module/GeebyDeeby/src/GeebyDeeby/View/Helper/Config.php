<?php
/**
 * Configuration view helper
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
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\View\Helper;

/**
 * Configuration view helper
 *
 * @category GeebyDeeby
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class Config extends \Zend\View\Helper\AbstractHelper
{
    /**
     * Configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * Constructor
     *
     * @param array $config Configuration
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get a setting
     *
     * @param string $key Configuration key
     *
     * @return mixed
     */
    public function __invoke($key)
    {
        return isset($this->config[$key]) ? $this->config[$key] : null;
    }
}
