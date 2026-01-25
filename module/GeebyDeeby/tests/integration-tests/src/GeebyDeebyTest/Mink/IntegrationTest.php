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

use Behat\Mink\Element\Element;
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
 *
 * @todo Add tests for edition preferred titles.
 * @todo Add test for edition copying.
 * @todo Add test to set citation on creator relationship
 * @todo Add tests for series relationships/translations
 * @todo Add tests for self-serve account editing (password change, etc.)
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
     * Approve a user by directly manipulating the database.
     *
     * @param string $username User to approve
     * @param ?int   $groupId  Group to apply (null for no group)
     * @param ?int   $personId Person ID to link
     *
     * @return void
     */
    protected function approveUserWithDirectDatabaseAccess(
        string $username,
        ?int $groupId = null,
        ?int $personId = null
    ): void {
        $userTable = $this->getServiceLocator()->get(\GeebyDeeby\Db\Table\PluginManager::class)->get('user');
        $changes = ['Person_ID' => $personId, 'Approved' => 'y'];
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
        yield 'admin' => ['admin', 'password', 'admin@example.com'];
        yield 'user' => ['user', 'password', 'user@example.com'];
        yield 'user2' => ['user2'];
        yield 'user3' => ['user3'];
    }

    /**
     * Test creation of a user.
     *
     * @param string  $username Username to create
     * @param string  $password Password of new user
     * @param ?string $email    Email address of user (null for none)
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('createUserProvider')]
    public function testCreateUser(string $username, string $password = 'password', ?string $email = null): void
    {
        $page = $this->goToPage();
        $page->clickLink('Sign Up');
        $this->findCssAndSetValue($page, '#signup_username', $username);
        $this->findCssAndSetValue($page, '#signup_fullname', 'test ' . $username);
        if ($email) {
            $this->findCssAndSetValue($page, '#signup_email', $email);
        }
        $this->findCssAndSetValue($page, '#signup_password1', $password);
        $this->findCssAndSetValue($page, '#signup_password2', $password);
        $this->clickCss($page, '.signup input[type="submit"]');
        $this->assertEquals(
            'Your request for an account has been sent; it should be approved shortly.',
            $this->findCssAndGetText($page, '.content')
        );
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
        $this->approveUserWithDirectDatabaseAccess($username, $group);

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
     * Test that an admin can approve a pending user.
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testUserApproval')]
    public function testUserApprovalByAdmin(): void
    {
        $header = 'Username Full Name Email Person Record Join Reason Options';
        $user2 = 'user2 I want to submit spam to the site. Reject';
        $user3 = str_replace('2', '3', $user2);
        $page = $this->goToPage('/edit/Approve');
        $this->logIn($page, 'admin');
        $this->assertEquals('user2', $this->findCssAndGetValue($page, '#Username_3'));
        $this->assertEquals("$header $user2 $user3", $this->findCssAndGetText($page, '.content table'));
        // Click the approve button:
        $this->clickCss($page, '#UserButtons_3 #approveForm0 input');
        $this->waitForPageLoad($page);
        // If all went to plan, the user should have disappeared:
        $this->assertEquals("$header $user3", $this->findCssAndGetText($page, '.content table'));
        // Let's also test rejecting the other user:
        $this->assertEquals('user3', $this->findCssAndGetValue($page, '#Username_4'));
        // First cancel the alert to keep the user, and confirm that nothing happened:
        $this->clickCss($page, '#UserButtons_4 button');
        $this->getMinkSession()->getDriver()->dismissAlert();
        $this->assertEquals("$header $user3", $this->findCssAndGetText($page, '.content table'));
        // Now delete for real:
        $this->clickCss($page, '#UserButtons_4 button');
        $this->getMinkSession()->getDriver()->acceptAlert();
        $this->waitForPageLoad($page);
        $this->assertEquals($header, $this->findCssAndGetText($page, '.content table'));
    }

    /**
     * Test that an admin can delete a user.
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testUserApprovalByAdmin')]
    public function testDeleteUser(): void
    {
        $page = $this->goToPage('/edit/UserList');
        $this->logIn($page, 'admin');
        // Based on what happened in testUserApprovalByAdmin, we expect the user list to contain
        // user2 (approved) but not user3 (rejected).
        $userList = $this->findCssAndGetText($page, '#user_list');
        $this->assertStringNotContainsString('user3', $userList);
        $this->assertStringContainsString('user2', $userList);
        // Test that we can cancel a delete operation:
        $this->clickCss($page, '#deleteUser3');
        $this->getMinkSession()->getDriver()->dismissAlert();
        $this->assertStringContainsString('user2', $userList);
        // Now delete the approved user:
        $this->clickCss($page, '#deleteUser3');
        $this->getMinkSession()->getDriver()->acceptAlert();
        $this->waitForPageLoad($page);
        $this->assertStringNotContainsString('user2', $this->findCssAndGetText($page, '#user_list'));
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
        yield 'person 3' => [
            'PersonList',
            '#add_person',
            ['#First_Name' => 'test-third', '#Last_Name' => 'lastname'],
            '#person_list',
            'lastname, test-third',
            7, // number is higher due to jump links
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
        yield 'user group' => [
            'UserList',
            '#add_user_group',
            ['#Group_Name' => 'test group'],
            '#user_group_list',
            null,
            2,
        ];
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
     * Test linking categories to a series.
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testPopulateData')]
    public function testCategoryLinking(): void
    {
        $page = $this->goToPage('/edit/Series/1');
        $this->logIn($page, 'admin');
        $page->clickLink('Categories');
        $this->clickCss($page, '#Category_ID_1');
        $this->clickCss($page, '.tab-pane.active input[type="submit"]');
    }

    /**
     * Test creating container items using volume/number numbering.
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testPopulateData')]
    public function testBuildingIssues(): void
    {
        $page = $this->goToPage('/edit/Series/2');
        $this->logIn($page, 'admin');

        // Build two issues:
        foreach ([1, 2] as $issue) {
            $this->clickCss($page, '#add_item');
            $issueData = [
                '#Item_Name' => 'example issue ' . $issue,
                '#Item_Length' => '32 pages',
                '#Item_Endings' => '1',
                '#Item_Errata' => 'none -- perfection!',
                '#Item_Thanks' => 'for nothing',
            ];
            $this->populateForm($page, $issueData);
            $this->clickCss($page, '.modal-body input[type="submit"]');
            $this->waitForPageLoad($page);
            // We only created one item prior to this test, so IDs will start at 2;
            // take advantage of that fact to identify the relevant order control.
            $this->findCssAndSetValue($page, '#order' . ($issue + 1), '1,' . $issue);
            // Order defaults to zero, so the submit button we want will always be
            // the first one on the page:
            $this->clickCss($page, '.list_item input[type="submit"]');
        }
    }

    /**
     * Test inserting articles into container items.
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testBuildingIssues')]
    public function testBuildingArticles(): void
    {
        // Now add articles to one of the editions:
        $page = $this->goToPage('/edit/Edition/2');
        $this->logIn($page, 'admin');
        $this->assertSame('2: example issue 1', $this->findCssAndGetValue($page, '#Edition_Item_ID'));
        $page->clickLink('Contents (Attached Items)');
        $button = $this->findCss($page, '.tab-pane.active button');
        $this->assertSame('Add New Item', $button->getText());
        foreach ([1, 2] as $article) {
            $button->click();
            $articleData = [
                '#Item_Name' => 'example article ' . $article,
                '#Item_Length' => '16 pages',
                '#Item_Endings' => '0',
                '#Item_Errata' => 'undetermined',
                '#Item_Thanks' => 'to test suites',
            ];
            $this->populateForm($page, $articleData);
            $this->clickCss($page, '.modal-body input[type="submit"]');
            $this->waitForPageLoad($page);
            $this->findCssAndSetValue($page, '#item_order_' . ($article + 3), (string)$article);
            $this->clickCss($page, '.list_item input[type="submit"]');
        }
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
        yield 'user group' => $deriveTestCase($populateData['user group']);
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
     * Data provider for testSetAttributes().
     *
     * @return Generator<string, array>
     */
    public static function setAttributesProvider(): Generator
    {
        yield 'item' => ['Item'];
        yield 'series' => ['Series'];
        yield 'edition' => ['Edition'];
        yield 'tag' => ['Tag'];
    }

    /**
     * Test setting custom attributes.
     *
     * @param string $type Type of item to set attributes on
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testPopulateData')]
    #[\PHPUnit\Framework\Attributes\DataProvider('setAttributesProvider')]
    public function testSetAttributes(string $type): void
    {
        $page = $this->goToPage('/edit/' . $type . '/1');
        $this->logIn($page, 'admin');
        // Search for the appropriate button:
        for ($i = 0; $i < 2; $i++) {
            $toggleButton = $this->findCss($page, 'button[data-toggle="collapse"]', index: $i);
            if ('Toggle Additional Attributes' === $toggleButton->getText()) {
                break;
            }
        }
        $this->assertSame('Toggle Additional Attributes', $toggleButton->getText());
        $toggleButton->click();
        $input = $this->findCss($page, '#' . $type . '_Attribute_1');
        $this->assertTrue($input->isVisible());
        $input->setValue('attribute value for ' . $type);
        $this->clickCss($page, '.edit_container input[type="submit"]');
    }

    /**
     * Assert the current default material type.
     *
     * @param int  $type  Expected default material type ID.
     * @param bool $logIn Do we need to log in?
     *
     * @return void
     */
    protected function assertDefaultMaterialType(int $type, bool $logIn = false): void
    {
        $page = $this->goToPage('/edit/Series/1');
        if ($logIn) {
            $this->logIn($page, 'admin');
        }
        $this->clickCss($page, '#add_item');
        $this->assertSame((string)$type, $this->getSelectedOption($page, '#Material_Type_ID'));
    }

    /**
     * Test setting a default material type.
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testEditExistingData')]
    public function testSetDefaultMaterialType(): void
    {
        $this->assertDefaultMaterialType(2, logIn: true);
        $page = $this->goToPage('/edit/MaterialTypeList');
        $page->clickLink('test material'); // click material type 1
        $this->clickCss($page, '#Default'); // set default
        $this->clickCss($page, '.modal-body input[type="submit"]');
        $this->assertDefaultMaterialType(1); // default should have changed!
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
        yield 'file related item' => [
            '/edit/File/1',
            null,
            ['#file_item_id' => '1'],
            '#item_list',
            '/^No relevant items.$/',
            '/test item/',
            '#link_item',
        ];
        yield 'file related series' => [
            '/edit/File/1',
            null,
            ['#file_series_id' => '1'],
            '#series_list',
            '/^No relevant series.$/',
            '/test series 1/',
            '#link_series',
        ];
        yield 'file related person' => [
            '/edit/File/1',
            null,
            ['#file_person_id' => '1'],
            '#person_list',
            '/^No relevant people.$/',
            '/test-last, test-first, extra/',
            '#link_person',
        ];
        yield 'link related item' => [
            '/edit/Link/2',
            null,
            ['#link_item_id' => '1'],
            '#item_list',
            '/^No relevant items.$/',
            '/test item/',
            '#link_item',
        ];
        yield 'link related series' => [
            '/edit/Link/2',
            null,
            ['#link_series_id' => '1'],
            '#series_list',
            '/^No relevant series.$/',
            '/test series 1/',
            '#link_series',
        ];
        yield 'link related person' => [
            '/edit/Link/2',
            null,
            ['#link_person_id' => '1'],
            '#person_list',
            '/^No relevant people.$/',
            '/test-last, test-first, extra/',
            '#link_person',
        ];
        yield 'pseudonym' => [
            '/edit/Person/1',
            null,
            ['#pseudo_name' => '2'],
            '#aliasrealname_list',
            '/^No relevant names.$/',
            '/last, test-second-edited/',
            '#add_relationship',
        ];
        yield 'person uri' => [
            '/edit/Person/1',
            null,
            ['#uri' => 'http://person/1'],
            '#uri_list',
            '/^No URIs defined.$/',
            '|http://person/1 \\(edited_test_predicate\\)|',
            '#add_uri',
        ];
        yield 'publisher address' => [
            '/edit/Publisher/1',
            null,
            ['#Country_ID' => '1', '#City_ID' => '1', '#Street' => 'fake st.'],
            '#address_list',
            '/^No addresses set.$/',
            '/test country -- test city -- fake st./',
        ];
        yield 'publisher imprint' => [
            '/edit/Publisher/1',
            'Imprints',
            ['#Imprint' => 'test imprint'],
            '#imprint_list',
            '/^No imprints set.$/',
            '/test imprint/',
        ];
        yield 'publisher URI' => [
            '/edit/Publisher/1',
            'URIs',
            ['#uri' => 'http://publisher/1'],
            '#uri_list',
            '/^No URIs defined.$/',
            '|http://publisher/1 \\(edited_test_predicate\\)|',
        ];
        yield 'tag URI' => [
            '/edit/Tag/1',
            null,
            ['#uri' => 'http://tag/1'],
            '#uri_list',
            '/^No URIs defined.$/',
            '|http://tag/1 \\(edited_test_predicate\\)|',
        ];
        yield 'tag relationship' => [
            '/edit/Tag/1',
            'Relationships',
            ['#target_tag' => '2'],
            '#relationship_list',
            '/^No relationships defined.$/',
            '/test tag relationship: test tag 2 \\(edited\\)/',
        ];
        yield 'tag to item link' => [
            '/edit/Tag/1',
            'Linked Items',
            ['#Item_ID' => '1'],
            '#item_list',
            '/^No relevant items.$/',
            '/test item/',
        ];
        yield 'item to tag link' => [
            '/edit/Item/1',
            'Subjects/Tags',
            ['#Tag_ID' => '2'],
            '#tag_list',
            '/test tag[^2]*$/',
            '/test tag.*test tag 2 \\(edited\\)/',
        ];
        yield 'item alternate title' => [
            '/edit/Item/1',
            'Alternate Titles',
            ['#Alt_Title' => 'test alternate title', '#Alt_Title_Note' => '1'],
            '#alttitle_list',
            '/^No alternate titles set.$/',
            '/test alternate title \\(test note\\)/',
        ];
        yield 'item creator' => [
            '/edit/Item/1',
            'Creators',
            ['#creator_person' => '2'],
            '#creator_list',
            '/No creators.$/',
            '/test person role: last, test-second-edited/',
        ];
        yield 'item description' => [
            '/edit/Item/1',
            'Descriptions',
            ['#Description' => 'Test description'],
            '#description_list',
            '/^No descriptions set.$/',
            '/Test description \\(Source: User Summary\\)/',
        ];
        yield 'item adaptation' => [
            '/edit/Item/5',
            'Adaptations',
            ['#adapt_name' => '4'],
            '#adaptationfrom_list',
            '/^No relevant items.$/',
            '/example article 1/',
        ];
        $attachmentNote = 'IMPORTANT: In the vast majority of cases, items should be attached at the individual'
            . ' EDITION level, not here at the ITEM level.';
        yield 'item attachments' => [
            '/edit/Item/5',
            'Attached Items',
            ['#attachment_name' => '4', '#Attachment_Note' => '1'],
            '#attachment_list',
            '/^' . $attachmentNote . ' No items.$/',
            '/example article 1 \\(second test material \\(edited\\), test note\\)/',
        ];
        yield 'item creators' => [
            '/edit/Item/5',
            'Creators',
            ['#creator_person' => '1'],
            '#creator_list',
            '/^No creators.$/',
            '/test person role: test-last, test-first, extra/',
        ];
        yield 'item credits' => [
            '/edit/Item/5',
            'Credits',
            ['#credit_person' => '2', '#credit_note' => '2'],
            '#credit_list',
            '/^No credits.$/',
            '/test person role: last, test-second-edited \\(test note 2 \\(edited\\)\\)/',
        ];
        yield 'item to item reference' => [
            '/edit/Item/5',
            'References',
            ['#item_bib_id' => '1'],
            '#aboutitem_list',
            '/^No relevant items.$/',
            '/test item/',
            '#add_item_reference',
        ];
        yield 'item to series reference' => [
            '/edit/Item/5',
            'References',
            ['#series_bib_id' => '1'],
            '#aboutseries_list',
            '/^No relevant series.$/',
            '/test series 1/',
            '#add_series_reference',
        ];
        yield 'item to person reference' => [
            '/edit/Item/5',
            'References',
            ['#person_bib_id' => '1'],
            '#aboutperson_list',
            '/^No relevant people.$/',
            '/test-last, test-first, extra/',
            '#add_person_reference',
        ];
        yield 'item relationship' => [
            '/edit/Item/5',
            'Relationships',
            ['#target_item' => '4'],
            '#relationship_list',
            '/^No relationships defined.$/',
            '/test item relationship 1: example article 1/',
        ];
        yield 'item translation' => [
            '/edit/Item/5',
            'Translations',
            ['#trans_name' => '4'],
            '#translationfrom_list',
            '/^No relevant items.$/',
            '/example article 1/',
        ];
        yield 'series alternate title' => [
            '/edit/Series/1',
            'Alternate Titles',
            ['#Alt_Title' => 'test alternate series title', '#Alt_Title_Note' => '1'],
            '#alttitle_list',
            '/^No alternate titles set.$/',
            '/test alternate series title \\(test note\\)/',
        ];
        yield 'series material type' => [
            '/edit/Series/1',
            'Material Types',
            [],
            '#material_list',
            '/^No material types set.$/',
            '/test material/',
        ];
        yield 'series publisher' => [
            '/edit/Series/1',
            'Publishers',
            ['#Publisher_ID' => '1', '#Publisher_Note_ID' => '1'],
            '#publisher_list',
            '/^No publishers set.$/',
            '/test publisher \\(test note\\)/',
        ];
        yield 'series relationship' => [
            '/edit/Series/1',
            'Relationships',
            ['#target_series' => '2'],
            '#relationship_list',
            '/^No relationships defined.$/',
            '/test series 2 \\(edited\\)/',
        ];
        yield 'series translation' => [
            '/edit/Series/1',
            'Translations',
            ['#trans_name' => '2'],
            '#translationfrom_list',
            '/^No relevant series.$/',
            '/test series 2 \\(edited\\)/',
        ];
    }

    /**
     * Assert that the page contains a container whose text matches a regular expression (but skip if the
     * regular expression is null).
     *
     * @param Element $page     Page containing container
     * @param string  $selector Selector containing text to check
     * @param ?string $regex    Regular expression to match (or null to skip check)
     *
     * @return void
     */
    protected function assertOptionalRegexInContainer(Element $page, string $selector, ?string $regex): void
    {
        if ($regex) {
            $this->assertMatchesRegularExpression($regex, $this->findCssAndGetText($page, $selector));
        }
    }

    /**
     * Add a link to a container and make assertions to confirm its success.
     *
     * @param Element $page                 Active page
     * @param array   $valuesToSet          Array of selector => value (data to link)
     * @param string  $containerSelector    Selector for container listing links
     * @param ?string $beforeContainerRegex Regular expression to check in container before linking data (null to skip)
     * @param ?string $afterContainerRegex  Regular expression to check in container after linking data (null to skip)
     * @param string  $submitSelector       Selector for submit button
     *
     * @return void
     */
    protected function addLinkToEmptyContainerAndAssertSuccess(
        Element $page,
        array $valuesToSet,
        string $containerSelector,
        ?string $beforeContainerRegex = null,
        ?string $afterContainerRegex = null,
        string $submitSelector = '.active .edit_container input[type="submit"]'
    ) {
        $this->assertOptionalRegexInContainer($page, $containerSelector, $beforeContainerRegex);
        foreach ($valuesToSet as $selector => $value) {
            $this->findCssAndSetValue($page, $selector, $value);
        }
        $this->clickCss($page, $submitSelector);
        $this->waitForPageLoad($page);
        $this->assertOptionalRegexInContainer($page, $containerSelector, $afterContainerRegex);
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
    #[\PHPUnit\Framework\Attributes\Depends('testBuildingArticles')]
    #[\PHPUnit\Framework\Attributes\Depends('testSetDefaultMaterialType')]
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
        $this->addLinkToEmptyContainerAndAssertSuccess(
            $page,
            $valuesToSet,
            $containerSelector,
            $beforeContainerRegex,
            $afterContainerRegex,
            $submitSelector
        );
        // If this is the first element added to the container, delete it and re-add it
        // to exercise delete functionality.
        if ($beforeContainerRegex && $afterContainerRegex && str_starts_with($beforeContainerRegex, '/^No ')) {
            $this->clickCss($page, $containerSelector . ' .ui-icon-trash');
            $this->getMinkSession()->getDriver()->acceptAlert();
            $this->waitForPageLoad($page);
            $this->addLinkToEmptyContainerAndAssertSuccess(
                $page,
                $valuesToSet,
                $containerSelector,
                $beforeContainerRegex,
                $afterContainerRegex,
                $submitSelector
            );
        }
    }

    /**
     * Test setting custom full text link attributes.
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testLinkCreation')]
    public function testSetFullTextAttributes(): void
    {
        $page = $this->goToPage('/edit/Edition/1');
        $this->logIn($page, 'admin');
        $page->clickLink('Full Text Links');
        $this->clickCss($page, '#fulltext_list .ui-icon-gear');
        $this->waitForPageLoad($page);
        $input = $this->findCss($page, '#FullText_Attribute_1');
        $this->assertTrue($input->isVisible());
        $input->setValue('attribute value for full text link');
        $this->clickCss($page, '#modal input[type="submit"]');
    }

    /**
     * Data provider for testSuggestions().
     *
     * @return Generator<string, array>
     */
    public static function suggestionsProvider(): Generator
    {
        $expectedEditions = '1: test series 1 edition';
        for ($i = 2; $i <= 5; $i++) {
            $expectedEditions .= "\n$i: test series 2 edition";
        }
        yield 'edition' => ['Edition', 'test', $expectedEditions];
        yield 'item' => ['Item', 'test', "1: test alternate title [alt. title for test item]\n1: test item"];
        yield 'note' => ['Note', 'test', "1: test note\n2: test note 2 (edited)"];
        yield 'person' => [
            'Person',
            'test',
            "2: test-second-edited last\n3: test-third lastname\n1: test-first test-last, extra",
        ];
        yield 'predicate' => ['Predicate', 'test', '1: test_predicate'];
        yield 'publisher' => ['Publisher', 'test', "1: test publisher\n2: test publisher 2 (edited)"];
        yield 'series' => [
            'Series',
            'test',
            "1: test alternate series title [alt. title for test series 1]\n"
            . "1: test series 1\n2: test series 2 (edited)",
        ];
        yield 'tag' => ['Tag', 'test', "1: test tag\n2: test tag 2 (edited)"];
    }

    /**
     * Test suggestions.
     *
     * @param string $type     Suggestion type
     * @param string $query    Query
     * @param string $expected Expected result
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testLinkCreation')]
    #[\PHPUnit\Framework\Attributes\DataProvider('suggestionsProvider')]
    public function testSuggestions(string $type, string $query, string $expected): void
    {
        $this->assertSame(
            $expected,
            trim(file_get_contents($this->getGeebyDeebyUrl("/Suggest/$type?q=" . urlencode($query))))
        );
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
     * Data provider for testCollectionAddAndRemoveControls().
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
     * Test collection management add/remove/control label functionality.
     *
     * @param string $subPage          Subpage of item page to test.
     * @param string $controlsSelector Selector for button bar to use for testing.
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('collectionBehaviorProvider')]
    #[\PHPUnit\Framework\Attributes\Depends('testPopulateData')]
    public function testCollectionAddAndRemoveControls(string $subPage, string $controlsSelector): void
    {
        $page = $this->goToPage('/Item/1' . $subPage);
        $this->assertControls($page, 'Please log in to manage your collection or post a review.');
        $this->logIn($page, 'user');
        $this->waitForPageLoad($page);
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
     * Test setting up collections for two different users to facilitate a potential trade.
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testPopulateData')]
    public function testSettingUpPotentialTrade(): void
    {
        // Now set up test scenario with buyer/seller:
        $page = $this->goToPage('/Item/1');
        $controlsSelector = '.controls.top';
        $this->assertControls($page, 'Please log in to manage your collection or post a review.');
        $this->logIn($page, 'user');
        $this->findCss($page, $controlsSelector)->clickLink('Add to Have List');
        $this->clickCss($page, '.content input[type="submit"]');
        $this->assertControls($page, 'Submit Review Modify Have List Add to Want List Add to Sale/Trade List');
        $this->findCss($page, $controlsSelector)->clickLink('Add to Sale/Trade List');
        $this->clickCss($page, '.content input[type="submit"]');
        $this->assertControls($page, 'Submit Review Modify Have List Add to Want List Modify Sale/Trade List');
        $page->clickLink('Log Out');
        $this->logIn($page, 'admin');
        $this->findCss($page, $controlsSelector)->clickLink('Add to Want List');
        $this->clickCss($page, '.content input[type="submit"]');
        $this->assertControls($page, 'Submit Review Add to Have List Modify Want List Add to Sale/Trade List');
    }

    /**
     * Test submitting an item review.
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testPopulateData')]
    public function testReviewSubmission(): void
    {
        $page = $this->goToPage('/Item/1');
        $this->logIn($page, 'user');
        $page->clickLink('Submit Review');
        $this->findCssAndSetValue($page, '#review', 'this is my review');
        $this->clickCss($page, '.content input[type="submit"]');
        $this->assertSame(
            'Your review has been saved. It will appear on the site once it has been approved by an administrator.',
            $this->findCssAndGetText($page, '.content p')
        );
        $page->clickLink('Back to Item');
        $page->clickLink('Edit Review');
        $this->assertStringStartsWith(
            'You have already reviewed this item.',
            $this->findCssAndGetText($page, '.disclaimer')
        );
        $this->assertSame(
            'this is my review',
            $this->findCssAndGetValue($page, '#review')
        );
    }

    /**
     * Test approving an item review.
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testReviewSubmission')]
    public function testReviewApproval(): void
    {
        $page = $this->goToPage('/edit/Approve');
        $this->logIn($page, 'admin');
        $this->assertSame(
            'test item user Approve Reject',
            $this->findCssAndGetText($page, '#PendingReview_2_1')
        );
        $this->assertSame(
            'this is my review',
            $this->findCssAndGetValue($page, '#ReviewText_2_1')
        );
        $button = $this->findCss($page, '#ReviewButtons_2_1 button');
        $this->assertSame('Approve', $button->getText());
        $button->click();
    }

    /**
     * Test submitting a series comment.
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testPopulateData')]
    public function testCommentSubmission(): void
    {
        $page = $this->goToPage('/Series/1');
        $this->logIn($page, 'user');
        $page->clickLink('Submit Comment');
        $this->findCssAndSetValue($page, '#review', 'this is my comment');
        $this->clickCss($page, '.content input[type="submit"]');
        $this->assertSame(
            'Your comment has been saved. It will appear on the site once it has been approved by an administrator.',
            $this->findCssAndGetText($page, '.content p')
        );
        $page->clickLink('Back to Series');
        $page->clickLink('Edit Comment');
        $this->assertStringStartsWith(
            'You have already commented on this series.',
            $this->findCssAndGetText($page, '.disclaimer')
        );
        $this->assertSame(
            'this is my comment',
            $this->findCssAndGetValue($page, '#review')
        );
    }

    /**
     * Test approving a series comment.
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testCommentSubmission')]
    public function testCommentApproval(): void
    {
        $page = $this->goToPage('/edit/Approve');
        $this->logIn($page, 'admin');
        $this->assertSame(
            'test series 1 user Approve Reject',
            $this->findCssAndGetText($page, '#PendingComment_2_1')
        );
        $this->assertSame(
            'this is my comment',
            $this->findCssAndGetValue($page, '#CommentText_2_1')
        );
        $button = $this->findCss($page, '#CommentButtons_2_1 button');
        $this->assertSame('Approve', $button->getText());
        $button->click();
    }

    /**
     * Test setting the advanced publisher controls.
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testLinkCreation')]
    public function testSetPublisherDetails(): void
    {
        $page = $this->goToPage('/edit/Series/1');
        $this->logIn($page, 'admin');
        $page->clickLink('Publishers');
        $this->clickCss($page, '#publisher_list .ui-icon-gear');
        $this->waitForPageLoad($page);
        $this->findCssAndSetValue($page, '#Address_ID', '2');
        $this->findCssAndSetValue($page, '#Imprint_ID', '2');
        $this->clickCss($page, '.modal-body input[type="submit"]');
        $this->waitForPageLoad($page);
        $this->assertSame(
            'test publisher (test imprint: test country -- test city -- fake st. - test note)',
            $this->findCssAndGetText($page, '#publisher_list td')
        );
    }

    /**
     * Test setting a preferred publisher on an edition.
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testSetPublisherDetails')]
    public function testSetPreferredPublisher(): void
    {
        $page = $this->goToPage('/edit/Edition/1');
        $this->logIn($page, 'admin');
        $page->clickLink('Preferred Publisher');
        $this->findCssAndSetValue($page, '#Series_Publisher_ID', '2');
        $this->clickCss($page, '.tab-pane.active input[type="submit"]');
    }

    /**
     * Data provider for testPopulatedRecords().
     *
     * @return Generator<string, array>
     */
    public static function populatedRecordsProvider(): Generator
    {
        yield 'series 1' => [
            '/Series/1',
            'Please log in to leave a comment.'
            . ' [List All Series] [List Series Full Text] [List Series Images] [List Series People]'
            . ' [List Series Subjects/Tags]'
            . ' Language: test language 1'
            . ' Alternate Title: test alternate series title (test note)'
            . ' Publisher: test publisher (test city: fake st.) (test imprint imprint) -- test country (test note)'
            . ' Category: test category'
            . ' Translated From: test series 2 (edited) (test language 1)'
            . ' test series attribute: attribute value for Series'
            . ' test series relationship: test series 2 (edited)'
            . ' second test materials (edited)'
            . ' test item (1952)'
            . ' Related Documents test file type 1 test file 1'
            . ' Bibliography of Items About "test series 1" second test materials (edited) example article 2'
            . ' Related Links test link 2 (edited) This has been edited. https://dimenovels.org'
            . ' (last verified: 2025-12-01)'
            . ' User Comments this is my comment --user Please log in to leave a comment.',
        ];
        yield 'series 2 (with volume/issue numbering)' => [
            '/Series/2',
            'Please log in to leave a comment.'
            . ' [List All Series] [List Series Full Text] [List Series Images] [List Series People]'
            . ' [List Series Subjects/Tags]'
            . ' Language: test language 1'
            . ' Translated Into: test series 1 (test language 1)'
            . ' This has been edited.'
            . ' second test materials (edited)'
            . ' v. 1, no. 1. example issue 1 (example article 1 and 1 more item)'
            . ' v. 1, no. 2. example issue 2'
            . ' User Comments No comments available. Please log in to leave a comment.',
        ];
        yield 'category' => ['/Category/1', 'test description T test series 1'];
        yield 'city' => ['/City/1', 'T test series 1'];
        yield 'country' => ['/Country/1', 'T test series 1'];
        yield 'language' => ['/Language/1', 'T test series 1 test series 2 (edited)'];
        yield 'material type' => ['/Material/1', 'T test series 1'];
        yield 'publisher' => ['/Publisher/1', 'External Identifier: http://publisher/1 T test series 1'];
        yield 'person' => [
            '/Person/1',
            '[List All People] [List Person Full Text]'
            . ' Pseudonym For: last, test-second-edited'
            . ' External Identifier: http://person/1'
            . ' Sort by: Series Title Year'
            . ' Items with "test-last, test-first, extra" as Cited test person role'
            . ' test series 2 (edited)'
            . ' example article 2'
            . ' Items with "test-last, test-first, extra" as Credited test person role'
            . ' test series 1'
            . ' test item (test note)'
            . ' Related Documents test file type 1 test file 1'
            . ' Bibliography of Items About "test-last, test-first, extra"'
            . ' second test materials (edited) example article 2'
            . ' Related Links test link 2 (edited) This has been edited. https://dimenovels.org'
            . ' (last verified: 2025-12-01)',
        ];
        yield 'person 2' => [
            '/Person/2',
            '[List All People] [List Person Full Text]'
            . ' Pseudonym: test-last, test-first, extra'
            . ' bio'
            . ' Sort by: Series Title Year'
            . ' Items with "last, test-second-edited" as Cited test person role'
            . ' test series 1'
            . ' test item'
            . ' Items with "last, test-second-edited" as Credited test person role'
            . ' test series 2 (edited)'
            . ' example article 2 (test note 2 (edited))',
        ];
        yield 'item (self-contained)' => [
            '/Item/1',
            'Please log in to manage your collection or post a review.'
            . ' (test note) View: Combined By Edition Online Full Text: test full text source 1'
            . ' (test full text attribute 1: attribute value for full text link)'
            . ' Series: test series 1'
            . ' Alternate Title: test alternate title (test note)'
            . ' Platform: test platform'
            . ' Subjects / Tags: test tag test tag 2 (edited)'
            . ' test person role: test-last, test-first, extra (pseudonym used by last, test-second-edited) (test note)'
            . ' Date: February 3, 1952 (test note)'
            . ' ISBN: 0123456789 / 9780123456786 (test note)'
            . ' Publisher: test publisher (test city: fake st.) (test imprint imprint) -- test country (test note)'
            . ' OCLC Number: 12345 (test note)'
            . ' Product Code: pc-test (test note)'
            . ' test item attribute 1: attribute value for Item'
            . ' test edition attribute 1: attribute value for Edition'
            . ' User Summary: Test description'
            . ' user\'s Thoughts: this is my review More reviews by user'
            . ' Users Who Own This Item: user Users Who Want This Item: admin Users with Extra Copies: user'
            . ' Please log in to manage your collection or post a review.'
            . ' Related Documents test file type 1 test file 1'
            . ' Bibliography of Items About "test item" second test materials (edited) example article 2'
            . ' Related Links test link 2 (edited) This has been edited. https://dimenovels.org'
            . ' (last verified: 2025-12-01)',
        ];
        yield 'item (with children)' => [
            '/Item/2',
            'Please log in to manage your collection or post a review.'
            . ' View: Combined By Edition'
            . ' Series: test series 2 (edited) — v. 1 no. 1'
            . ' Contents: example article 1 (second test material (edited))'
            . ' example article 2 (second test material (edited))'
            . ' Length: 32 pages Number of Endings: 1 Errata: none -- perfection! Special Thanks: for nothing'
            . ' Please log in to manage your collection or post a review.',
        ];
        yield 'item (with parents and relationships)' => [
            '/Item/4',
            'Please log in to manage your collection or post a review.'
            . ' View: Combined By Edition'
            . ' Series: test series 2 (edited) — v. 1 no. 1'
            . ' Contained In: example article 2 (second test material (edited), test note)'
            . ' Part of: example issue 1 (second test material (edited))'
            . ' Translated Into: example article 2 (test language 1)'
            . ' Adapted Into: example article 2 (second test material (edited))'
            . ' Length: 16 pages Errata: undetermined Special Thanks: to test suites'
            . ' Please log in to manage your collection or post a review.',
        ];
        yield 'item (also with parents and relationships)' => [
            '/Item/5',
            'Please log in to manage your collection or post a review.'
            . ' View: Combined By Edition'
            . ' Series: test series 2 (edited) — v. 1 no. 1'
            . ' Contains: example article 1 (second test material (edited), test note)'
            . ' Part of: example issue 1 (second test material (edited))'
            . ' Translated From: example article 1 (test language 1)'
            . ' Adapted From: example article 1 (second test material (edited))'
            . ' test item relationship 1: example article 1'
            . ' test person role: last, test-second-edited (test note 2 (edited))'
            . ' Length: 16 pages Errata: undetermined Special Thanks: to test suites'
            . ' Please log in to manage your collection or post a review.',
        ];
        yield 'fully populated edition' => [
            '/Edition/1',
            '(test note) Online Full Text: test full text source 1'
            . ' (test full text attribute 1: attribute value for full text link)'
            . ' Series: test series 1'
            . ' Item: test item'
            . ' Platform: test platform'
            . ' test person role: test-last, test-first, extra (pseudonym used by last, test-second-edited) (test note)'
            . ' Date: February 3, 1952 (test note)'
            . ' Publisher: test publisher (test city: fake st.) (test imprint imprint) -- test country (test note)'
            . ' ISBN: 0123456789 / 9780123456786 (test note)'
            . ' OCLC Number: 12345 (test note)'
            . ' Product Code: pc-test (test note)'
            . ' test edition attribute 1: attribute value for Edition',
        ];
        yield 'parent edition' => [
            '/Edition/2',
            'Series: test series 2 (edited) v. 1 no. 1'
            . ' Item: example issue 1'
            . ' Contents: example article 1 example article 2'
            . ' Length: 32 pages Number of Endings: 1',
        ];
        yield 'child edition' => [
            '/Edition/4',
            'Series: test series 2 (edited) v. 1 no. 1'
            . ' Item: example article 1'
            . ' Length: 16 pages',
        ];
        yield 'platform' => ['/Platform/1', 'test series 1 test item'];
        yield 'tag' => [
            '/Tag/1',
            'test tag attribute: attribute value for Tag'
            . ' test tag relationship: test tag 2 (edited)'
            . ' External Identifier: http://tag/1'
            . ' Sort by: Series Title'
            . ' test series 1'
            . ' test item',
        ];
        yield 'user 1'  => [
            '/User/1',
            '[List All Users]'
            . ' Full Name: test admin'
            . ' Email Address: Please log in to see this user\'s email address.'
            . ' Collections: View Have/Want Lists (0 items owned, 1 items wanted) [List Potential Sellers]'
            . ' View Sale/Trade Lists (0 items for sale/trade) [List Potential Buyers]'
            . ' Reviews and Comments: 0 item reviews. 0 series comments.',
        ];
        yield 'user 1 collection'  => [
            '/User/1/Collection',
            'Get more information on this user. Items in test language 1 test series 1 Wants: test item Has: None.',
        ];
        yield 'user 1 extras'  => [
            '/User/1/Extras',
            'Get more information on this user. No items listed.',
        ];
        $disclaimer = 'DISCLAIMER: Items offered for sale or trade on this site are submitted'
            . ' by users and are not verified in any way. My Gamebook Web Page makes no guarantees about the accuracy'
            . ' of these listings and cannot be held responsible for dishonest users. Please be cautious!';
        yield 'user 1 sellers'  => [
            '/User/1/Sellers',
            'Get more information on this user. ' . $disclaimer
            . ' user test series 1 test item',
        ];
        yield 'user 1 buyers'  => [
            '/User/1/Buyers',
            'Get more information on this user. No buyers available.',
        ];
        yield 'user 1 reviews'  => [
            '/User/1/Reviews',
            'Get more information on this user. No reviews listed.',
        ];
        yield 'user 1 comments'  => [
            '/User/1/Comments',
            'Get more information on this user. No comments listed.',
        ];
        yield 'user 2'  => [
            '/User/2',
            '[List All Users]'
            . ' Full Name: test user'
            . ' Email Address: Please log in to see this user\'s email address.'
            . ' Collections: View Have/Want Lists (1 items owned, 0 items wanted) [List Potential Sellers]'
            . ' View Sale/Trade Lists (1 items for sale/trade) [List Potential Buyers]'
            . ' Reviews and Comments: 1 item review. 1 series comment.',
        ];
        yield 'user 2 collection'  => [
            '/User/2/Collection',
            'Get more information on this user. Items in test language 1 test series 1 Wants: None. Has: test item',
        ];
        yield 'user 2 extras'  => [
            '/User/2/Extras',
            'Get more information on this user. ' . $disclaimer . ' test series 1 test item',
        ];
        yield 'user 2 sellers'  => [
            '/User/2/Sellers',
            'Get more information on this user. No sellers available.',
        ];
        yield 'user 2 buyers'  => [
            '/User/2/Buyers',
            'Get more information on this user. admin test series 1 test item',
        ];
        yield 'user 2 reviews'  => [
            '/User/2/Reviews',
            'Get more information on this user. test series 1 test item',
        ];
        yield 'user 2 comments'  => [
            '/User/2/Comments',
            'Get more information on this user. T test series 1',
        ];
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
    #[\PHPUnit\Framework\Attributes\Depends('testSetPreferredPublisher')]
    #[\PHPUnit\Framework\Attributes\Depends('testReviewApproval')]
    #[\PHPUnit\Framework\Attributes\Depends('testCommentApproval')]
    #[\PHPUnit\Framework\Attributes\Depends('testCategoryLinking')]
    #[\PHPUnit\Framework\Attributes\Depends('testSettingUpPotentialTrade')]
    #[\PHPUnit\Framework\Attributes\DataProvider('populatedRecordsProvider')]
    public function testPopulatedRecords(
        string $path,
        string $expectedMessage,
        bool $regExMatch = false
    ): void {
        $this->assertPageContent($expectedMessage, $path, regExMatch: $regExMatch);
    }

    /**
     * Data provider for testSearch().
     *
     * @return Generator<string, array>
     */
    public static function searchProvider(): Generator
    {
        $expectedTitleResults = 'Show credits Series test series 1 test series 2 (edited) '
            . 'Series Alternate Titles test alternate series title '
            . 'Items test item '
            . 'Item Alternate Titles test alternate title';
        $expectedTitleResultsWithCredits = 'Show credits Series test series 1 test series 2 (edited) '
            . 'Series Alternate Titles test alternate series title '
            . 'Items test item test-last, test-first, extra '
            . 'Item Alternate Titles test alternate title test-last, test-first, extra';
        yield 'title' => ['Title', 'test', $expectedTitleResults, $expectedTitleResultsWithCredits];
        yield 'bad title' => ['Title', 'xyzzy', 'No results found for xyzzy.'];
        $expectedPeopleResults = 'People last, test-second-edited lastname, test-third test-last, test-first, extra';
        yield 'person' => ['Person', 'test', $expectedPeopleResults];
        yield 'bad person' => ['Person', 'xyzzy', 'No results found for xyzzy.'];
        $extendedKeywordResults = ' Categories second test category (edited) test category '
            . $expectedPeopleResults
            . ' Subjects/Tags test tag test tag 2 (edited)';
        yield 'keyword' => [
            'Keyword',
            'test',
            $expectedTitleResults . $extendedKeywordResults,
            str_replace( // add separator in front of credits for this view:
                'test-last, test-first, extra',
                '/ test-last, test-first, extra',
                $expectedTitleResultsWithCredits
            ) . $extendedKeywordResults,
        ];
        yield 'bad keyword' => ['Keyword', 'xyzzy', 'No results found for xyzzy.'];
        yield 'isbn-10' => ['ISBN', '0123456789', 'ISBNs test item (0123456789 / 9780123456786)'];
        yield 'isbn-13' => ['ISBN', '9780123456786', 'ISBNs test item (0123456789 / 9780123456786)'];
        yield 'bad isbn' => ['ISBN', 'bad', 'No results found for bad.'];
    }

    /**
     * Test search functionality.
     *
     * @param string $type                Search type
     * @param string $query               Search query
     * @param string $expected            Expected results
     * @param string $expectedWithCredits Expected results with credits enabled (null to skip credit check)
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testLinkCreation')]
    #[\PHPUnit\Framework\Attributes\DataProvider('searchProvider')]
    public function testSearch(
        string $type,
        string $query,
        string $expected,
        ?string $expectedWithCredits = null
    ): void {
        [$safeQuery, $safeType] = [urlencode($query), urlencode($type)];
        $page = $this->goToPage("/Search?SearchQuery=$safeQuery&SearchType=$safeType");
        $this->assertEquals($expected, $this->findCssAndGetText($page, '.content'));
        if ($expectedWithCredits) {
            $this->clickCss($page, '.creator-toggle');
            $this->waitForPageLoad($page);
            $this->assertEquals($expectedWithCredits, $this->findCssAndGetText($page, '.content'));
        }
    }

    /**
     * Data provider for testPersonFullText().
     *
     * @return Generator<string, array>
     */
    public static function personFullTextProvider(): Generator
    {
        yield 'credit full text' => [1, 'test item'];
        yield 'cited full text' => [2, 'test item'];
        yield 'no full text' => [3, null];
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
        yield 'series by material type' => ['by material type', 'T test material', 0];
        yield 'series by publisher' => ['by publisher', 'T test publisher test publisher 2 (edited)', 0];
        yield 'series with comments' => ['with comments', 'T test series 1', 0];
        yield 'recently added series' => [
            'recently added',
            'Viewing page 1 of 1 test series 2 (edited) test series 1 First | Previous | 1 | Next | Last',
            0,
        ];
        yield 'people by name' => [
            'by name',
            'L T last, test-second-edited lastname, test-third T Back to Top ↑ test-last, test-first, extra',
            1,
        ];
        yield 'people with biographical notes' => ['with biographical notes', 'L last, test-second-edited', 1];
        yield 'recently added people' => [
            'recently added',
            'Viewing page 1 of 1 lastname, test-third last, test-second-edited test-last, test-first, extra '
            . 'First | Previous | 1 | Next | Last',
            1,
        ];
        yield 'items by name' => [
            'by name',
            'E T example article 1 example article 2 example issue 1 example issue 2 T Back to Top ↑ test item',
            2,
        ];
        yield 'items by platform' => ['by platform', 'T test platform test platform 2 (edited)', 2];
        yield 'items by subject/tag' => ['by subject/tag', 'T test tag test tag 2 (edited)', 2];
        yield 'items by year' => ['by year', '1952 test item (test note)', 2];
        yield 'items with full text' => ['with full text', '/.*test series 1 test item \\(1952\\)$/', 2, true];
        yield 'items with reviews' => ['with reviews', 'test series 1 test item', 2];
        yield 'recently added items' => [
            'recently added',
            'Viewing page 1 of 1 example article 2 example article 1 example issue 2 example issue 1 test item'
            . ' First | Previous | 1 | Next | Last',
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
        yield 'recent reviews' => ['Browse Recent Reviews', '/.*test item user.*test series 1 user.*/', null, true];
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
    #[\PHPUnit\Framework\Attributes\Depends('testSetAttributes')]
    #[\PHPUnit\Framework\Attributes\Depends('testSetFullTextAttributes')]
    #[\PHPUnit\Framework\Attributes\Depends('testReviewApproval')]
    public function testPopulatedDatabase(
        string $linkText,
        string $expectedMessage,
        ?int $containerIndex = null,
        bool $regExMatch = false
    ): void {
        $this->assertPageContent($expectedMessage, '', $linkText, $containerIndex, $regExMatch);
    }

    /**
     * Data provider for testSeriesCheckPage.
     *
     * @return Generator<string, array>
     */
    public static function seriesCheckPageProvider(): Generator
    {
        yield 'series 1' => [
            1,
            'Missing Credits '
            . 'None. '
            . 'Unspecified Creators '
            . 'None. '
            . 'Missing Dates '
            . 'None. '
            . 'Statistics '
            . 'Series contains dates from 1952. '
            . 'Series contains 1 total items representing 1 different positions. '
            . 'Series does not use volume numbering. '
            . '1 item(s) numbered from 0 to 0.',
        ];
        yield 'series 2' => [
            2,
            'Missing Credits '
            . 'example article 1, [v. 1, no. 1], [v. 1, no. 2] '
            . 'Unspecified Creators '
            . 'example article 1, [v. 1, no. 1], [v. 1, no. 2] '
            . 'Missing Dates '
            . '[v. 1, no. 1], [v. 1, no. 2] '
            . 'Statistics '
            . 'No date information. '
            . 'Series contains 2 total items representing 2 different positions. '
            . 'Series contains volume numbers from 1 to 1. '
            . 'Volume 1 '
            . '2 item(s) numbered from 1 to 2.',
        ];
    }

    /**
     * Test the "check series" page.
     *
     * @param int    $seriesId Series to check
     * @param string $expected Expected page content
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('seriesCheckPageProvider')]
    #[\PHPUnit\Framework\Attributes\Depends('testBuildingArticles')]
    public function testSeriesCheckPage(int $seriesId, string $expected): void
    {
        $page = $this->goToPage('/Series/' . $seriesId . '/Check');
        $this->assertSame($expected, $this->findCssAndGetText($page, '.content'));
    }
}
