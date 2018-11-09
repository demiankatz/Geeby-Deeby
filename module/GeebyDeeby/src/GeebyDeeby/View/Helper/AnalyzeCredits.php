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
     * Group credits by role and person ID.
     *
     * @param array $credits Credits to analyze
     *
     * @return array
     */
    protected function groupGredits($credits)
    {
        $groupedCredits = array();
        foreach ($credits as $credit) {
            if (!isset($groupedCredits[$credit['Role_Name']])) {
                $groupedCredits[$credit['Role_Name']] = array();
            }
            if (!isset($groupedCredits[$credit['Role_Name']][$credit['Person_ID']])) {
                $groupedCredits[$credit['Role_Name']][$credit['Person_ID']] = array();
            }
            $groupedCredits[$credit['Role_Name']][$credit['Person_ID']][] = $credit;
        }
        return $groupedCredits;
    }

    /**
     * Figure out the real person behind a pseudonym.
     *
     * @param int   $person    Person ID.
     * @param array $realNames Real name data.
     *
     * @return array
     */
    protected function getRealPersonDetails($person, $realNames)
    {
        return isset($realNames[$person]) ? $realNames[$person] : [];
    }

    /**
     * Reformat all the credit lines for a single role.
     *
     * @param array $editions  Edition details
     * @param array $realNames Real name data.
     * @param array $details   Credits for a single role, keyed by person
     *
     * @return array
     */
    protected function analyzeGroup($editions, $realNames, $details)
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
                'realPerson' => $this->getRealPersonDetails($person, $realNames),
                'notes' => implode(', ', array_unique($notes))
            ];
        }
        return $final;
    }

    /**
     * Analyze and reformat a list of credits
     *
     * @param array $credits   Credits to analyze
     * @param array $editions  Information on editions containing credits
     * @param array $realNames Real name data.
     *
     * @return array
     */
    public function __invoke($credits, $editions, $realNames)
    {
        $final = [];
        foreach ($this->groupGredits($credits) as $role => $details) {
            $final[$role] = $this->analyzeGroup($editions, $realNames, $details);
        }
        return $final;
    }
}