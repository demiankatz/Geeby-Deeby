<?php

/**
 * Script manager (wrapper around HeadScript helper).
 *
 * PHP version 8
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
 * along with this program; if not, see
 * <https://www.gnu.org/licenses/>.
 *
 * @category GeebyDeeby
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\View\Helper;

use GeebyDeeby\ServiceManager\Factory\Autowire;
use Laminas\View\Helper\BasePath;
use Laminas\View\Helper\HeadScript;

/**
 * Script manager (wrapper around HeadScript helper).
 *
 * @category GeebyDeeby
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ScriptManager
{
    /**
     * Base path
     *
     * @var string
     */
    protected $basePath;

    /**
     * Constructor
     *
     * @param string|BasePath $basePath   BasePath view helper (or base path string)
     * @param HeadScript      $headScript HeadScript view helper
     */
    public function __construct(
        #[Autowire(container: 'ViewHelperManager', service: BasePath::class)]
        string|BasePath $basePath,
        #[Autowire(container: 'ViewHelperManager')]
        protected HeadScript $headScript
    ) {
        $this->basePath = $basePath instanceof BasePath ? ($basePath)() : $basePath;
    }

    /**
     * Make helper invokable.
     *
     * @return static
     */
    public function __invoke(): static
    {
        return $this;
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
