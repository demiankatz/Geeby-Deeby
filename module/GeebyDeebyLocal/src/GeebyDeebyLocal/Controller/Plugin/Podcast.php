<?php
/**
 * Action Helper - Podcast
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
 * @package  Controller_Plugins
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 */
namespace GeebyDeebyLocal\Controller\Plugin;
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
class Podcast extends AbstractPlugin
{
    /**
     * Load podcast metadata
     *
     * @param int    $limit  Number of results to return (0 for no limit)
     * @param string $filter Category to filter by (null for no filter)
     *
     * @return array
     */
    public function getMetadata($limit = 0, $filter = null)
    {
        $handle = fopen(__DIR__ . '/../../../../../../public/mp3/metadata', 'r');
        $result = array();
        while (true) {
            $current = array(
                'filename' => trim(fgets($handle)),
                'date' => trim(fgets($handle)),
                'category' => trim(fgets($handle)),
                'title' => trim(fgets($handle)),
                'author' => trim(fgets($handle)),
                'duration' => trim(fgets($handle)),
                'description' => trim(fgets($handle))
            );
            fgets($handle);
            if (empty($current['filename'])) {
                break;
            }
            if (null !== $filter && $current['category'] !== $filter) {
                continue;
            }
            $filename = realpath(
                __DIR__ . '/../../../../../public/mp3/' . $current['filename']
            );
            $current['size'] = filesize($filename);
            $current['image'] = str_replace('.mp3', '.jpg', $current['filename']);
            $firstWord = current(explode(' ', $current['category']));
            $current['category_route'] = 'podcast-'
                . strtolower(preg_replace('/[^a-zA-Z]/', '', $firstWord));
            $credits = str_replace('.mp3', '.html', $filename);
            $current['credits'] = file_exists($credits)
                ? file_get_contents($credits) : false;
            $result[] = $current;
            if ($limit > 0 && count($result) == $limit) {
                break;
            }
        }
        return $result;
    }
}
