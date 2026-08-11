<?php

/**
 * Database service for the Items_Translations table.
 *
 * PHP version 8
 *
 * Copyright (C) Demian Katz 2026.
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
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Db\Service;

use GeebyDeeby\Db\Entity\Edition;
use GeebyDeeby\Db\Entity\Item;
use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\ItemsTranslation;
use GeebyDeeby\Db\Entity\ItemsTranslationEntityInterface;
use GeebyDeeby\Db\Entity\Language;
use GeebyDeeby\Db\Entity\Series;

/**
 * Database service for the Items_Translations table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsTranslationService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return ItemsTranslationEntityInterface
     */
    public function createEntity(): ItemsTranslationEntityInterface
    {
        $entity = new ItemsTranslation();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Build the DQL query to look up translation relationships.
     *
     * @param bool   $includeLang Should we also load language information?
     * @param string $joinField   Name of ItemsTranslation field to join on
     * @param string $whereField  Name of ItemsTranslation field to select on
     *
     * @return string
     */
    protected function getTranslationDql(bool $includeLang, string $joinField, string $whereField): string
    {
        $fields = 'i.id AS Item_ID, i.itemName AS Item_Name';
        $extraJoins = $groupBy = '';
        if ($includeLang) {
            $fields .= ', l.id AS Language_ID, l.languageName AS Language_Name';
            $extraJoins .= 'INNER JOIN ' . Edition::class . ' e ON e.item=i.id '
                . 'INNER JOIN ' . Series::class . ' s ON e.series=s.id '
                . 'INNER JOIN ' . Language::class . ' l ON s.language=l.id ';
            $groupBy .= ' GROUP BY i.id ORDER BY i.itemName';
        }
        return "SELECT $fields FROM " . ItemsTranslation::class . ' it '
            . 'INNER JOIN ' . Item::class . ' i ON it.' . $joinField . '=i.id '
            . $extraJoins
            . 'WHERE it.' . $whereField . '=:item' . $groupBy;
    }

    /**
     * Get a list of items translated from the specified item.
     *
     * @param int  $itemID      Item ID
     * @param bool $includeLang Should we also load language information?
     *
     * @return array
     */
    public function getTranslatedFrom(int $itemID, bool $includeLang = false): array
    {
        $dql = $this->getTranslationDql($includeLang, 'translatedItem', 'sourceItem');
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('item', $itemID);
        return $query->getResult();
    }

    /**
     * Get a list of items translated into the specified item.
     *
     * @param int  $itemID      Item ID
     * @param bool $includeLang Should we also load language information?
     *
     * @return array
     */
    public function getTranslatedInto(int $itemID, bool $includeLang = false): array
    {
        $dql = $this->getTranslationDql($includeLang, 'sourceItem', 'translatedItem');
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('item', $itemID);
        return $query->getResult();
    }

    /**
     * Get a row matching the provided source item/translated item pair.
     *
     * @param int|ItemEntityInterface $source     Source item ID or entity
     * @param int|ItemEntityInterface $translated Translated item ID or entity
     *
     * @return ?ItemsTranslationEntityInterface
     */
    public function getBySourceItemAndTranslatedItem(
        int|ItemEntityInterface $source,
        int|ItemEntityInterface $translated
    ): ?ItemsTranslationEntityInterface {
        $dql = 'SELECT it FROM ' . ItemsTranslation::class
            . ' it WHERE it.sourceItem=:source AND it.translatedItem=:translated';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('source', $source instanceof ItemEntityInterface ? $source->getId() : $source);
        $query->setParameter(
            'translated',
            $translated instanceof ItemEntityInterface ? $translated->getId() : $translated
        );
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
