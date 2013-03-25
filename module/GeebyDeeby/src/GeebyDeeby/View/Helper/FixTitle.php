<?php
/**
 * Title display view helper
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
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\View\Helper;

/**
 * Title display view helper
 *
 * @category GeebyDeeby
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class FixTitle extends \Zend\View\Helper\AbstractHelper
{
    /**
     * Articles object.
     *
     * @var \GeebyDeeby\Articles
     */
    protected $articles;

    /**
     * Constructor
     *
     * @param \GeebyDeeby\Articles $articles Articles object
     */
    public function __construct(\GeebyDeeby\Articles $articles)
    {
        $this->articles = $articles;
    }

    /**
     * Format a title with the article in the correct position.
     *
     * @param string $title Title to reformat
     *
     * @return string
     */
    public function __invoke($title)
    {
        return $this->articles->formatTrailingArticles($title);
    }
}