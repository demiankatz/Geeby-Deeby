<?php
/**
 * Script manager (wrapper around HeadScript helper).
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2017.
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
 * Script manager (wrapper around HeadScript helper).
 *
 * @category GeebyDeeby
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ScriptManager extends \Zend\View\Helper\AbstractHelper
{
    /**
     * Base path
     *
     * @var string
     */
    protected $basePath;

    /**
     * HeadScript helper
     *
     * @var object
     */
    protected $headScript;

    /**
     * Constructor
     *
     * @param string $basePath  Base path
     * @param object $headScript HeadScript helper
     */
    public function __construct($basePath, $headScript)
    {
        $this->basePath = $basePath;
        $this->headScript = $headScript;
    }

    /**
     * Add an array of scripts to the HeadScript helper.
     *
     * @param array $scripts Array of script names (omit path and .js suffix)
     * @param bool  $prepend Should we prepend rather than append scripts?
     *
     * @return void
     */
    public function add($scripts, $prepend = false)
    {
        foreach ($prepend ? array_reverse($scripts) : $scripts as $script) {
            $date = filemtime(__DIR__ . "/../../../../../../public/js/$script.js");
            $method = $prepend ? 'prependFile' : 'appendFile';
            $this->headScript->$method($this->basePath . "/js/$script.js?_=$date");
        }
    }
}
