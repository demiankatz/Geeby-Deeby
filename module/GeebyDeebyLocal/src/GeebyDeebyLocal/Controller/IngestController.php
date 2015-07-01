<?php
/**
 * Ingest controller
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
use Zend\Console\Console;

/**
 * Ingest controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class IngestController extends \GeebyDeeby\Controller\AbstractBase
{
    /**
     * Standard ingest action.
     *
     * @return mixed
     */
    public function indexAction()
    {
        $edition = 5865;
        $rawMods = $this->getModsForEdition('http://dimenovels.org/Edition/' . $edition);
        if (!$rawMods) {
            Console::writeLine('Could not retrieve MODS.');
            return;
        }
        $mods = simplexml_load_string($rawMods);
        $editionObj = $this->getDbTable('edition')->getByPrimaryKey($edition);
        $item = $this->getItemForEdition($editionObj);

        $details = [];
        foreach ($mods->xpath('/mods:mods') as $current) {
            $currentDetails = $this->extractDetailsFromMods($current);
            if (!empty($currentDetails)) {
                $details[] = $currentDetails;
            }
        }
        foreach ($mods->xpath('/mods:mods/mods:relatedItem[@type="constituent"]') as $current) {
            $currentDetails = $this->extractDetailsFromMods($current);
            if (!empty($currentDetails)) {
                $details[] = $currentDetails;
            }
        }
    }

    protected function extractDetailsFromMods($mods)
    {
        $details = [];
        $title = $this->extractTitleInfoFromMods($mods);
        if (!empty($title)) {
            $details['title'] = $title;
        }
        $authors = $this->extractAuthorsFromMods($mods);
        if (!empty($authors)) {
            $details['authors'] = $authors;
        }
        return $details;
    }

    protected function checkTitle($item, $title)
    {
        $itemTitle = (isset($item['Item_AltName']) && !empty($item['Item_AltName']))
            ? $item['Item_AltName'] : $item['Item_Name'];
        Console::writeLine('Comparing to ' . $itemTitle);
    }

    protected function extractAuthorsFromMods($mods)
    {
        $authors = [];
        $matches = $mods->xpath('mods:name');
        foreach ($matches as $current) {
            $role = $current->xpath('mods:role/mods:roleTerm');
            if (isset($role[0]) && (string)$role[0] == 'author') {
                $currentAuthor = [];
                $uri = $current->xpath('@valueURI');
                if (isset($uri[0])) {
                    $currentAuthor['uri'] = (string)$uri[0];
                }
                $currentAuthor['name'] = implode(', ', $current->xpath('mods:namePart'));
                if (!empty($currentAuthor['name'])) {
                    Console::writeLine(
                        'Extracted author: ' . $currentAuthor['name']
                        . (empty($currentAuthor['uri']) ? '' : " ({$currentAuthor['uri']})")
                    );
                    $authors[] = $currentAuthor;
                }
            }
        }
        return $authors;
    }

    protected function extractTitleInfoFromMods($mods)
    {
        $matches = $mods->xpath('mods:titleInfo[not(@type="alternative")]');
        if (empty($matches)) {
            return '';
        }
        $title = (string)$matches[0]->xpath('mods:title')[0];
        $article = $matches[0]->xpath('mods:nonSort');
        $full = $title .= (empty($article) ? '' : ', ' . (string)$article[0]);
        Console::writeLine("Extracted title: $full");
        return $full;
    }

    protected function getItemForEdition($rowObj)
    {
        $itemTable = $this->getDbTable('item');
        $itemObj = $itemTable->getByPrimaryKey($rowObj->Item_ID);
        $item = $itemObj->toArray();
        if (!empty($rowObj->Preferred_Item_AltName_ID)) {
            $ian = $this->getDbTable('itemsalttitles');
            $tmpRow = $ian->select(
                array('Sequence_ID' => $rowObj->Preferred_Item_AltName_ID)
            )->current();
            $item['Item_AltName'] = $tmpRow['Item_AltName'];
        }
        return $item;
    }

    protected function getModsForEdition($edition)
    {
        // Get MODS identifier from Solr:
        $settings = json_decode(file_get_contents(__DIR__ . '/settings.json'));
        $query = $settings->solrQueryField . ':"' . $edition . '"';
        $field = $settings->solrIdField;
        $url = (string)$settings->solrUrl . '?q=' . urlencode($query) . '&wt=json'
            . '&fl=' . urlencode($field);
        Console::writeLine("Querying {$settings->solrUrl} for $query...");
        $solr = json_decode(file_get_contents($url));
        $pid = isset($solr->response->docs[0]->PID)
            ? $solr->response->docs[0]->PID : false;
        if (!$pid) {
            return false;
        }

        // Retrieve MODS from repository:
        $modsUrl = sprintf($settings->modsUrl, $pid);
        Console::writeLine("Retrieving $modsUrl...");
        $mods = file_get_contents($modsUrl);
        return $mods;
    }
}
