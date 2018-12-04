<?php
/**
 * Class for managing articles
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
 * @package  Articles
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby;

/**
 * Class for managing articles
 *
 * @category GeebyDeeby
 * @package  Articles
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class Articles
{
    /**
     * List of articles (and other non-sorting prefixes).
     *
     * @var array
     */
    protected $articles;

    /**
     * List of articles/non-sorting prefixes that shouldn't be followed with a space.
     *
     * @var array
     */
    protected $unspacedArticles;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->articles = array(
            'The','Los','Il','La','Le','L\'','El','De', 
            'Het','Een','Os','O','Un','Une','As','Uma',
            'An','A','¡','Les','"', 'I', 'Ein', 'Lo',
            'Un','Das','Die','Der','Den','Det','Et', 
            'Las','¿','¡La "', 'Els', 'The "', 'El "',
            'Una', 'Gli'
        );
        $this->unspacedArticles = array(
            "¡", "¿", "L'", '"', '¡La "', 'The "', 'El "'
        );
    }

    /**
     * Format a title with the article in the correct position.
     *
     * @param string $title Title to reformat
     *
     * @return string
     */
    public function formatTrailingArticles($title)
    {
        foreach ($this->articles as $art) {
            $suffix = ", " . $art;
            if (in_array($art, $this->unspacedArticles)) {
                $prefix = $art;
            } else {
                $prefix = $art . " ";
            }
            $suflen = strlen($suffix);
            if (substr($title, strlen($title)-$suflen) == $suffix) {
                return $prefix . substr($title, 0, strlen($title) - $suflen);
            }
        }
        // If we've reached this point, there was no article... return now!
        return $title;
    }

    /**
     * Append a string to another string without disturbing trailing articles.
     *
     * @param string $title Title to append to
     * @param string $extra Text to append
     *
     * @return string
     */
    public function articleAwareAppend($title, $extra)
    {
        foreach ($this->articles as $art) {
            $suffix = ", " . $art;
            $suflen = strlen($suffix);
            if (substr($title, strlen($title)-$suflen) == $suffix) {
                return substr($title, 0, strlen($title) - $suflen)
                    . $extra . $suffix;
            }
        }
        // If we've reached this point, there was no article... return now!
        return $title . $extra;
    }

    /**
     * Format a title to remove leading articles.
     *
     * @param string $title Title to strip
     *
     * @return string
     */
    public function stripLeadingArticles($title)
    {
        foreach ($this->articles as $art) {
            $article = in_array($art, $this->unspacedArticles)
                ? $art : $art . " ";
            $artlen = strlen($article);
            if (strtolower(substr($title, 0, $artlen)) == strtolower($article)) {
                $title = substr($title, $artlen, strlen($title) - $artlen);
            }
        }
        return $title;
    }
}