<?php

/**
 * Class to move Works into Issues within a Series.
 *
 * PHP version 8
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
 * along with this program; if not, see
 * <https://www.gnu.org/licenses/>.
 *
 * @category GeebyDeeby
 * @package  Ingest
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeebyLocal\Ingest;

use GeebyDeeby\Articles;
use GeebyDeeby\Db\Entity\EditionEntityInterface;
use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\Service\CollectionService;
use GeebyDeeby\Db\Service\DbServiceInterface;
use GeebyDeeby\Db\Service\EditionsFullTextService;
use GeebyDeeby\Db\Service\EditionsImageService;
use GeebyDeeby\Db\Service\EditionsIsbnService;
use GeebyDeeby\Db\Service\EditionsOclcNumberService;
use GeebyDeeby\Db\Service\EditionsPlatformService;
use GeebyDeeby\Db\Service\EditionsProductCodeService;
use GeebyDeeby\Db\Service\EditionsReleaseDateService;
use GeebyDeeby\Db\Service\ItemService;
use GeebyDeeby\Db\Service\PluginManager as DbServiceManager;
use GeebyDeebyLocal\Db\Service\EditionService;

use function count;

/**
 * Class to move Works into Issues within a Series.
 *
 * @category GeebyDeeby
 * @package  Ingest
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class IssueMaker
{
    // constant values drawn from dimenovels.org database:
    public const MATERIALTYPE_WORK = 1;
    public const MATERIALTYPE_ISSUE = 2;

    /**
     * Last message sent to writeln()
     *
     * @var string
     */
    protected $lastMessage = '';

    /**
     * Process a status message
     *
     * @param string $msg Message
     *
     * @return void
     */
    protected function writeln(string $msg)
    {
        $this->lastMessage = $msg;
    }

    /**
     * Get the last message
     *
     * @return string
     */
    public function getLastMessage()
    {
        return $this->lastMessage;
    }

    /**
     * Constructor
     *
     * @param DbServiceManager $services DB service plugin manager
     * @param Articles         $articles Articles helper
     */
    public function __construct(protected DbServiceManager $services, protected Articles $articles)
    {
    }

    /**
     * Make issues
     *
     * @param SeriesEntityInterface $seriesObj Series entity
     * @param string                $prefix    Title prefix for issues
     *
     * @return bool
     */
    public function makeIssues(SeriesEntityInterface $seriesObj, string $prefix): bool
    {
        $works = $this->getDbService(EditionService::class)
            ->getNumberedEditionsOfTypeFromSeries(self::MATERIALTYPE_WORK, $seriesObj);
        if (count($works) == 0) {
            $this->writeln('No eligible works.');
            return false;
        }
        foreach ($works as $currentEdition) {
            if (!$this->createIssueForWork($currentEdition, $prefix)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get a database service.
     *
     * @param class-string<T> $name Name of service to retrieve
     *
     * @template T
     *
     * @return T
     */
    protected function getDbService(string $name): DbServiceInterface
    {
        return $this->services->get($name);
    }

    /**
     * Create an Issue edition to wrap the provided Work edition.
     *
     * @param EditionEntityInterface $workEdition Edition entity
     * @param string                 $prefix      Title prefix for issues
     *
     * @return bool
     */
    public function createIssueForWork(EditionEntityInterface $workEdition, string $prefix): bool
    {
        if (!$this->workIsEligibleForConversion($workEdition)) {
            return false;
        }
        $issueItem = $this->createIssueItem($workEdition, $prefix);
        $issueEdition = $this->createIssueEdition($issueItem, $workEdition);
        return $this->transferEditionData($workEdition, $issueEdition);
    }

    /**
     * Validate a work edition before proceeding with issue conversion.
     *
     * @param EditionEntityInterface $workEdition Edition entity
     *
     * @return bool
     */
    protected function workIsEligibleForConversion(EditionEntityInterface $workEdition)
    {
        $editionService = $this->getDbService(EditionService::class);
        if ($editionService->hasEditionMatchingPositionAndType($workEdition, self::MATERIALTYPE_ISSUE)) {
            $this->writeln(
                'Duplicate issue found for edition #' . $workEdition->getId()
            );
            return false;
        }
        if ($workEdition->getVolume() > 0) {
            $this->writeln('TODO: add support for volumes > 0');
            return false;
        }
        if ($workEdition->getReplacementNumber() > 0) {
            $this->writeln('TODO: add support for replacement numbers > 0');
            return false;
        }
        if (
            $workEdition->getParentEdition()
            || !empty($workEdition->getPositionInParent())
            || !empty($workEdition->getExtentInParent())
        ) {
            $this->writeln(
                'Unexpected parent details in edition #' . $workEdition->getId()
            );
            return false;
        }
        return true;
    }

    /**
     * Create an Issue edition.
     *
     * @param ItemEntityInterface    $issueItem   Item entity for new Issue
     * @param EditionEntityInterface $workEdition Edition entity for existing Work
     *
     * @return EditionEntityInterface
     */
    protected function createIssueEdition(
        ItemEntityInterface $issueItem,
        EditionEntityInterface $workEdition
    ): EditionEntityInterface {
        $editionService = $this->getDbService(EditionService::class);
        $newEdition = $editionService->createEntity();
        $newEdition->setEditionName($workEdition->getEditionName())
            ->setItem($issueItem)
            ->setSeries($workEdition->getSeries())
            ->setVolume($workEdition->getVolume())
            ->setPosition($workEdition->getPosition())
            ->setReplacementNumber($workEdition->getReplacementNumber())
            ->setPreferredSeriesAlternateTitle($workEdition->getPreferredSeriesAlternateTitle())
            ->setLength($workEdition->getLength())
            ->setEndings($workEdition->getEndings())
            ->setDescription($workEdition->getDescription())
            ->setPreferredPublisher($workEdition->getPreferredPublisher());
        $editionService->persistEntity($newEdition);
        return $newEdition;
    }

    /**
     * Create an Issue item.
     *
     * @param EditionEntityInterface $workEdition Edition entity
     * @param string                 $prefix      Title prefix for issues
     *
     * @return ItemEntityInterface
     */
    protected function createIssueItem(EditionEntityInterface $workEdition, string $prefix): ItemEntityInterface
    {
        $name = $this->articles->articleAwareAppend($prefix, $workEdition->Position);
        $this->writeln('Creating issue: ' . $name);
        $itemService = $this->getDbService(ItemService::class);
        $item = $itemService->createEntity()->setItemName($name)->setMaterialType(self::MATERIALTYPE_ISSUE);
        $itemService->persistEntity($item);
        return $item;
    }

    /**
     * Transfer relevant data from work edition to issue edition
     *
     * @param EditionEntityInterface $workEdition  Edition entity for work
     * @param EditionEntityInterface $issueEdition Edition entity for issue
     *
     * @return bool
     */
    protected function transferEditionData(
        EditionEntityInterface $workEdition,
        EditionEntityInterface $issueEdition
    ): bool {
        // Attach work to issue and remove no-longer-relevant details
        $editionService = $this->getDbService(EditionService::class);
        $workEdition->setParentEdition($issueEdition)
            ->setPosition(0)
            ->setLength(null)
            ->setDescription(null);
        $editionService->persistEntity($workEdition);

        // Move relevant associations:
        $editionLinkServices = [
            [EditionsFullTextService::class, 'getFullTextForEdition'],
            [EditionsImageService::class, 'getImagesForEdition'],
            [EditionsIsbnService::class, 'getISBNsForEdition'],
            [EditionsOclcNumberService::class, 'getOCLCNumbersForEdition'],
            [EditionsProductCodeService::class, 'getProductCodesForEdition'],
        ];
        foreach ($editionLinkServices as $details) {
            [$serviceName, $method] = $details;
            $linkService = $this->getDbService($serviceName);
            foreach ($linkService->$method($workEdition->getId()) as $current) {
                $current->setEdition($issueEdition);
                $linkService->persistEntity($current);
            }
        }
        // Relocate platforms and dates (these don't update right in Laminas due to lack of primary key):
        $platformService = $this->getDbService(EditionsPlatformService::class);
        $platforms = $platformService->getPlatformsForEdition($workEdition->getId());
        foreach ($platforms as $platform) {
            $newLink = $platformService->createEntity()
                ->setEdition($issueEdition)
                ->setPlatform($platform->getPlatform());
            $platformService->persistEntity($newLink);
            $platformService->deleteEntity($platform);
        }
        $dateService = $this->getDbService(EditionsReleaseDateService::class);
        $dates = $dateService->getDatesForEdition($workEdition->getId());
        foreach ($dates as $date) {
            $newDate = $dateService->createEntity()
                ->setYear($date->getYear())
                ->setMonth($date->getMonth())
                ->setDay($date->getDay())
                ->setNote($date->getNote())
                ->setEdition($issueEdition);
            $dateService->persistEntity($newDate);
            $dateService->deleteEntity($date);
        }

        // Migrate collection data:
        $collectionService = $this->getDbService(CollectionService::class);
        foreach ($collectionService->getForItem($workEdition->getId()) as $collectionEntry) {
            $collectionEntry->setItem($issueEdition->getItem());
            $collectionService->persistEntity($collectionEntry);
        }
        return true;
    }
}
