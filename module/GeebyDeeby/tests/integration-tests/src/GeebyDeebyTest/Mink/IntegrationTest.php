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
        $page = $this->goToPage();
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
        foreach ($data as $selector => $value) {
            $expectedDisplay = $expectedDisplay === null ? $value : $expectedDisplay;
            $this->findCssAndSetValue($page, $selector, $value);
        }
        $this->clickCss($page, '.modal-body input[type="submit"]');
        $this->waitForPageLoad($page);
        $links = $page->findAll('css', "$listSelector a");
        $linkText = array_map(fn ($a) => $a->getText(), $links);
        $this->assertTrue(in_array($expectedDisplay, $linkText), "Link list should include '$expectedDisplay'");
        $this->assertCount($expectedLinkCount, $links);
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
     * Test adding details to an edition.
     *
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testPopulateData')]
    public function testEditionEditor(): void
    {
        $page = $this->goToPage('/edit/Edition/1');
        $this->logIn($page, 'admin');

        // Add a credit:
        $this->findCssAndSetValue($page, '#credit_person', '1');
        $this->findCssAndSetValue($page, '#credit_note', '1');
        $this->clickCss($page, '.active .edit_container input[type="submit"]');
        $this->assertEquals(
            'test person role: test-last, test-first, extra (test note)',
            $this->findCssAndGetText($page, '#credit_list table td', index: 2)
        );

        // Add a full-text link:
        $page->clickLink('Full Text Links');
        $this->findCssAndSetValue($page, '#Full_Text_URL', 'http://example.com/fulltext');
        $this->clickCss($page, '.active .edit_container input[type="submit"]');
        $this->assertEquals(
            'test full text source: http://example.com/fulltext '
            . 'Edit options for URL: http://example.com/fulltext '
            . 'Delete full text URL: http://example.com/fulltext',
            $this->findCssAndGetText($page, '#fulltext_list')
        );
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
    #[\PHPUnit\Framework\Attributes\Depends('testEditionEditor')]
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
}
