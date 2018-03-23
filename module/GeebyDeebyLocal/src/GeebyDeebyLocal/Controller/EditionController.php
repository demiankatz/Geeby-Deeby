<?php
/**
 * Edition controller
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
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeebyLocal\Controller;

/**
 * Edition controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionController extends \GeebyDeeby\Controller\EditionController
{
    const LC_NAME_AUTHORITY = 1;

    /**
     * Build the primary resource in an RDF graph.
     *
     * @param \EasyRdf\Graph $graph Graph to populate
     * @param object         $view  View model populated with information.
     * @param mixed          $class Class(es) for resource.
     *
     * @return \EasyRdf\Resource
     */
    protected function addPrimaryResourceToGraph($graph, $view, $class = array())
    {
        $articleHelper = $this->getServiceLocator()->get('GeebyDeeby\Articles');
        $class[] = 'dime:Edition';
        $edition = parent::addPrimaryResourceToGraph($graph, $view, $class);
        foreach ($view->credits as $credit) {
            $personUri = $this->getServerUrl('person', ['id' => $credit['Person_ID']]);
            $edition->add('dime:HasCredit', $graph->resource($personUri));
        }
        if (!empty($view->item)) {
            $itemUri = $this->getServerUrl('item', ['id' => $view->item['Item_ID']]);
            $itemType = $this->getDbTable('materialtype')->getByPrimaryKey(
                $view->item['Material_Type_ID']
            );
            $predicate = $itemType['Material_Type_Name'] == 'Issue'
                ? 'dime:IsEditionOf' : 'dime:IsRealizationOfCreativeWork';
            $edition->set($predicate, $graph->resource($itemUri));
            $itemTitle = empty($view->item['Item_AltName'])
                ? $view->item['Item_Name'] : $view->item['Item_AltName'];
            if (!empty($itemTitle)) {
                $edition->set('rda:titleProper', $itemTitle);
            }
        }
        if (isset($view->series)) {
            $seriesUri = $this->getServerUrl('series', ['id' => $view->series['Series_ID']]);
            $edition->add('dime:HasSeries', $graph->resource($seriesUri));
            if ($view->edition['Position'] > 0) {
                $edition->add('rda:numberingWithinSeries', (int)$view->edition['Position']);
            }
            $seriesTitle = empty($view->series['Series_AltName'])
                ? $view->series['Series_Name'] : $view->series['Series_AltName'];
            if (!empty($seriesTitle)) {
                $edition->set('rda:titleProperOfSeries', $seriesTitle);
            }
        }
        foreach ($view->publishers as $publisher) {
            $pubUri = $this->getServerUrl('publisher', ['id' => $publisher['Publisher_ID']]);
            $edition->add('rda:publisher', $graph->resource($pubUri));
        }
        foreach ($view->children as $child) {
            $childUri = $this->getServerUrl('edition', ['id' => $child['Edition_ID']]);
            $edition->add('rda:containerOf', $graph->resource($childUri));
        }
        if ($view->parent) {
            $parentUri = $this->getServerUrl('edition', ['id' => $view->parent['Edition_ID']]);
            $edition->add('rda:containedIn', $graph->resource($parentUri));
        }
        if (!empty($view->edition['Edition_Length'])) {
            $edition->add('rda:extent', $view->edition['Edition_Length']);
        }
        foreach ($view->dates as $date) {
            if ($date['Year'] > 0) {
                $dateStr = $date['Year'];
                foreach (['Month', 'Day'] as $field) {
                    if (!empty($date[$field])) {
                        $dateStr .= '-' . substr('0' . $date[$field], -2);
                    }
                }
                $edition->add('rda:dateOfPublication', ['type' => 'literal', 'value' => $dateStr, 'datatype' => 'xsd:date']);
            }
        }
        return $edition;
    }

    /**
     * Add title details to an XML object.
     *
     * @param \SimpleXMLElement $xml   An empty <mods:titleInfo> element
     * @param string            $title The title to format
     *
     * @return void
     */
    protected function addModsTitle($xml, $title)
    {
        list($article, $body) = $this->getServiceLocator()->get('GeebyDeeby\Articles')
            ->separateArticle($title, false);
        if (!empty($article)) {
            $xml->nonSort = $article;
        }
        $bodyParts = explode(':', $body, 2);
        $xml->title = trim($bodyParts[0]);
        if (!empty($bodyParts[1])) {
            $xml->subTitle = trim($bodyParts[1]);
        }
    }

    /**
     * Add part details to an XML object.
     *
     * @param \SimpleXMLElement $xml  An empty <mods:part> element
     * @param string            $part Part information
     *
     * @return void
     */
    protected function addModsPart($xml, $part)
    {
        $chunks = explode(' ', $part);
        if (stristr($chunks[0], 'chapter')) {
            $type = 'chapter';
        } else {
            $type = 'number';
        }
        if (!empty($chunks[1])) {
            $xml->detail->number = $chunks[1];
            $xml->detail['type'] = $type;
        }
    }

    /**
     * Add names to the child record of a MODS object.
     *
     * @param \SimpleXMLElement $xml       A <mods:relatedItem> element
     * @param int               $editionID The edition whose credits should be added
     *
     * @return void
     */
    protected function addModsNames($xml, $editionID)
    {
        $credits = $this->getDbTable('editionscredits')
            ->getCreditsForEdition($editionID);
        foreach ($credits as $credit) {
            $name = $xml->addChild('name');
            if ($credit->Authority_ID == self::LC_NAME_AUTHORITY) {
                $name['authority'] = 'naf';
                $name['authorityURI'] = 'http://id.loc.gov/authorities/names';
                $URIs = $this->getDbTable('peopleuris')
                    ->getURIsForPerson($credit->Person_ID)->toArray();
                $name['valueURI'] = isset($URIs[0]) ? $URIs[0]['URI'] : 'LC URI MISSING!!';
            } else {
                $name['authority'] = 'dime';
                $name['authorityURI'] = 'https://dimenovels.org/Person';
                $name['valueURI'] = 'https://dimenovels.org/Person/'
                    . $credit->Person_ID;
            }
            $name['type'] = 'personal';
            $mainName = $credit->Last_Name;
            $firstName = trim($credit->First_Name . ' ' . $credit->Middle_Name);
            if (!empty($firstName)) {
                $mainName .= ', ' . $firstName;
            }
            $rawExtra = trim($credit->Extra_Details);
            if (!empty($rawExtra)) {
                if (substr($rawExtra, 0, 1) == '(') {
                    $mainName .= ' ' . $rawExtra;
                } else {
                    $extra = (substr($rawExtra, 0, 2) == ', ')
                        ? substr($rawExtra, 2) : $rawExtra;
                    $extraType = (preg_match('/[0-9]{4}/', $extra))
                        ? 'date' : 'termsOfAddress';
                }
            }
            $name->addChild('namePart', $mainName);
            if (!empty($extra)) {
                $part = $name->addChild('namePart', $extra);
                $part['type'] = $extraType;
            }
            $name->role->roleTerm = strtolower($credit->Role_Name);
            $name->role->roleTerm['type'] = 'text';
            
        }
    }

    /**
     * Add genre info to the child record of a MODS object.
     *
     * @param \SimpleXMLElement $xml    A <mods:relatedItem> element
     * @param int               $itemID The item whose tags should be added
     *
     * @return void
     */
    protected function addModsGenres($xml, $itemID)
    {
        $knownForms = ['Poem', 'Sketch'];
        $knownGenres = ['Detective and mystery stories'];
        $tags = $this->getDbTable('itemstags')->getTags($itemID);
        foreach ($tags as $tag) {
            if (in_array($tag->Tag, $knownForms)) {
                $modsTag = 'genre';
                $auth = 'aat';
            } else if (in_array($tag->Tag, $knownGenres)) {
                $modsTag = 'genre';
                $auth = null;
            } else {
                $modsTag = 'subject';
                $auth = null;
            }
            $tag = $xml->addChild($modsTag, $tag->Tag);
            if (!empty($auth)) {
                $tag['authority'] = $auth;
            }
        }
    }

    /**
     * Add a child record to a MODS object.
     *
     * @param \SimpleXMLElement $xml   A <mods:mods> element
     * @param object            $child Child record information in object form
     *
     * @return void
     */
    protected function addModsChild($xml, $child)
    {
        $current = $xml->addChild('relatedItem');
        $current['type'] = 'constituent';
        $this->addModsTitle($current->addChild('titleInfo'), $child->Item_Name);
        $this->addModsNames($current, $child->Edition_ID);
        $this->addModsGenres($current, $child->Item_ID);
        if (!empty($child->Extent_In_Parent)) {
            $this->addModsPart($current->addChild('part'), $child->Extent_In_Parent);
        }
    }

    /**
     * Construct a MODS record.
     *
     * @return mixed
     */
    public function modsAction()
    {
        $view = $this->getViewModelWithEditionAndDetails();
        $template = '<mods:mods xmlns="http://www.loc.gov/mods/v3" xmlns:mods="http://www.loc.gov/mods/v3" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xlink="http://www.w3.org/1999/xlink" xsi:schemaLocation="http://www.loc.gov/mods/v3 http://www.loc.gov/standards/mods/v3/mods-3-5.xsd"></mods:mods>';
        $xml = simplexml_load_string($template);
        $this->addModsTitle($xml->addChild('titleInfo'), $view->item['Item_Name']);
        $xml->typeOfResource = 'text';
        if (!empty($view->children)) {
            foreach ($view->children as $child) {
                $this->addModsChild($xml, $child);
            }
        }
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-type', 'text/xml');
        $response->setContent($xml->asXml());
        return $response;
    }

    /**
     * Get the module namespace for use in template resolution. See
     * \GeebyDeebyLocal\View\InjectTemplateListener. This allows us to extend core
     * controllers without duplicating templates.
     *
     * @return string
     */
    public static function getModuleTemplateNamespace()
    {
        return 'geeby-deeby';
    }
}