<?php

/**
 * Mink integration test for the platform.
 *
 * PHP version 8
 *
 * Copyright (C) Demian Katz 2025.
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
 * along with this program; if not, see
 * <https://www.gnu.org/licenses/>.
 *
 * @category GeebyDeeby
 * @package  Tests
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 *
 * @GeebyDeeby.SkipBadStringCheck
 */

namespace GeebyDeebyTest\Mink;

use GeebyDeebyTest\Integration\MinkTestCase;
use Generator;

/**
 * Mink integration test for the platform.
 *
 * @category GeebyDeeby
 * @package  Tests
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class IntegrationTest extends MinkTestCase
{
    /**
     * Data provider for testEmptyDatabase()
     *
     * @return Generator<string, array>
     */
    public static function emptyDatabaseProvider(): Generator
    {
        yield 'series by name' => ['by name', 'No series listed in this database yet.', 0];
        yield 'series by category' => ['by category', 'No categories listed in this database yet.', 0];
        yield 'series by city' => ['by city', 'No cities listed in this database yet.', 0];
        yield 'series by country' => ['by country', 'No countries listed in this database yet.', 0];
        yield 'series by language' => ['by language', 'No languages listed in this database yet.', 0];
        yield 'series by material type' => ['by material type', 'No material types listed in this database yet.', 0];
        yield 'series by publisher' => ['by publisher', 'No publishers listed in this database yet.', 0];
        yield 'series with comments' => ['with comments', 'No comments listed.', 0];
        yield 'recently added series' => ['recently added', 'No series listed in this database yet.', 0];
        yield 'people by name' => ['by name', 'No people listed in this database yet.', 1];
        yield 'people with biographical notes' => [
            'with biographical notes',
            'No biographies listed in this database yet.',
            1,
        ];
        yield 'recently added people' => ['recently added', 'No people listed in this database yet.', 1];
        yield 'items by name' => ['by name', 'No items listed in this database yet.', 2];
        yield 'items by platform' => ['by platform', 'No platforms listed in this database yet.', 2];
        yield 'items by subject/tag' => ['by subject/tag', 'No subjects/tags listed in this database yet.', 2];
        yield 'items by year' => ['by year', 'No items listed in this database yet.', 2];
        yield 'items with full text' => ['with full text', 'No full text listed.', 2];
        yield 'items with reviews' => ['with reviews', 'No reviews listed.', 2];
        yield 'recently added items' => ['recently added', 'No items listed in this database yet.', 2];
        yield 'file list' => ['List Files', 'No files listed in this database yet.'];
        yield 'link list' => ['List Links', 'No links listed in this database yet.'];
        yield 'user list' => ['List Registered Users', 'No users listed in this database yet.'];
        yield 'recent reviews' => ['Browse Recent Reviews', 'No reviews available. No comments available.'];
        yield 'FAQs' => ['List All', 'No FAQs listed in this database yet.'];
    }

    /**
     * Test the behavior of an empty database.
     *
     * @param string $linkText        Text of link to click
     * @param string $expectedMessage Expected message on resulting page
     * @param ?int   $containerIndex  Index of paragraph containing link (null to search whole page)
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('emptyDatabaseProvider')]
    public function testEmptyDatabase(string $linkText, string $expectedMessage, ?int $containerIndex = null): void
    {
        $session = $this->getMinkSession();
        $session->visit($this->getGeebyDeebyUrl());
        $page = $session->getPage();
        $target = $containerIndex === null ? $page : $this->findCss($page, 'p', index: $containerIndex);
        $target->clickLink($linkText);
        $this->assertStringEndsWith($expectedMessage, $this->findCssAndGetText($page, '.content'));
    }
}
