<?php
/**
 * Edit predicate controller
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2015.
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
namespace GeebyDeeby\Controller;

/**
 * Edit predicate controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditPredicateController extends AbstractBase
{
    /**
     * Display a list of notes
     *
     * @return mixed
     */
    public function listAction()
    {
        return $this->getGenericList(
            'predicate', 'predicates', 'geeby-deeby/edit-predicate/render-predicates'
        );
    }

    /**
     * Operate on a single predicate
     *
     * @return mixed
     */
    public function indexAction()
    {
        $assignMap = array('predicate' => 'Predicate', 'abbrev' => 'Predicate_Abbrev');
        return $this->handleGenericItem('predicate', $assignMap, 'predicate');
    }
}
