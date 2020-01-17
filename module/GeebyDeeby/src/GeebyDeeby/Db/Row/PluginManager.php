<?php
/**
 * Database row plugin manager
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2019.
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
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\Db\Row;

/**
 * Database row plugin manager
 *
 * @category GeebyDeeby
 * @package  Db_Row
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
        'authority' => 'GeebyDeeby\Db\Row\Authority',
        'category' => 'GeebyDeeby\Db\Row\Category',
        'citation' => 'GeebyDeeby\Db\Row\Citation',
        'city' => 'GeebyDeeby\Db\Row\City',
        'collections' => 'GeebyDeeby\Db\Row\Collections',
        'country' => 'GeebyDeeby\Db\Row\Country',
        'edition' => 'GeebyDeeby\Db\Row\Edition',
        'editionsattribute' => 'GeebyDeeby\Db\Row\EditionsAttribute',
        'editionsattributesvalues' => 'GeebyDeeby\Db\Row\EditionsAttributesValues',
        'editionscredits' => 'GeebyDeeby\Db\Row\EditionsCredits',
        'editionsfulltext' => 'GeebyDeeby\Db\Row\EditionsFullText',
        'editionsimages' => 'GeebyDeeby\Db\Row\EditionsImages',
        'editionsisbns' => 'GeebyDeeby\Db\Row\EditionsISBNs',
        'editionsoclcnumbers' => 'GeebyDeeby\Db\Row\EditionsOCLCNumbers',
        'editionsplatforms' => 'GeebyDeeby\Db\Row\EditionsPlatforms',
        'editionsproductcodes' => 'GeebyDeeby\Db\Row\EditionsProductCodes',
        'editionsreleasedates' => 'GeebyDeeby\Db\Row\EditionsReleaseDates',
        'faqs' => 'GeebyDeeby\Db\Row\FAQs',
        'file' => 'GeebyDeeby\Db\Row\File',
        'filetype' => 'GeebyDeeby\Db\Row\FileType',
        'fulltextsource' => 'GeebyDeeby\Db\Row\FullTextSource',
        'item' => 'GeebyDeeby\Db\Row\Item',
        'itemsadaptations' => 'GeebyDeeby\Db\Row\ItemsAdaptations',
        'itemsalttitles' => 'GeebyDeeby\Db\Row\ItemsAltTitles',
        'itemsbibliography' => 'GeebyDeeby\Db\Row\ItemsBibliography',
        'itemscreators' => 'GeebyDeeby\Db\Row\ItemsCreators',
        'itemscreatorscitations' => 'GeebyDeeby\Db\Row\ItemsCreatorsCitations',
        'itemscredits' => 'GeebyDeeby\Db\Row\ItemsCredits',
        'itemsdescriptions' => 'GeebyDeeby\Db\Row\ItemsDescriptions',
        'itemsfiles' => 'GeebyDeeby\Db\Row\ItemsFiles',
        'itemsimages' => 'GeebyDeeby\Db\Row\ItemsImages',
        'itemsincollections' => 'GeebyDeeby\Db\Row\ItemsInCollections',
        'itemsinseries' => 'GeebyDeeby\Db\Row\ItemsInSeries',
        'itemsisbns' => 'GeebyDeeby\Db\Row\ItemsISBNs',
        'itemslinks' => 'GeebyDeeby\Db\Row\ItemsLinks',
        'itemsplatforms' => 'GeebyDeeby\Db\Row\ItemsPlatforms',
        'itemsproductcodes' => 'GeebyDeeby\Db\Row\ItemsProductCodes',
        'itemsrelationship' => 'GeebyDeeby\Db\Row\ItemsRelationship',
        'itemsrelationshipsvalues' => 'GeebyDeeby\Db\Row\ItemsRelationshipsValues',
        'itemsreleasedates' => 'GeebyDeeby\Db\Row\ItemsReleaseDates',
        'itemsreviews' => 'GeebyDeeby\Db\Row\ItemsReviews',
        'itemstags' => 'GeebyDeeby\Db\Row\ItemsTags',
        'itemstranslations' => 'GeebyDeeby\Db\Row\ItemsTranslations',
        'language' => 'GeebyDeeby\Db\Row\Language',
        'link' => 'GeebyDeeby\Db\Row\Link',
        'linktype' => 'GeebyDeeby\Db\Row\LinkType',
        'materialtype' => 'GeebyDeeby\Db\Row\MaterialType',
        'note' => 'GeebyDeeby\Db\Row\Note',
        'peoplebibliography' => 'GeebyDeeby\Db\Row\PeopleBibliography',
        'peoplefiles' => 'GeebyDeeby\Db\Row\PeopleFiles',
        'peoplelinks' => 'GeebyDeeby\Db\Row\PeopleLinks',
        'peopleuris' => 'GeebyDeeby\Db\Row\PeopleURIs',
        'person' => 'GeebyDeeby\Db\Row\Person',
        'platform' => 'GeebyDeeby\Db\Row\Platform',
        'predicate' => 'GeebyDeeby\Db\Row\Predicate',
        'pseudonyms' => 'GeebyDeeby\Db\Row\Pseudonyms',
        'publisher' => 'GeebyDeeby\Db\Row\Publisher',
        'publishersaddresses' => 'GeebyDeeby\Db\Row\PublishersAddresses',
        'publishersimprints' => 'GeebyDeeby\Db\Row\PublishersImprints',
        'recentreviews' => 'GeebyDeeby\Db\Row\RecentReviews',
        'role' => 'GeebyDeeby\Db\Row\Role',
        'series' => 'GeebyDeeby\Db\Row\Series',
        'seriesalttitles' => 'GeebyDeeby\Db\Row\SeriesAltTitles',
        'seriesattribute' => 'GeebyDeeby\Db\Row\SeriesAttribute',
        'seriesattributesvalues' => 'GeebyDeeby\Db\Row\SeriesAttributesValues',
        'seriesbibliography' => 'GeebyDeeby\Db\Row\SeriesBibliography',
        'seriescategories' => 'GeebyDeeby\Db\Row\SeriesCategories',
        'seriesreviews' => 'GeebyDeeby\Db\Row\SeriesReviews',
        'seriesfiles' => 'GeebyDeeby\Db\Row\SeriesFiles',
        'serieslinks' => 'GeebyDeeby\Db\Row\SeriesLinks',
        'seriesmaterialtypes' => 'GeebyDeeby\Db\Row\SeriesMaterialTypes',
        'seriespublishers' => 'GeebyDeeby\Db\Row\SeriesPublishers',
        'seriesrelationship' => 'GeebyDeeby\Db\Row\SeriesRelationship',
        'seriesrelationshipsvalues' => 'GeebyDeeby\Db\Row\SeriesRelationshipsValues',
        'seriestranslations' => 'GeebyDeeby\Db\Row\SeriesTranslations',
        'tag' => 'GeebyDeeby\Db\Row\Tag',
        'tagsattribute' => 'GeebyDeeby\Db\Row\TagsAttribute',
        'tagsattributesvalues' => 'GeebyDeeby\Db\Row\TagsAttributesValues',
        'tagsrelationship' => 'GeebyDeeby\Db\Row\TagsRelationship',
        'tagsrelationshipsvalues' => 'GeebyDeeby\Db\Row\TagsRelationshipsValues',
        'tagsuris' => 'GeebyDeeby\Db\Row\TagsURIs',
        'tagtype' => 'GeebyDeeby\Db\Row\TagType',
        'user' => 'GeebyDeeby\Db\Row\User',
        'usergroup' => 'GeebyDeeby\Db\Row\UserGroup',
    ];

    /**
     * Constructor
     *
     * Make sure Row gateways are properly initialized.
     *
     * @param mixed                configOrContainerInstance Config or container (for backward compatibility)
     * @param null|ConfigInterface                                                                            $v3config Configuration settings (optional)
     */
    public function __construct($configOrContainerInstance = null, array $v3config = [])
    {
        $this->addAbstractFactory('GeebyDeeby\Db\Row\AbstractFactory');
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
        return 'Zend\Db\RowGateway\RowGateway';
    }
}
