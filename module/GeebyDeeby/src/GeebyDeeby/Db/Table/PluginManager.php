<?php
/**
 * Database table plugin manager
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
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\Db\Table;

/**
 * Database table plugin manager
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PluginManager extends \GeebyDeeby\ServiceManager\AbstractPluginManager
{
    /**
     * Default plugin aliases.
     *
     * @var array
     */
    protected $aliases = [
        'authority' => 'GeebyDeeby\Db\Table\Authority',
        'category' => 'GeebyDeeby\Db\Table\Category',
        'citation' => 'GeebyDeeby\Db\Table\Citation',
        'citiesuris' => 'GeebyDeeby\Db\Table\CitiesURIs',
        'city' => 'GeebyDeeby\Db\Table\City',
        'collections' => 'GeebyDeeby\Db\Table\Collections',
        'countriesuris' => 'GeebyDeeby\Db\Table\CountriesURIs',
        'country' => 'GeebyDeeby\Db\Table\Country',
        'edition' => 'GeebyDeeby\Db\Table\Edition',
        'editionsattribute' => 'GeebyDeeby\Db\Table\EditionsAttribute',
        'editionsattributesvalues' => 'GeebyDeeby\Db\Table\EditionsAttributesValues',
        'editionscredits' => 'GeebyDeeby\Db\Table\EditionsCredits',
        'editionsfulltext' => 'GeebyDeeby\Db\Table\EditionsFullText',
        'editionsimages' => 'GeebyDeeby\Db\Table\EditionsImages',
        'editionsisbns' => 'GeebyDeeby\Db\Table\EditionsISBNs',
        'editionsoclcnumbers' => 'GeebyDeeby\Db\Table\EditionsOCLCNumbers',
        'editionsplatforms' => 'GeebyDeeby\Db\Table\EditionsPlatforms',
        'editionsproductcodes' => 'GeebyDeeby\Db\Table\EditionsProductCodes',
        'editionsreleasedates' => 'GeebyDeeby\Db\Table\EditionsReleaseDates',
        'faqs' => 'GeebyDeeby\Db\Table\FAQs',
        'file' => 'GeebyDeeby\Db\Table\File',
        'filetype' => 'GeebyDeeby\Db\Table\FileType',
        'fulltextsource' => 'GeebyDeeby\Db\Table\FullTextSource',
        'item' => 'GeebyDeeby\Db\Table\Item',
        'itemsadaptations' => 'GeebyDeeby\Db\Table\ItemsAdaptations',
        'itemsalttitles' => 'GeebyDeeby\Db\Table\ItemsAltTitles',
        'itemsattribute' => 'GeebyDeeby\Db\Table\ItemsAttribute',
        'itemsattributesvalues' => 'GeebyDeeby\Db\Table\ItemsAttributesValues',
        'itemsbibliography' => 'GeebyDeeby\Db\Table\ItemsBibliography',
        'itemscreators' => 'GeebyDeeby\Db\Table\ItemsCreators',
        'itemscreatorscitations' => 'GeebyDeeby\Db\Table\ItemsCreatorsCitations',
        'itemscredits' => 'GeebyDeeby\Db\Table\ItemsCredits',
        'itemsdescriptions' => 'GeebyDeeby\Db\Table\ItemsDescriptions',
        'itemsfiles' => 'GeebyDeeby\Db\Table\ItemsFiles',
        'itemsimages' => 'GeebyDeeby\Db\Table\ItemsImages',
        'itemsincollections' => 'GeebyDeeby\Db\Table\ItemsInCollections',
        'itemsinseries' => 'GeebyDeeby\Db\Table\ItemsInSeries',
        'itemsisbns' => 'GeebyDeeby\Db\Table\ItemsISBNs',
        'itemslinks' => 'GeebyDeeby\Db\Table\ItemsLinks',
        'itemsplatforms' => 'GeebyDeeby\Db\Table\ItemsPlatforms',
        'itemsproductcodes' => 'GeebyDeeby\Db\Table\ItemsProductCodes',
        'itemsrelationship' => 'GeebyDeeby\Db\Table\ItemsRelationship',
        'itemsrelationshipsvalues' => 'GeebyDeeby\Db\Table\ItemsRelationshipsValues',
        'itemsreleasedates' => 'GeebyDeeby\Db\Table\ItemsReleaseDates',
        'itemsreviews' => 'GeebyDeeby\Db\Table\ItemsReviews',
        'itemstags' => 'GeebyDeeby\Db\Table\ItemsTags',
        'itemstranslations' => 'GeebyDeeby\Db\Table\ItemsTranslations',
        'language' => 'GeebyDeeby\Db\Table\Language',
        'link' => 'GeebyDeeby\Db\Table\Link',
        'linktype' => 'GeebyDeeby\Db\Table\LinkType',
        'materialtype' => 'GeebyDeeby\Db\Table\MaterialType',
        'note' => 'GeebyDeeby\Db\Table\Note',
        'peoplebibliography' => 'GeebyDeeby\Db\Table\PeopleBibliography',
        'peoplefiles' => 'GeebyDeeby\Db\Table\PeopleFiles',
        'peoplelinks' => 'GeebyDeeby\Db\Table\PeopleLinks',
        'peopleuris' => 'GeebyDeeby\Db\Table\PeopleURIs',
        'person' => 'GeebyDeeby\Db\Table\Person',
        'platform' => 'GeebyDeeby\Db\Table\Platform',
        'predicate' => 'GeebyDeeby\Db\Table\Predicate',
        'pseudonyms' => 'GeebyDeeby\Db\Table\Pseudonyms',
        'publisher' => 'GeebyDeeby\Db\Table\Publisher',
        'publishersaddresses' => 'GeebyDeeby\Db\Table\PublishersAddresses',
        'publishersimprints' => 'GeebyDeeby\Db\Table\PublishersImprints',
        'recentreviews' => 'GeebyDeeby\Db\Table\RecentReviews',
        'role' => 'GeebyDeeby\Db\Table\Role',
        'series' => 'GeebyDeeby\Db\Table\Series',
        'seriesalttitles' => 'GeebyDeeby\Db\Table\SeriesAltTitles',
        'seriesattribute' => 'GeebyDeeby\Db\Table\SeriesAttribute',
        'seriesattributesvalues' => 'GeebyDeeby\Db\Table\SeriesAttributesValues',
        'seriesbibliography' => 'GeebyDeeby\Db\Table\SeriesBibliography',
        'seriescategories' => 'GeebyDeeby\Db\Table\SeriesCategories',
        'seriesreviews' => 'GeebyDeeby\Db\Table\SeriesReviews',
        'seriesfiles' => 'GeebyDeeby\Db\Table\SeriesFiles',
        'serieslinks' => 'GeebyDeeby\Db\Table\SeriesLinks',
        'seriesmaterialtypes' => 'GeebyDeeby\Db\Table\SeriesMaterialTypes',
        'seriespublishers' => 'GeebyDeeby\Db\Table\SeriesPublishers',
        'seriesrelationship' => 'GeebyDeeby\Db\Table\SeriesRelationship',
        'seriesrelationshipsvalues' =>
            'GeebyDeeby\Db\Table\SeriesRelationshipsValues',
        'seriestranslations' => 'GeebyDeeby\Db\Table\SeriesTranslations',
        'tag' => 'GeebyDeeby\Db\Table\Tag',
        'tagsattribute' => 'GeebyDeeby\Db\Table\TagsAttribute',
        'tagsattributesvalues' => 'GeebyDeeby\Db\Table\TagsAttributesValues',
        'tagsrelationship' => 'GeebyDeeby\Db\Table\TagsRelationship',
        'tagsrelationshipsvalues' => 'GeebyDeeby\Db\Table\TagsRelationshipsValues',
        'tagsuris' => 'GeebyDeeby\Db\Table\TagsURIs',
        'tagtype' => 'GeebyDeeby\Db\Table\TagType',
        'user' => 'GeebyDeeby\Db\Table\User',
        'usergroup' => 'GeebyDeeby\Db\Table\UserGroup',
    ];

    /**
     * Constructor
     *
     * Make sure table gateways are properly initialized.
     *
     * @param mixed                $configOrContainerInstance Config or container
     * (for backward compatibility)
     * @param null|ConfigInterface $v3config                  Configuration settings
     * (optional)
     */
    public function __construct($configOrContainerInstance = null,
        array $v3config = []
    ) {
        $this->addAbstractFactory('GeebyDeeby\Db\Table\AbstractFactory');
        parent::__construct($configOrContainerInstance, $v3config);
    }

    /**
     * Return the name of the base class or interface that plug-ins must conform
     * to.
     *
     * @return string
     */
    protected function getExpectedInterface()
    {
        return 'GeebyDeeby\Db\Table\Gateway';
    }
}
