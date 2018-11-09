<?php
/**
 * Credit analysis view helper
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2018.
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
 * Credit analysis view helper
 *
 * @category GeebyDeeby
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class AnalyzeCredits extends \Zend\View\Helper\AbstractHelper
{
    /**
     * Pseudonyms table.
     *
     * @var object
     */
    protected $pseudonymsTable;

    /**
     * Real name information.
     *
     * @var array
     */
    protected $realNames = array();

    /**
     * Constructor
     *
     * @param object $pseudonyms Pseudonyms table.
     */
    public function __construct($pseudonyms)
    {
        $this->pseudonymsTable = $pseudonyms;
    }

    /**
     * Group credits by role and person ID.
     *
     * @param array $creatorIds IDs of known creators
     * @param array $credits    Credits to analyze
     *
     * @return array
     */
    protected function groupGredits($creatorIds, $credits)
    {
        $groupedCredits = array();
        foreach ($credits as $credit) {
            $personId = $credit['Person_ID'];
            $prefix = $this->isMatchingPerson($personId, $creatorIds)
                ? '' : 'Incorrectly Attributed ';
            $role = $prefix . $credit['Role_Name'];
            if (!isset($groupedCredits[$role])) {
                $groupedCredits[$role] = array();
            }
            if (!isset($groupedCredits[$role][$personId])) {
                $groupedCredits[$role][$personId] = array();
            }
            $groupedCredits[$role][$personId][] = $credit;
        }
        return $groupedCredits;
    }

    /**
     * Is $personId a match with one of the people in $creatorIds (i.e. is it
     * the same name, or a pseudonym of one of them)?
     *
     * @param int   $personId   Person to check
     * @param array $creatorIds Known good people
     *
     * @return bool
     */
    protected function isMatchingPerson($personId, $creatorIds)
    {
        if (in_array($personId, $creatorIds)) {
            return true;
        }
        foreach ($this->getRealPersonDetails($personId) as $real) {
            if (in_array($real['Person_ID'], $creatorIds)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Figure out the real person behind a pseudonym.
     *
     * @param int $person Person ID.
     *
     * @return array
     */
    protected function getRealPersonDetails($person)
    {
        if (!isset($this->realNames[$person])) {
            $this->realNames[$person] = $this->pseudonymsTable
                ->getRealNames($person)->toArray();
        }
        return $this->realNames[$person];
    }

    /**
     * Reformat all the credit lines for a single role.
     *
     * @param array $creatorIds IDs of known creators
     * @param array $editions   Edition details
     * @param array $details    Credits for a single role, keyed by person
     *
     * @return array
     */
    protected function analyzeGroup($creatorIds, $editions, $details)
    {
        $final = [];
        $fixTitle = $this->view->plugin('fixtitle');
        foreach ($details as $person => $credits) {
            $notes = [];
            foreach ($credits as $current) {
                // If credit count doesn't match edition count, then different
                // editions have different attributions.
                if (count($credits) != count($editions)) {
                    foreach ($credits as $credit) {
                        $note = $fixTitle($credit['Edition_Name']);
                        if (!empty($credit['Note'])) {
                            $note .= ' - ' . $credit['Note'];
                        }
                        $notes[] = $note;
                    }
                } else {
                    foreach ($credits as $credit) {
                        if (!empty($credit['Note'])) {
                            $notes[] = $credit['Note'];
                        }
                    }
                }
            }
            $final[$person] = [
                'person' => $credit,
                'realPerson' => $this->getRealPersonDetails($person),
                'notes' => implode(', ', array_unique($notes))
            ];
        }
        return $final;
    }

    /**
     * Extract IDs from creator list.
     *
     * @param array $creators Known creators to analyze
     *
     * @return array
     */
    protected function extractCreatorIds($creators)
    {
        $ids = [];
        foreach ($creators as $current) {
            $ids[] = $current['Person_ID'];
        }
        return $ids;
    }

    /**
     * Analyze and reformat a list of credits
     *
     * @param array $creators  Known creators to analyze
     * @param array $credits   Credits to analyze
     * @param array $editions  Information on editions containing credits
     *
     * @return array
     */
    public function __invoke($creators, $credits, $editions)
    {
        $final = [];
        $creatorIds = $this->extractCreatorIds($creators);
        foreach ($this->groupGredits($creatorIds, $credits) as $role => $details) {
            $final[$role] = $this
                ->analyzeGroup($creatorIds, $editions, $details);
        }
        return $final;
    }
}