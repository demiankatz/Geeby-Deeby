<?php
/**
 * Person controller
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
 * Person controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PersonController extends \GeebyDeeby\Controller\PersonController
{
    /**
     * Build the primary resource in an RDF graph.
     *
     * @param \EasyRdf\Graph $graph Graph to populate
     * @param object         $view  View model populated with information.
     * @param mixed          $class Class(es) for resource.
     *
     * @return \EasyRdf\Resource
     */
    protected function addPrimaryResourceToGraph($graph, $view, $class = 'foaf:Person')
    {
        $authName = $view->person['Last_Name'];
        $first = trim($view->person['First_Name'] . ' ' . $view->person['Middle_Name']);
        if (!empty($first)) {
            $authName .= ', ' . $first;
        }
        $authName .= $view->person['Extra_Details'];

        // Only focus name to real person if the person is known to be real (through cited
        // attributions) or no known pseudonyms are recorded.
        $isRealPerson = count($view->citations) > 0 || count($view->realNames) == 0;
        if ($isRealPerson) {
            $person = parent::addPrimaryResourceToGraph($graph, $view, $class);
            $person->set('rda:preferredNameForTheAgent', $authName);
        }

        $id = $view->person['Person_ID'];
        $uri = $this->getServerUrl('person', ['id' => $id]) . '#name';
        $name = $graph->resource($uri, 'skos:Concept');
        if ($isRealPerson) {
            $name->add('foaf:focus', $person);
        }
        foreach ($view->realNames as $realName) {
            $realNameUri = $this->getServerUrl('person', ['id' => $realName['Person_ID']]);
            $name->add('foaf:focus', $graph->resource($realNameUri));
        }
        $name->set('rdfs:label', $authName);

        return $person;
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
