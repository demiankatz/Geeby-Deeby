<?php
/**
 * Podcast controller
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
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeebyLocal\Controller;
use DateTime, Zend\Feed\Writer\Feed;

/**
 * Podcast controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PodcastController extends \GeebyDeeby\Controller\AbstractBase
{
    /**
     * About page
     *
     * @return mixed
     */
    public function indexAction()
    {
        return $this->createViewModel(
            array('latest' => current($this->getPodcastMetadata(1)))
        );
    }

    /**
     * RSS feed
     *
     * @return mixed
     */
    public function rssAction()
    {
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-type', 'text/xml');
        $response->setContent($this->getRssFeed()->export('rss'));
        return $response;
    }

    /**
     * Show information about an episode
     *
     * @return mixed
     */
    public function aboutAction()
    {
        $episode = $this->params()->fromQuery('file');
        $details = false;
        $meta = $this->getPodcastMetadata();
        foreach ($meta as $i => $current) {
            if ($current['filename'] == $episode) {
                $details = $current;
                break;
            }
        }
        return $this->createViewModel(
            array(
                'prev' => $details && isset($meta[$i + 1]) ? $meta[$i + 1] : false,
                'details' => $details,
                'next' => $details && isset($meta[$i - 1]) ? $meta[$i - 1] : false,
            )
        );
    }

    /**
     * Generate RSS feed
     *
     * @return Feed
     */
    protected function getRssFeed()
    {
        // Get access to view helper:
        $serverUrl = $this->getServiceLocator()->get('viewmanager')->getRenderer()
            ->plugin('serverurl');

        $feed = new Feed();
        $feed->setTitle('The Spare Change Library');
        $desc = 'The dime novel and popular literature podcast.';
        $feed->setDescription($desc);
        $feed->setItunesSummary($desc);
        $feed->setLink($this->url()->fromRoute('podcast'));
        $feed->setFeedLink(
            $serverUrl($this->url()->fromRoute('podcast-rss')), 'rss'
        );
        $feed->setItunesCategories(array('Literature'));
        $feed->setLanguage('en');

        $baseUrl = $serverUrl($this->url()->fromRoute('home'));
        $feed->setItunesImage($baseUrl . 'mp3/SCL-Feed.jpg');

        $aboutUrl = $serverUrl($this->url()->fromRoute('podcast-about'));
        $filter = $this->params()->fromQuery('cat');
        foreach ($this->getPodcastMetadata(0, $filter) as $current) {
            $entry = $feed->createEntry();
            $entry->setTitle($current['category'] . ': ' . $current['title']);
            $entry->setLink($aboutUrl . '?file=' . urlencode($current['filename']));
            $entry->setDateModified(strtotime($current['date']));
            $mp3 = $baseUrl . 'mp3/' . $current['filename'];
            $entry->setEnclosure(
                array('uri' => $mp3, 'length' => $current['size'], 'type' => 'audio/mpeg')
            );
            $entry->addItunesAuthor($current['author']);
            $entry->setItunesDuration($current['duration']);
            $entry->setItunesSummary($current['description']);
            $feed->addEntry($entry);
        }

        return $feed;
    }

    /**
     * Load podcast metadata
     *
     * @param int    $limit  Number of results to return (0 for no limit)
     * @param string $filter Category to filter by (null for no filter)
     *
     * @return array
     */
    public function getPodcastMetadata($limit = 0, $filter = null)
    {
        $handle = fopen(__DIR__ . '/../../../../../public/mp3/metadata', 'r');
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
            $result[] = $current;
            fgets($handle);
            if ($limit > 0 && count($result) == $limit) {
                break;
            }
        }
        return $result;
    }
}
