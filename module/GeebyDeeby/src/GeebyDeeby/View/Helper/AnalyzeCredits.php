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
     * Items_Creators_Citations table.
     *
     * @var object
     */
    protected $citationsTable;

    /**
     * Pseudonyms table.
     *
     * @var object
     */
    protected $pseudonymsTable;

    /**
     * Pseudonym information.
     *
     * @var array
     */
    protected $pseudonyms = array();

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
     * @param object $citations  Items_Creators_Citations table.
     */
    public function __construct($pseudonyms, $citations)
    {
        $this->pseudonymsTable = $pseudonyms;
        $this->citationsTable = $citations;
    }

    /**
     * Group credits by role and person ID.
     *
     * @param array $creators Known creators to analyze
     * @param array $credits  Credits to analyze
     *
     * @return array
     */
    protected function groupCredits($creators, $credits)
    {
        $groupedCredits = array();

        // First group and associate the credits:
        foreach ($credits as $credit) {
            $personId = $credit['Person_ID'];
            $prefix = $this->isMatchingPerson($personId, array_keys($creators))
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

        // Now insert uncredited author credits for anything that wasn't matched:
        foreach ($creators as $creator) {
            $role = $creator['Role_Name'];
            $personId = $creator['Person_ID'];
            $creditedIds = array_keys(
                isset($groupedCredits[$role]) ? $groupedCredits[$role] : []
            );
            if (empty($creditedIds)
                || !$this->isMatchingPerson($personId, $creditedIds)
            ) {
                $groupedCredits[$role][$personId][]
                    = $creator->getArrayCopy() + ['Note' => 'uncredited'];
            }
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
        // If no creator IDs have been specified, we just assume that all names
        // are correct. We mainly use these for disambiguation.
        if (empty($creatorIds) || in_array($personId, $creatorIds)) {
            return true;
        }
        foreach ($this->getRealPersonDetails($personId) as $real) {
            if (in_array($real['Person_ID'], $creatorIds)) {
                return true;
            }
        }
        foreach ($this->getPseudonymDetails($personId) as $pseudo) {
            if (in_array($pseudo['Person_ID'], $creatorIds)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Filter $names to only include values matching $filter (or all names, if
     * $filter is empty).
     *
     * @param array $names  Names to filter
     * @param array $filter Person IDs to keep (or empty to keep all).
     */
    protected function filterNames($names, $filter)
    {
        if (empty($filter)) {
            return $names;
        }
        $filtered = array();
        foreach ($names as $name) {
            if (in_array($name['Real_Person_ID'], $filter)) {
                $filtered[] = $name;
            }
        }
        return $filtered;
    }

    /**
     * Figure out the pseudonyms for a real person.
     *
     * @param int   $person Person ID.
     * @param array $filter Array of person IDs to filter on.
     *
     * @return array
     */
    protected function getPseudonymDetails($person, $filter = array())
    {
        if (!isset($this->pseudonyms[$person])) {
            $this->pseudonyms[$person] = $this->pseudonymsTable
                ->getPseudonyms($person)->toArray();
        }
        return $this->filterNames($this->pseudonyms[$person], $filter);
    }

    /**
     * Figure out the real person behind a pseudonym.
     *
     * @param int   $person Person ID.
     * @param array $filter Array of person IDs to filter on.
     *
     * @return array
     */
    protected function getRealPersonDetails($person, $filter = array())
    {
        if (!isset($this->realNames[$person])) {
            $this->realNames[$person] = $this->pseudonymsTable
                ->getRealNames($person)->toArray();
        }
        return $this->filterNames($this->realNames[$person], $filter);
    }

    /**
     * Reformat all the credit lines for a single role.
     *
     * @param array $creators Known creators
     * @param array $editions Edition details
     * @param array $details  Credits for a single role, keyed by person
     *
     * @return array
     */
    protected function analyzeGroup($creators, $editions, $details)
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
                            if (!empty($note)) {
                                $note .= ' - ';
                            }
                            $note .= $credit['Note'];
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
                'realPerson' => $this
                    ->getRealPersonDetails($person, array_keys($creators)),
                'notes' => implode(', ', array_unique($notes))
            ];
        }
        return $final;
    }

    /**
     * Given an item creator, turn the citation IDs into a lookup key.
     *
     * @param int $id Item_Creator_ID value
     *
     * @return string
     */
    protected function getCitationGroup($id)
    {
        $citations = array();
        foreach ($this->citationsTable->getCitations($id) as $citation) {
            $citations[] = $citation['Citation'];
        }
        if (empty($citations)) {
            $citations[] = 'an uncited source';
        }
        return implode(' and ', $citations);
    }

    /**
     * Group creators by citation. Return an array of arrays keyed by Person_ID
     *
     * @param array $creators Creators to group
     * @param int   $itemId   Item ID creators belong to
     *
     * @return array
     */
    protected function groupCreators($creators, $itemId)
    {
        $groups = [];
        foreach ($creators as $creator) {
            // Determine group key by looking up citation IDs
            $key = $this->getCitationGroup($creator['Item_Creator_ID']);
            $groups[$key][$creator['Person_ID']] = $creator;
        }
        return $groups;
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
        $currentEdition = current($editions);
        $itemId = isset($currentEdition['Item_ID'])
            ? $currentEdition['Item_ID'] : null;
        $groupedCreators = $this->groupCreators($creators, $itemId);
        if (empty($groupedCreators)) {
            $groupedCreators = ['an uncited source' => []];
        }
        foreach ($groupedCreators as $groupLabel => $currentCreators) {
            $groups = $this->groupCredits($currentCreators, $credits);
            foreach ($groups as $role => $details) {
                $data = $this->analyzeGroup($currentCreators, $editions, $details);
                $citation = count($groupedCreators) > 1 ? $groupLabel : null;
                $final[] = compact('role', 'data', 'citation');
            }
        }
        return $final;
    }
}