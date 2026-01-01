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
 */

namespace GeebyDeebyTest\Mink;

use Behat\Mink\Element\TraversableElement;
use GeebyDeebyTest\Integration\MinkTestCase;
use Generator;
use Laminas\ServiceManager\ServiceLocatorInterface;

use function in_array;

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
     * Do a basic page content comparison.
     *
     * @param string $expectedMessage Expected message on resulting page
     * @param string $path            URL path to visit initially.
     * @param string $linkText        Text of link to click on resulting page (null to skip clicking)
     * @param ?int   $containerIndex  Index of paragraph containing link (null to search whole page)
     * @param bool   $regExMatch      Should we do a string match (false), or a regex match (true)?
     *
     * @return void
     */
    protected function assertPageContent(
        string $expectedMessage,
        string $path = '',
        ?string $linkText = null,
        ?int $containerIndex = null,
        bool $regExMatch = false
    ): void {
        $page = $this->goToPage($path);
        if ($linkText) {
            $target = $containerIndex === null ? $page : $this->findCss($page, 'p', index: $containerIndex);
            $target->clickLink($linkText);
        }
        $assertion = $regExMatch ? 'assertMatchesRegularExpression' : 'assertEquals';
        $this->$assertion($expectedMessage, $this->findCssAndGetText($page, '.content'));
    }

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
        yield 'items with full text' => ['with full text', '/.*No full text listed.$/', 2, true];
        yield 'items with reviews' => ['with reviews', 'No reviews listed.', 2];
        yield 'recently added items' => ['recently added', 'No items listed in this database yet.', 2];
        yield 'file list' => ['List Files', 'No files listed in this database yet.'];
        yield 'link list' => ['List Links', 'No links listed in this database yet.'];
        yield 'user list' => ['List Registered Users', 'No users listed in this database yet.'];
        yield 'recent reviews' => [
            'Browse Recent Reviews',
            '/.*No reviews available. No comments available.$/',
            null,
            true,
        ];
        yield 'FAQs' => ['List All', 'No FAQs listed in this database yet.'];
    }

    /**
     * Test the behavior of an empty database.
     *
     * @param string $linkText        Text of link to click
     * @param string $expectedMessage Expected message on resulting page
     * @param ?int   $containerIndex  Index of paragraph containing link (null to search whole page)
     * @param bool   $regExMatch      Should we do a string match (false), or a regex match (true)?
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('emptyDatabaseProvider')]
    public function testEmptyDatabase(
        string $linkText,
        string $expectedMessage,
        ?int $containerIndex = null,
        bool $regExMatch = false
    ): void {
        $this->assertPageContent($expectedMessage, '', $linkText, $containerIndex, $regExMatch);
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
    protected function logIn(TraversableElement $page, string $username, string $password = 'password'): void
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
     * Create a user.
     *
     * @param TraversableElement $page     Page element
     * @param string             $username Username to create
     * @param string             $password New account's password
     *
     * @return void
     */
    protected function createUser(TraversableElement $page, string $username, string $password = 'password'): void
    {
        $page->clickLink('Sign Up');
        $this->findCssAndSetValue($page, '#signup_username', $username);
        $this->findCssAndSetValue($page, '#signup_fullname', 'test ' . $username);
        $this->findCssAndSetValue($page, '#signup_email', $username . '@example.com');
        $this->findCssAndSetValue($page, '#signup_password1', $password);
        $this->findCssAndSetValue($page, '#signup_password2', $password);
        $this->clickCss($page, '.signup input[type="submit"]');
        $this->assertEquals(
            'Your request for an account has been sent; it should be approved shortly.',
            $this->findCssAndGetText($page, '.content')
        );
    }

    /**
     * Approve a user.
     *
     * @param string $username User to approve
     * @param ?int   $groupId  Group to apply (null for no group)
     * @param int    $personId Person ID to link (-1 for none)
     *
     * @return void
     */
    protected function approveUser(string $username, ?int $groupId = null, int $personId = -1): void
    {
        $userTable = $this->getServiceLocator()->get(\GeebyDeeby\Db\Table\PluginManager::class)->get('user');
        $changes = ['Person_ID' => $personId];
        if ($groupId) {
            $changes['User_Group_ID'] = $groupId;
        }
        $userTable->update($changes, ['Username' => $username]);
    }

    /**
     * Data provider for testCreateUser().
     *
     * @return Generator<string, array>
     */
    public static function createUserProvider(): Generator
    {
        yield 'admin' => ['admin'];
        yield 'user' => ['user'];
    }

    /**
     * Test creation of a user.
     *
     * @param string $username Username to create
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('createUserProvider')]
    public function testCreateUser(string $username): void
    {
        $this->createUser($this->goToPage(), $username);
    }

    /**
     * Data provider for testUserApproval().
     *
     * @return Generator<string, array>
     */
    public static function userApprovalProvider(): Generator
    {
        yield 'admin' => ['admin', 1];
        yield 'user' => ['user'];
    }

    /**
     * Test approving users.
     *
     * @param string $username User to approve
     * @param ?int   $group    Group to assign user to (null for none)
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testCreateUser')]
    #[\PHPUnit\Framework\Attributes\DataProvider('userApprovalProvider')]
    public function testUserApproval(string $username, ?int $group = null): void
    {
        $page = $this->goToPage();

        // Try to log in and confirm that the user exists but is not approved yet:
        $this->logIn($page, $username);
        $this->assertEquals('Your account has not been approved yet.', $this->findCssAndGetText($page, '.error'));

        // Now assign appropriate group permissions to the user:
        $this->approveUser($username, $group);

        // Now go to the edit page and assert appropriate behavior (admin available if in group 1, not otherwise):
        $nextPage = $this->goToPage('/edit');
        $this->logIn($nextPage, $username);
        $this->assertEquals('Administration', $this->findCssAndGetText($nextPage, 'h1'));
        if ($group === 1) {
            $this->assertEquals('Edit Data', $this->findCssAndGetText($nextPage, '.content h2'));
        } else {
            $this->assertNotEquals('Edit Data', $this->findCssAndGetText($nextPage, '.content h2'));
            $this->assertEquals(
                'You do not have permission to access this page.',
                $this->findCssAndGetText($nextPage, '.content p')
            );
        }
    }

    /**
     * Data provider for testPopulateData().
     *
     * @return Generator<string, array>
     */
    public static function populateDataProvider(): Generator
    {
        yield 'category 1' => [
            'CategoryList',
            '#add_category',
            ['#Category_Name' => 'test category', '#Description' => 'test description'],
            '#category_list',
        ];
        yield 'category 2' => [
            'CategoryList',
            '#add_category',
            ['#Category_Name' => 'second test category', '#Description' => 'test description 2'],
            '#category_list',
            null,
            2,
        ];
        yield 'citation 1' => [
            'CitationList',
            '#add_citation',
            ['#Citation_Text' => 'test citation'],
            '#citation_list',
        ];
        yield 'citation 2' => [
            'CitationList',
            '#add_citation',
            ['#Citation_Text' => 'second test citation'],
            '#citation_list',
            null,
            2,
        ];
        yield 'edition attribute 1' => [
            'EditionsAttributeList',
            '#add_edition_attribute',
            ['#Editions_Attribute_Name' => 'test edition attribute 1'],
            '#editions_attribute_list',
        ];
        yield 'edition attribute 2' => [
            'EditionsAttributeList',
            '#add_edition_attribute',
            ['#Editions_Attribute_Name' => 'test edition attribute 2'],
            '#editions_attribute_list',
            null,
            2,
        ];
        yield 'file type 1' => [
            'FileList',
            '#add_file_type',
            ['#File_Type' => 'test file type 1'],
            '#file_type_list',
        ];
        yield 'file type 2' => [
            'FileList',
            '#add_file_type',
            ['#File_Type' => 'test file type 2'],
            '#file_type_list',
            null,
            2,
        ];
        yield 'file 1' => [
            'FileList',
            '#add_file',
            ['#File_Name' => 'test file 1', '#File_Path' => '/foo/bar/1'],
            '#file_list',
        ];
        yield 'file 2' => [
            'FileList',
            '#add_file',
            ['#File_Name' => 'test file 2', '#File_Path' => '/foo/bar/2'],
            '#file_list',
            null,
            2,
        ];
        yield 'full text attribute 1' => [
            'EditionFullTextAttributeList',
            '#add_edition_full_text_attribute',
            ['#Editions_Full_Text_Attribute_Name' => 'test full text attribute 1'],
            '#edition_full_text_attribute_list',
        ];
        yield 'full text attribute 2' => [
            'EditionFullTextAttributeList',
            '#add_edition_full_text_attribute',
            ['#Editions_Full_Text_Attribute_Name' => 'test full text attribute 2'],
            '#edition_full_text_attribute_list',
            null,
            2,
        ];
        yield 'full text source 1' => [
            'FullTextSourceList',
            '#add_source',
            ['#Full_Text_Source_Name' => 'test full text source 1'],
            '#full_text_source_list',
        ];
        yield 'full text source 2' => [
            'FullTextSourceList',
            '#add_source',
            ['#Full_Text_Source_Name' => 'test full text source 2'],
            '#full_text_source_list',
            null,
            2,
        ];
        yield 'item attribute 1' => [
            'ItemsAttributeList',
            '#add_item_attribute',
            ['#Items_Attribute_Name' => 'test item attribute 1'],
            '#items_attribute_list',
        ];
        yield 'item attribute 2' => [
            'ItemsAttributeList',
            '#add_item_attribute',
            ['#Items_Attribute_Name' => 'test item attribute 2'],
            '#items_attribute_list',
            null,
            2,
        ];
        yield 'item relationship 1' => [
            'ItemsRelationshipList',
            '#add_item_relationship',
            ['#Items_Relationship_Name' => 'test item relationship 1'],
            '#items_relationship_list',
        ];
        yield 'item relationship 2' => [
            'ItemsRelationshipList',
            '#add_item_relationship',
            ['#Items_Relationship_Name' => 'test item relationship 2'],
            '#items_relationship_list',
            null,
            2,
        ];
        yield 'language 1' => [
            'LanguageList',
            '#add_language',
            ['#Language_Name' => 'test language 1'],
            '#language_list',
        ];
        yield 'language 2' => [
            'LanguageList',
            '#add_language',
            ['#Language_Name' => 'test language 2'],
            '#language_list',
            null,
            2,
        ];
        yield 'link type 1' => [
            'LinkList',
            '#add_link_type',
            ['#Link_Type' => 'test link type 1'],
            '#link_type_list',
        ];
        yield 'link type 2' => [
            'LinkList',
            '#add_link_type',
            ['#Link_Type' => 'test link type 2'],
            '#link_type_list',
            null,
            2,
        ];
        yield 'link 1' => [
            'LinkList',
            '#add_link',
            ['#Link_Name' => 'test link 1', '#URL' => 'https://gamebooks.org/'],
            '#link_list',
        ];
        yield 'link 2' => [
            'LinkList',
            '#add_link',
            ['#Link_Name' => 'test link 2', '#URL' => 'https://dimenovels.org/raw/'],
            '#link_list',
            null,
            2,
        ];
        yield 'material type 1' => [
            'MaterialTypeList',
            '#add_material_type',
            ['#Material_Type_Name' => 'test material', '#Material_Type_Plural_Name' => 'test materials'],
            '#material_type_list',
        ];
        yield 'material type 2' => [
            'MaterialTypeList',
            '#add_material_type',
            ['#Material_Type_Name' => 'second test material', '#Material_Type_Plural_Name' => 'second test materials'],
            '#material_type_list',
            null,
            2,
        ];
        yield 'note 1' => [
            'NoteList',
            '#add_note',
            ['#Note_Text' => 'test note'],
            '#note_list',
        ];
        yield 'note 2' => [
            'NoteList',
            '#add_note',
            ['#Note_Text' => 'test note 2'],
            '#note_list',
            null,
            2,
        ];
        yield 'person role' => [
            'PersonList',
            '#add_person_role',
            ['#Role_Name' => 'test person role'],
            '#person_role_list',
        ];
        yield 'person role 2' => [
            'PersonList',
            '#add_person_role',
            ['#Role_Name' => 'test person role 2'],
            '#person_role_list',
            null,
            2,
        ];
        yield 'person authority' => [
            'PersonList',
            '#add_person_authority',
            ['#Authority_Name' => 'test person authority'],
            '#person_authority_list',
        ];
        yield 'person authority 2' => [
            'PersonList',
            '#add_person_authority',
            ['#Authority_Name' => 'test person authority 2'],
            '#person_authority_list',
            null,
            2,
        ];
        yield 'person 1' => [
            'PersonList',
            '#add_person',
            ['#First_Name' => 'test-first', '#Last_Name' => 'test-last', '#Extra_Details' => ', extra'],
            '#person_list',
            'test-last, test-first, extra',
        ];
        yield 'person 2' => [
            'PersonList',
            '#add_person',
            ['#First_Name' => 'test-second', '#Last_Name' => 'lastname'],
            '#person_list',
            'lastname, test-second',
            6, // number is higher due to jump links
        ];
        yield 'country 1' => [
            'CountryList',
            '#add_country',
            ['#Country_Name' => 'test country'],
            '#country_list',
        ];
        yield 'country 2' => [
            'CountryList',
            '#add_country',
            ['#Country_Name' => 'test country 2'],
            '#country_list',
            null,
            2,
        ];
        yield 'city 1' => [
            'CountryList',
            '#add_city',
            ['#City_Name' => 'test city'],
            '#city_list',
        ];
        yield 'city 2' => [
            'CountryList',
            '#add_city',
            ['#City_Name' => 'test city 2'],
            '#city_list',
            null,
            2,
        ];
        yield 'platform 1' => [
            'PlatformList',
            '#add_platform',
            ['#Platform_Name' => 'test platform'],
            '#platform_list',
        ];
        yield 'platform 2' => [
            'PlatformList',
            '#add_platform',
            ['#Platform_Name' => 'test platform 2'],
            '#platform_list',
            null,
            2,
        ];
        yield 'predicate 1' => [
            'PredicateList',
            '#add_predicate',
            ['#Predicate_Abbrev' => 'test_predicate', '#Predicate_URI' => 'http://test-predicate'],
            '#predicate_list',
            'test_predicate (http://test-predicate)',
        ];
        yield 'predicate 2' => [
            'PredicateList',
            '#add_predicate',
            ['#Predicate_Abbrev' => 'another_test_predicate', '#Predicate_URI' => 'http://test-predicate/2'],
            '#predicate_list',
            'another_test_predicate (http://test-predicate/2)',
            6, // number is higher due to jump links
        ];
        yield 'publisher 1' => [
            'PublisherList',
            '#add_publisher',
            ['#Publisher_Name' => 'test publisher'],
            '#publisher_list',
        ];
        yield 'publisher 2' => [
            'PublisherList',
            '#add_publisher',
            ['#Publisher_Name' => 'test publisher 2'],
            '#publisher_list',
            null,
            2,
        ];
        yield 'series attribute 1' => [
            'SeriesAttributeList',
            '#add_series_attribute',
            ['#Series_Attribute_Name' => 'test series attribute'],
            '#series_attribute_list',
        ];
        yield 'series attribute 2' => [
            'SeriesAttributeList',
            '#add_series_attribute',
            ['#Series_Attribute_Name' => 'test series attribute 2'],
            '#series_attribute_list',
            null,
            2,
        ];
        yield 'series relationship 1' => [
            'SeriesAttributeList',
            '#add_series_relationship',
            ['#Series_Relationship_Name' => 'test series relationship'],
            '#series_relationship_list',
        ];
        yield 'series relationship 2' => [
            'SeriesAttributeList',
            '#add_series_relationship',
            ['#Series_Relationship_Name' => 'test series relationship 2'],
            '#series_relationship_list',
            null,
            2,
        ];
        yield 'tag attribute 1' => [
            'TagsAttributeList',
            '#add_tag_attribute',
            ['#Tags_Attribute_Name' => 'test tag attribute'],
            '#tags_attribute_list',
        ];
        yield 'tag attribute 2' => [
            'TagsAttributeList',
            '#add_tag_attribute',
            ['#Tags_Attribute_Name' => 'test tag attribute 2'],
            '#tags_attribute_list',
            null,
            2,
        ];
        yield 'tag relationship 1' => [
            'TagsAttributeList',
            '#add_tag_relationship',
            ['#Tags_Relationship_Name' => 'test tag relationship'],
            '#tags_relationship_list',
        ];
        yield 'tag relationship 2' => [
            'TagsAttributeList',
            '#add_tag_relationship',
            ['#Tags_Relationship_Name' => 'test tag relationship 2'],
            '#tags_relationship_list',
            null,
            2,
        ];
        yield 'tag type 1' => [
            'TagList',
            '#add_tag_type',
            ['#Tag_Type' => 'test tag type'],
            '#tag_type_list',
        ];
        yield 'tag type 2' => [
            'TagList',
            '#add_tag_type',
            ['#Tag_Type' => 'test tag type 2'],
            '#tag_type_list',
            null,
            2,
        ];
        yield 'tag 1' => [
            'TagList',
            '#add_tag',
            ['#Tag_Name' => 'test tag'],
            '#tag_list',
        ];
        yield 'tag 2' => [
            'TagList',
            '#add_tag',
            ['#Tag_Name' => 'test tag 2'],
            '#tag_list',
            null,
            2,
        ];
        yield 'series 1' => [
            'SeriesList',
            '#add_series',
            ['#Series_Name' => 'test series 1'],
            '#series_list',
        ];
        yield 'series 2' => [
            'SeriesList',
            '#add_series',
            ['#Series_Name' => 'test series 2'],
            '#series_list',
            null,
            2,
        ];
        yield 'item' => [
            'Series/1',
            '#add_item',
            ['#Item_Name' => 'test item'],
            '#item_list',
            '[edit edition]',
            2,
        ];
    }

    /**
     * Populate a form and return the first value entered (or empty string if no data provided).
     *
     * @param TraversableElement $page Page containing form
     * @param array              $data Data to enter into the form (indexed by selector)
     *
     * @return string
     */
    protected function populateForm(TraversableElement $page, array $data): string
    {
        $firstValue = null;
        foreach ($data as $selector => $value) {
            $firstValue ??= $value;
            $this->findCssAndSetValue($page, $selector, $value);
        }
        return $firstValue ?? '';
    }

    /**
     * Assert that a list of links contains the expected value (and, if provided, matches the expected count)
     *
     * @param TraversableElement $page              Page containing list
     * @param string             $listSelector      Selector for container containing links
     * @param string             $expectedLink      Link text we expect to find in the list
     * @param ?int               $expectedLinkCount Expected count of links (or null to skip check)
     *
     * @return void
     * @throws \Exception
     */
    protected function assertLinkListIsCorrect(
        TraversableElement $page,
        string $listSelector,
        string $expectedLink,
        ?int $expectedLinkCount = null
    ): void {
        $links = $page->findAll('css', "$listSelector a");
        $linkText = array_map(fn ($a) => $a->getText(), $links);
        $this->assertTrue(in_array($expectedLink, $linkText), "Link list should include '$expectedLink'");
        if (null !== $expectedLinkCount) {
            $this->assertCount($expectedLinkCount, $links);
        }
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
    #[\PHPUnit\Framework\Attributes\Depends('testUserApproval')]
    #[\PHPUnit\Framework\Attributes\DataProvider('populateDataProvider')]
    public function testPopulateData(
        string $url,
        string $buttonSelector,
        array $data,
        string $listSelector,
        ?string $expectedDisplay = null,
        int $expectedLinkCount = 1
    ): void {
        $page = $this->goToPage("/edit/$url");
        $this->logIn($page, 'admin');
        $this->clickCss($page, $buttonSelector);
        $firstValue = $this->populateForm($page, $data);
        $this->clickCss($page, '.modal-body input[type="submit"]');
        $this->waitForPageLoad($page);
        $this->assertLinkListIsCorrect($page, $listSelector, $expectedDisplay ?? $firstValue, $expectedLinkCount);
    }

    /**
     * Data provider for testEditExistingData().
     *
     * @return Generator<string, array>
     */
    public static function editExistingDataProvider(): Generator
    {
        // We're going to derive some data from the populateDataProvider, so let's obtain that as an array:
        $populateData = iterator_to_array(static::populateDataProvider());

        /**
         * Function to derive an edit test case from a populate test case.
         *
         * @param array  $testCase       Original populate test case
         * @param array  $fieldOverrides Specific edits to make (instead of default append "(edited)")
         * @param ?array $fieldsToEdit   Array of fields to append "(edit)" to
         * @param bool   $inModal        Do we expect the edit screen to be in a modal?
         *
         * @return array
         */
        $deriveTestCase = function (
            array $testCase,
            ?array $fieldsToEdit = null,
            array $fieldOverrides = [],
            ?string $expectedDisplay = null,
            bool $inModal = true
        ): array {
            // Extract the test case to convenience variables:
            [$url, , $data, $listSelector] = $testCase;
            $linkToClick = $testCase[4] ?? reset($data);
            foreach ($fieldsToEdit ?? array_merge(array_keys($fieldOverrides), array_keys($data)) as $key) {
                $data[$key] = $fieldOverrides[$key] ?? ($data[$key] . ' (edited)');
            }
            $expectedDisplay ??= $linkToClick . ' (edited)';
            return [$url, $linkToClick, $data, $listSelector, $expectedDisplay, $inModal];
        };

        yield 'category' => $deriveTestCase($populateData['category 2']);
        yield 'citation' => $deriveTestCase($populateData['citation 2']);
        yield 'edition attribute' => $deriveTestCase($populateData['edition attribute 2']);
        yield 'file type' => $deriveTestCase($populateData['file type 2']);
        yield 'file' => $deriveTestCase(
            $populateData['file 2'],
            fieldOverrides: [
                '#File_Path' => '/foo/2_edited',
                '#Description' => 'This has been edited.',
            ],
            inModal: false
        );
        yield 'full text attribute' => $deriveTestCase($populateData['full text attribute 2']);
        yield 'full text source' => $deriveTestCase($populateData['full text source 2']);
        yield 'item attribute' => $deriveTestCase($populateData['item attribute 2']);
        yield 'item relationship' => $deriveTestCase($populateData['item relationship 2']);
        yield 'language' => $deriveTestCase($populateData['language 2']);
        yield 'link type' => $deriveTestCase($populateData['link type 2']);
        yield 'link' => $deriveTestCase(
            $populateData['link 2'],
            fieldOverrides: [
                '#URL' => 'https://dimenovels.org',
                '#Description' => 'This has been edited.',
                '#Date_Checked' => '2025-12-01',
            ],
            inModal: false
        );
        yield 'material type' => $deriveTestCase($populateData['material type 2']);
        yield 'note' => $deriveTestCase($populateData['note 2']);
        yield 'person role' => $deriveTestCase($populateData['person role 2']);
        yield 'person authority' => $deriveTestCase($populateData['person authority 2']);
        yield 'person' => $deriveTestCase(
            $populateData['person 2'],
            fieldOverrides: ['#First_Name' => 'test-second-edited', '#Last_Name' => 'last', '#Biography' => 'bio'],
            inModal: false,
            expectedDisplay: 'last, test-second-edited'
        );
        yield 'country' => $deriveTestCase($populateData['country 2'], inModal: false);
        yield 'city' => $deriveTestCase($populateData['city 2'], inModal: false);
        yield 'platform' => $deriveTestCase($populateData['platform 2']);
        yield 'predicate' => $deriveTestCase(
            $populateData['predicate 2'],
            fieldOverrides: ['#Predicate_Abbrev' => 'edited_test_predicate', '#Predicate_URI' => 'http://edit-pred/'],
            expectedDisplay: 'edited_test_predicate (http://edit-pred/)',
        );
        yield 'publisher' => $deriveTestCase($populateData['publisher 2'], inModal: false);
        yield 'series attribute' => $deriveTestCase($populateData['series attribute 2']);
        yield 'series relationship' => $deriveTestCase($populateData['series relationship 2']);
        yield 'tag attribute' => $deriveTestCase($populateData['tag attribute 2']);
        yield 'tag relationship' => $deriveTestCase($populateData['tag relationship 2']);
        yield 'tag type' => $deriveTestCase($populateData['tag type 2']);
        yield 'tag' => $deriveTestCase($populateData['tag 2'], inModal: false);
        yield 'series' => $deriveTestCase(
            $populateData['series 2'],
            fieldOverrides: [
                '#Series_Description' => 'This has been edited.',
            ],
            inModal: false
        );
    }

    /**
     * Edit existing data in the database.
     *
     * @param string $url             URL for edit screen (will be appended to /edit/)
     * @param string $linkToClick     Text of link to click to open edit form
     * @param array  $data            Data to enter into the edit form (indexed by selector)
     * @param string $listSelector    Selector for container listing all values
     * @param string $expectedDisplay Expected display value for edited item
     * @param bool   $inModal         Do we expect the edit screen to be in a modal?
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testPopulateData')]
    #[\PHPUnit\Framework\Attributes\DataProvider('editExistingDataProvider')]
    public function testEditExistingData(
        string $url,
        string $linkToClick,
        array $data,
        string $listSelector,
        string $expectedDisplay,
        bool $inModal
    ): void {
        $page = $this->goToPage("/edit/$url");
        $this->logIn($page, 'admin');
        $page->clickLink($linkToClick);
        $this->populateForm($page, $data);
        $baseSelector = $inModal ? '.modal-body' : '.edit_container';
        $this->clickCss($page, $baseSelector . ' input[type="submit"]');
        $this->waitForPageLoad($page);
        // If we're not in a modal, we need to return to the previous page to check our results:
        if (!$inModal) {
            $this->getMinkSession()->visit($this->getGeebyDeebyUrl("/edit/$url"));
        }
        $this->assertLinkListIsCorrect($page, $listSelector, $expectedDisplay);
    }

    /**
     * Data provider for testEmptyLinkLists().
     *
     * @return Generator<string, array>
     */
    public static function emptyLinkListsProvider(): Generator
    {
        yield 'series' => [
            '/Series/1',
            '/.*No items listed in this series yet. User Comments No comments available.$/',
            true,
        ];
        yield 'category' => ['/Category/1', 'test description No series are listed in this category.'];
        yield 'city' => ['/City/1', 'No information is available about this city.'];
        yield 'country' => ['/Country/1', 'No information is available about this country.'];
        yield 'language' => ['/Language/2', 'No information is available about this language.'];
        yield 'material type' => ['/Material/1', 'No information is available about this material type.'];
        yield 'publisher' => ['/Publisher/1', 'No information is available about this publisher.'];
        yield 'person' => ['/Person/1', 'No further information on this person is available at the moment.'];
        yield 'person with biography' => ['/Person/2', 'bio'];
        yield 'item 404' => ['/Item/2', 'The item you requested does not exist.'];
        yield 'platform' => ['/Platform/1', 'No information is available about this platform.'];
        yield 'tag' => ['/Tag/1', 'No information is available about this subject/tag.'];
    }

    /**
     * Test that appropriate empty messages are provided before content is linked up.
     *
     * @param string $path            URL path to check
     * @param string $expectedMessage Content expected on page
     * @param bool   $regExMatch      Should we do a string match (false), or a regex match (true)?
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testPopulateData')]
    #[\PHPUnit\Framework\Attributes\DataProvider('emptyLinkListsProvider')]
    protected function testEmptyLinkLists(
        string $path,
        string $expectedMessage,
        bool $regExMatch = false
    ): void {
        $this->assertPageContent($expectedMessage, $path, regExMatch: $regExMatch);
    }

    /**
     * Data provider for testLinkCreation().
     *
     * @return Generator<string, array>
     */
    public static function linkCreationProvider(): Generator
    {
        yield 'edition credit' => [
            '/edit/Edition/1',
            null,
            ['#credit_person' => '1', '#credit_note' => '1'],
            '#credit_list',
            '/^No credits.$/',
            '/test person role: test-last, test-first, extra \\(test note\\)/',
        ];
        yield 'edition ISBN' => [
            '/edit/Edition/1',
            'Codes/ISBNs',
            ['#isbn' => '0123456789', '#isbn_note' => '1'],
            '#isbn_list',
            '/^No ISBNs set.$/',
            '|0123456789 / 9780123456786 \\(test note\\)|',
            '#add_isbn',
        ];
        yield 'edition OCLC number' => [
            '/edit/Edition/1',
            'Codes/ISBNs',
            ['#oclc_number' => '12345', '#oclc_number_note' => '1'],
            '#oclcnumber_list',
            '/^No OCLC numbers set.$/',
            '/12345 \\(test note\\)/',
            '#add_oclc_number',
        ];
        yield 'edition product code' => [
            '/edit/Edition/1',
            'Codes/ISBNs',
            ['#product_code' => 'pc-test', '#product_code_note' => '1'],
            '#productcode_list',
            '/^No product codes set.$/',
            '/pc-test \\(test note\\)/',
            '#add_product_code',
        ];
        yield 'edition date' => [
            '/edit/Edition/1',
            'Dates',
            ['#releaseMonth' => '2', '#releaseDay' => '3', '#releaseYear' => '1952', '#releaseNote' => '1'],
            '#date_list',
            '/^No dates set.$/',
            '/February 3, 1952 \\(test note\\)/',
        ];
        yield 'edition full text link' => [
            '/edit/Edition/1',
            'Full Text Links',
            ['#Full_Text_URL' => 'http://example.com/fulltext'],
            '#fulltext_list',
            '/^No full text set.$/',
            '|test full text source 1: http://example.com/fulltext '
            . 'Edit options for URL: http://example.com/fulltext '
            . 'Delete full text URL: http://example.com/fulltext|',
        ];
        yield 'edition image' => [
            '/edit/Edition/1',
            'Images',
            ['#iiif_uri' => 'http://example.com/iiif', '#image_note' => '1'],
            '#image_list',
            '/^No images.$/',
            '|IIIF URI: http://example.com/iiif Note: test note|',
        ];
        yield 'edition platform' => [
            '/edit/Edition/1',
            'Platforms',
            [], // use default
            '#platform_list',
            '/^No platforms set.$/',
            '/test platform/',
        ];
        // TODO: add separate tests for contents and preferred publisher/titles.
    }

    /**
     * Test linking additional data to records.
     *
     * @param string  $url                  Path relative to Geeby-Deeby base URL for entering data
     * @param ?string $tabToClick           Tab to click before populating data (null to use default tab)
     * @param array   $valuesToSet          Array of selector => value (data to link)
     * @param string  $containerSelector    Selector for container listing links
     * @param ?string $beforeContainerRegex Regular expression to check in container before linking data (null to skip)
     * @param ?string $afterContainerRegex  Regular expression to check in container after linking data (null to skip)
     * @param string  $submitSelector       Selector for submit button
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testEditExistingData')]
    #[\PHPUnit\Framework\Attributes\DataProvider('linkCreationProvider')]
    public function testLinkCreation(
        string $url,
        ?string $tabToClick,
        array $valuesToSet,
        string $containerSelector,
        ?string $beforeContainerRegex = null,
        ?string $afterContainerRegex = null,
        string $submitSelector = '.active .edit_container input[type="submit"]'
    ): void {
        $page = $this->goToPage($url);
        $this->logIn($page, 'admin');
        if ($tabToClick) {
            $page->clickLink($tabToClick);
        }
        if ($beforeContainerRegex) {
            $this->assertMatchesRegularExpression(
                $beforeContainerRegex,
                $this->findCssAndGetText($page, $containerSelector)
            );
        }
        foreach ($valuesToSet as $selector => $value) {
            $this->findCssAndSetValue($page, $selector, $value);
        }
        $this->clickCss($page, $submitSelector);
        $this->waitForPageLoad($page);
        if ($afterContainerRegex) {
            $this->assertMatchesRegularExpression(
                $afterContainerRegex,
                $this->findCssAndGetText($page, $containerSelector)
            );
        }
    }

    /**
     * Data provider for testEmptyLinkLists().
     *
     * @return Generator<string, array>
     */
    public static function populatedRecordsProvider(): Generator
    {
        yield 'series' => [
            '/Series/1',
            'Please log in to leave a comment.'
            . ' [List All Series] [List Series Full Text] [List Series Images] [List Series People]'
            . ' [List Series Subjects/Tags]'
            . ' Language: test language 1'
            . ' second test materials (edited)'
            . ' test item (1952)'
            . ' User Comments No comments available. Please log in to leave a comment.',
        ];
        // TODO: add data so the following commented-out tests will be non-empty
        //yield 'category' => ['/Category/1', 'test description No series are listed in this category.'];
        //yield 'city' => ['/City/1', 'No information is available about this city.'];
        //yield 'country' => ['/Country/1', 'No information is available about this country.'];
        //yield 'language' => ['/Language/2', 'No information is available about this language.'];
        //yield 'material type' => ['/Material/1', 'No information is available about this material type.'];
        //yield 'publisher' => ['/Publisher/1', 'No information is available about this publisher.'];
        yield 'person' => [
            '/Person/1',
            '[List All People] [List Person Full Text]'
            . ' Sort by: Series Title Year'
            . ' Items with "test-last, test-first, extra" as Credited test person role'
            . ' test series 1'
            . ' test item (test note)',
        ];
        yield 'item' => [
            '/Item/1',
            'Please log in to manage your collection or post a review.'
            . ' (test note) View: Combined By Edition Online Full Text: test full text source 1'
            . ' Series: test series 1'
            . ' Platform: test platform'
            . ' test person role: test-last, test-first, extra (test note)'
            . ' Date: February 3, 1952 (test note)'
            . ' ISBN: 0123456789 / 9780123456786 (test note)'
            . ' OCLC Number: 12345 (test note)'
            . ' Product Code: pc-test (test note)'
            . ' Please log in to manage your collection or post a review.',
        ];
        yield 'platform' => ['/Platform/1', 'test series 1 test item'];
        //yield 'tag' => ['/Tag/1', 'No information is available about this subject/tag.'];
    }

    /**
     * Test that appropriate empty messages are provided before content is linked up.
     *
     * @param string $path            URL path to check
     * @param string $expectedMessage Content expected on page
     * @param bool   $regExMatch      Should we do a string match (false), or a regex match (true)?
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testLinkCreation')]
    #[\PHPUnit\Framework\Attributes\DataProvider('populatedRecordsProvider')]
    public function testPopulatedRecords(
        string $path,
        string $expectedMessage,
        bool $regExMatch = false
    ): void {
        $this->assertPageContent($expectedMessage, $path, regExMatch: $regExMatch);
    }

    /**
     * Assert the contents of the top and bottom controls.
     *
     * @param TraversableElement $page     Page being examined
     * @param string             $expected Expected control text
     *
     * @return void
     * @throws \Exception
     */
    protected function assertControls(TraversableElement $page, string $expected): void
    {
        $this->assertEquals($expected, $this->findCssAndGetText($page, '.controls.top'));
        $this->assertEquals($expected, $this->findCssAndGetText($page, '.controls.bottom'));
    }

    /**
     * Data provider for testCollectionBehavior().
     *
     * @return Generator<string, array>
     */
    public static function collectionBehaviorProvider(): Generator
    {
        yield 'default page, top buttons' => ['', '.controls.top'];
        yield 'default page, bottom buttons' => ['', '.controls.bottom'];
        yield 'editions view, top buttons' => ['/Editions', '.controls.top'];
        yield 'editions view, bottom buttons' => ['/Editions', '.controls.bottom'];
    }

    /**
     * Test collection management functionality.
     *
     * @param string $subPage          Subpage of item page to test.
     * @param string $controlsSelector Selector for button bar to use for testing.
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('collectionBehaviorProvider')]
    #[\PHPUnit\Framework\Attributes\Depends('testPopulateData')]
    public function testCollectionBehavior(string $subPage, string $controlsSelector): void
    {
        $page = $this->goToPage('/Item/1' . $subPage);
        $this->assertControls($page, 'Please log in to manage your collection or post a review.');
        $this->logIn($page, 'user');
        // Add to all lists:
        $this->assertControls($page, 'Submit Review Add to Have List Add to Want List Add to Sale/Trade List');
        $this->findCss($page, $controlsSelector)->clickLink('Add to Have List');
        $this->clickCss($page, '.content input[type="submit"]');
        $this->assertControls($page, 'Submit Review Modify Have List Add to Want List Add to Sale/Trade List');
        $this->findCss($page, $controlsSelector)->clickLink('Add to Want List');
        $this->clickCss($page, '.content input[type="submit"]');
        $this->assertControls($page, 'Submit Review Modify Have List Modify Want List Add to Sale/Trade List');
        $this->findCss($page, $controlsSelector)->clickLink('Add to Sale/Trade List');
        $this->clickCss($page, '.content input[type="submit"]');
        $this->assertControls($page, 'Submit Review Modify Have List Modify Want List Modify Sale/Trade List');
        // Remove from all lists:
        $this->findCss($page, $controlsSelector)->clickLink('Modify Have List');
        $this->clickCss($page, '.content input[type="submit"]', index: 1);
        $this->assertControls($page, 'Submit Review Add to Have List Modify Want List Modify Sale/Trade List');
        $this->findCss($page, $controlsSelector)->clickLink('Modify Want List');
        $this->clickCss($page, '.content input[type="submit"]', index: 1);
        $this->assertControls($page, 'Submit Review Add to Have List Add to Want List Modify Sale/Trade List');
        $this->findCss($page, $controlsSelector)->clickLink('Modify Sale/Trade List');
        $this->clickCss($page, '.content input[type="submit"]', index: 1);
        $this->assertControls($page, 'Submit Review Add to Have List Add to Want List Add to Sale/Trade List');
    }

    /**
     * Data provider for testPersonFullText().
     *
     * @return Generator<string, array>
     */
    public static function personFullTextProvider(): Generator
    {
        yield 'credit full text' => [1, 'test item'];
        yield 'no full text' => [2, null];
    }

    /**
     * Test person full text.
     *
     * @param int     $personId           ID of person to check
     * @param ?string $firstExpectedTitle First expected title on full text list (null for none expected)
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('personFullTextProvider')]
    #[\PHPUnit\Framework\Attributes\Depends('testLinkCreation')]
    public function testPersonFullText(int $personId, ?string $firstExpectedTitle): void
    {
        $page = $this->goToPage("/Person/$personId/FullText");
        if ($firstExpectedTitle) {
            $this->assertEquals($firstExpectedTitle, $this->findCssAndGetText($page, 'li'));
        } else {
            $this->unFindCss($page, 'ul');
            $this->assertEquals(
                'No full-text items are currently associated with this person.',
                $this->findCssAndGetText($page, '.content p', index: 1)
            );
        }
    }

    /**
     * Data provider for testPopulatedDatabase()
     *
     * @return Generator<string, array>
     */
    public static function populatedDatabaseProvider(): Generator
    {
        yield 'series by name' => ['by name', 'T test series 1 test series 2 (edited)', 0];
        yield 'series by category' => [
            'by category',
            'S T second test category (edited) T Back to Top ↑ test category',
            0,
        ];
        yield 'series by city' => ['by city', 'T test city test city 2 (edited)', 0];
        yield 'series by country' => ['by country', 'T test country test country 2 (edited)', 0];
        yield 'series by language' => ['by language', 'T test language 1 test language 2 (edited)', 0];
        // TODO: link a material type to a series so this test will become interesting
        //yield 'series by material type' => ['by material type', 'No material types listed in this database yet.', 0];
        yield 'series by publisher' => ['by publisher', 'T test publisher test publisher 2 (edited)', 0];
        // TODO: add a comment test so this will have content:
        //yield 'series with comments' => ['with comments', 'No comments listed.', 0];
        yield 'recently added series' => [
            'recently added',
            'Viewing page 1 of 1 test series 2 (edited) test series 1 First | Previous | 1 | Next | Last',
            0,
        ];
        yield 'people by name' => [
            'by name',
            'L T last, test-second-edited T Back to Top ↑ test-last, test-first, extra',
            1,
        ];
        yield 'people with biographical notes' => ['with biographical notes', 'L last, test-second-edited', 1];
        yield 'recently added people' => [
            'recently added',
            'Viewing page 1 of 1 last, test-second-edited test-last, test-first, extra '
            . 'First | Previous | 1 | Next | Last',
            1,
        ];
        yield 'items by name' => ['by name', 'T test item', 2];
        yield 'items by platform' => ['by platform', 'T test platform test platform 2 (edited)', 2];
        yield 'items by subject/tag' => ['by subject/tag', 'T test tag test tag 2 (edited)', 2];
        yield 'items by year' => ['by year', '1952 test item (test note)', 2];
        yield 'items with full text' => ['with full text', '/.*test series 1 test item \\(1952\\)$/', 2, true];
        // TODO: add review so this will have content:
        //yield 'items with reviews' => ['with reviews', 'No reviews listed.', 2];
        yield 'recently added items' => [
            'recently added',
            'Viewing page 1 of 1 test item First | Previous | 1 | Next | Last',
            2,
        ];
        yield 'file list' => [
            'List Files',
            'test file type 1 test file 1 test file 2 (edited) - This has been edited.',
        ];
        yield 'link list' => [
            'List Links',
            '|test link type 1 Back to Top ↑ '
            . 'test link 1 https://gamebooks.org/ \\(last verified: [\d-]+\\) '
            . 'test link 2 \\(edited\\) This has been edited. https://dimenovels.org '
            . '\\(last verified: 2025-12-01\\)|',
            null,
            true,
        ];
        yield 'user list' => ['List Registered Users', 'A U admin U Back to Top ↑ user'];
        // TODO: add reviews/comments so this will have content:
        //yield 'recent reviews' => ['Browse Recent Reviews', 'No reviews available. No comments available.'];
        // TODO: add FAQs so this will have content:
        //yield 'FAQs' => ['List All', 'No FAQs listed in this database yet.'];
    }

    /**
     * Test the behavior of a populated database.
     *
     * @param string $linkText        Text of link to click
     * @param string $expectedMessage Expected message on resulting page
     * @param ?int   $containerIndex  Index of paragraph containing link (null to search whole page)
     * @param bool   $regExMatch      Should we do a string match (false), or a regex match (true)?
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('populatedDatabaseProvider')]
    public function testPopulatedDatabase(
        string $linkText,
        string $expectedMessage,
        ?int $containerIndex = null,
        bool $regExMatch = false
    ): void {
        $this->assertPageContent($expectedMessage, '', $linkText, $containerIndex, $regExMatch);
    }
}
