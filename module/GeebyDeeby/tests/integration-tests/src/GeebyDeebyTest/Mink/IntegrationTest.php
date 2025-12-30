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

use Behat\Mink\Element\TraversableElement;
use GeebyDeebyTest\Integration\MinkTestCase;
use Generator;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
     * Service locator
     *
     * @var ?ServiceLocatorInterface
     */
    protected $serviceLocator = null;

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

    /**
     * Log in as a user.
     *
     * @param TraversableElement $page     Page element
     * @param string             $username Username
     * @param string             $password Password
     *
     * @return void
     */
    protected function logIn(TraversableElement $page, string $username = 'admin', string $password = 'password'): void
    {
        $page->clickLink('Log In');
        $this->findCssAndSetValue($page, '#username', $username);
        $this->findCssAndSetValue($page, '#password', $password);
        $this->clickCss($page, '.content input[type="submit"]');
    }

    /**
     * Get the service locator.
     *
     * @return ServiceLocatorInterface
     */
    protected function getServiceLocator(): ServiceLocatorInterface
    {
        if (null === $this->serviceLocator) {
            $app = \Laminas\Mvc\Application::init(require 'config/application.config.php');
            $this->serviceLocator = $app->getServiceManager();
        }
        return $this->serviceLocator;
    }

    /**
     * Approve a user.
     *
     * @param int  $userId   User to approve
     * @param ?int $groupId  Group to apply (null for no group)
     * @param int  $personId Person ID to link (-1 for none)
     *
     * @return void
     */
    protected function approveUser(int $userId, ?int $groupId = null, int $personId = -1): void
    {
        $userTable = $this->getServiceLocator()->get(\GeebyDeeby\Db\Table\PluginManager::class)->get('user');
        $changes = ['Person_ID' => $personId];
        if ($groupId) {
            $changes['User_Group_ID'] = $groupId;
        }
        $userTable->update($changes, ['User_ID' => $userId]);
    }

    /**
     * Test creation of a user.
     *
     * @return void
     */
    public function testCreateUser(): void
    {
        $session = $this->getMinkSession();
        $session->visit($this->getGeebyDeebyUrl());
        $page = $session->getPage();
        $page->clickLink('Sign Up');
        $this->findCssAndSetValue($page, '#signup_username', 'admin');
        $this->findCssAndSetValue($page, '#signup_fullname', 'test admin user');
        $this->findCssAndSetValue($page, '#signup_email', 'admin@example.com');
        $this->findCssAndSetValue($page, '#signup_password1', 'password');
        $this->findCssAndSetValue($page, '#signup_password2', 'password');
        $this->clickCss($page, '.signup input[type="submit"]');
        $this->assertEquals(
            'Your request for an account has been sent; it should be approved shortly.',
            $this->findCssAndGetText($page, '.content')
        );

        // Try to log in and confirm that the user exists but is not approved yet:
        $this->logIn($page);
        $this->assertEquals('Your account has not been approved yet.', $this->findCssAndGetText($page, '.error'));

        // Now make the user an admin to support future tests:
        $this->approveUser(1, 1);

        // Now go to the edit page:
        $session->visit($this->getGeebyDeebyUrl('/edit'));
        $this->logIn($page);
        $this->assertEquals('Administration', $this->findCssAndGetText($page, 'h1'));
    }

    /**
     * Data provider for testPopulateData().
     *
     * @return Generator<string, array>
     */
    public static function populateDataProvider(): Generator
    {
        yield 'category' => [
            'CategoryList',
            '#add_category',
            ['#Category_Name' => 'test category', '#Description' => 'test description'],
            '#category_list',
        ];
        yield 'citation' => [
            'CitationList',
            '#add_citation',
            ['#Citation_Text' => 'test citation'],
            '#citation_list',
        ];
        yield 'edition attribute' => [
            'EditionsAttributeList',
            '#add_edition_attribute',
            ['#Editions_Attribute_Name' => 'test edition attribute'],
            '#editions_attribute_list',
        ];
        yield 'file type' => [
            'FileList',
            '#add_file_type',
            ['#File_Type' => 'test file type'],
            '#file_type_list',
        ];
        yield 'file' => [
            'FileList',
            '#add_file',
            ['#File_Name' => 'test file', '#File_Path' => '/foo/bar'],
            '#file_list',
        ];
        yield 'full text attribute' => [
            'EditionFullTextAttributeList',
            '#add_edition_full_text_attribute',
            ['#Editions_Full_Text_Attribute_Name' => 'test full text attribute'],
            '#edition_full_text_attribute_list',
        ];
        yield 'full text source' => [
            'FullTextSourceList',
            '#add_source',
            ['#Full_Text_Source_Name' => 'test full text source'],
            '#full_text_source_list',
        ];
        yield 'item attribute' => [
            'ItemsAttributeList',
            '#add_item_attribute',
            ['#Items_Attribute_Name' => 'test item attribute'],
            '#items_attribute_list',
        ];
        yield 'item relationships' => [
            'ItemsRelationshipList',
            '#add_item_relationship',
            ['#Items_Relationship_Name' => 'test item relationship'],
            '#items_relationship_list',
        ];
        yield 'language' => [
            'LanguageList',
            '#add_language',
            ['#Language_Name' => 'test language'],
            '#language_list',
        ];
        yield 'link type' => [
            'LinkList',
            '#add_link_type',
            ['#Link_Type' => 'test link type'],
            '#link_type_list',
        ];
        yield 'link' => [
            'LinkList',
            '#add_link',
            ['#Link_Name' => 'test link', '#URL' => 'https://gamebooks.org/'],
            '#link_list',
        ];
        yield 'material type' => [
            'MaterialTypeList',
            '#add_material_type',
            ['#Material_Type_Name' => 'test material', '#Material_Type_Plural_Name' => 'test materials'],
            '#material_type_list',
        ];
        yield 'note' => [
            'NoteList',
            '#add_note',
            ['#Note_Text' => 'test note'],
            '#note_list',
        ];
        yield 'person role' => [
            'PersonList',
            '#add_person_role',
            ['#Role_Name' => 'test person role'],
            '#person_role_list',
        ];
        yield 'person authority' => [
            'PersonList',
            '#add_person_authority',
            ['#Authority_Name' => 'test person authority'],
            '#person_authority_list',
        ];
        yield 'person' => [
            'PersonList',
            '#add_person',
            ['#First_Name' => 'test-first', '#Last_Name' => 'test-last', '#Extra_Details' => ', extra'],
            '#person_list',
            'test-last, test-first, extra',
        ];
        yield 'country' => [
            'CountryList',
            '#add_country',
            ['#Country_Name' => 'test country'],
            '#country_list',
        ];
        yield 'city' => [
            'CountryList',
            '#add_city',
            ['#City_Name' => 'test city'],
            '#city_list',
        ];
        yield 'platform' => [
            'PlatformList',
            '#add_platform',
            ['#Platform_Name' => 'test platform'],
            '#platform_list',
        ];
        yield 'predicate' => [
            'PredicateList',
            '#add_predicate',
            ['#Predicate_Abbrev' => 'test_predicate', '#Predicate_URI' => 'http://test-predicate'],
            '#predicate_list',
            'test_predicate (http://test-predicate)',
        ];
        yield 'publisher' => [
            'PublisherList',
            '#add_publisher',
            ['#Publisher_Name' => 'test publisher'],
            '#publisher_list',
        ];
        yield 'series attribute' => [
            'SeriesAttributeList',
            '#add_series_attribute',
            ['#Series_Attribute_Name' => 'test series attribute'],
            '#series_attribute_list',
        ];
        yield 'series relationships' => [
            'SeriesAttributeList',
            '#add_series_relationship',
            ['#Series_Relationship_Name' => 'test series relationship'],
            '#series_relationship_list',
        ];
        yield 'tag attribute' => [
            'TagsAttributeList',
            '#add_tag_attribute',
            ['#Tags_Attribute_Name' => 'test tag attribute'],
            '#tags_attribute_list',
        ];
        yield 'tag relationships' => [
            'TagsAttributeList',
            '#add_tag_relationship',
            ['#Tags_Relationship_Name' => 'test tag relationship'],
            '#tags_relationship_list',
        ];
        yield 'tag type' => [
            'TagList',
            '#add_tag_type',
            ['#Tag_Type' => 'test tag type'],
            '#tag_type_list',
        ];
        yield 'tag' => [
            'TagList',
            '#add_tag',
            ['#Tag_Name' => 'test tag'],
            '#tag_list',
        ];
        yield 'series' => [
            'SeriesList',
            '#add_series',
            ['#Series_Name' => 'test series'],
            '#series_list',
        ];
        yield 'item' => [
            'Series/1',
            '#add_item',
            ['#Item_Name' => 'test item'],
            '#item_list',
            '[edit item]',
            2,
        ];
    }

    /**
     * Populate some data in the database.
     *
     * @param string  $url               URL for edit screen (will be appended to /edit/)
     * @param string  $buttonSelector    Selector for button to open create modal
     * @param array   $data              Data to enter into the create form (indexed by selector)
     * @param string  $listSelector      Selector for container listing all values
     * @param ?string $expectedDisplay   Expected display value for created item (null defaults to first $data element)
     * @param int     $expectedLinkCount Expected number of links in container after item creation
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testCreateUser')]
    #[\PHPUnit\Framework\Attributes\DataProvider('populateDataProvider')]
    public function testPopulateData(
        string $url,
        string $buttonSelector,
        array $data,
        string $listSelector,
        ?string $expectedDisplay = null,
        int $expectedLinkCount = 1
    ): void {
        $session = $this->getMinkSession();
        $session->visit($this->getGeebyDeebyUrl("/edit/$url"));
        $page = $session->getPage();
        $this->logIn($page);
        $this->clickCss($page, $buttonSelector);
        $firstValue = null;
        foreach ($data as $selector => $value) {
            $expectedDisplay = $expectedDisplay === null ? $value : $expectedDisplay;
            $this->findCssAndSetValue($page, $selector, $value);
        }
        $this->clickCss($page, '.modal-body input[type="submit"]');
        $this->waitForPageLoad($page);
        $this->assertEquals($expectedDisplay, $this->findCssAndGetText($page, "$listSelector a"));
        $this->assertCount($expectedLinkCount, $page->findAll('css', "$listSelector a"));
    }
}
