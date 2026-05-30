<?php

/**
 * Class to manage database persistence operations.
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
 * @link     https://vufind.org Main Site
 */

namespace GeebyDeeby\Db;

use Doctrine\ORM\EntityManager;
use GeebyDeeby\Db\Entity\AbstractEntity;
use GeebyDeeby\Db\Entity\CollectionEntityInterface;
use GeebyDeeby\Db\Entity\EntityInterface;
use GeebyDeeby\Db\Entity\ItemsReviewEntityInterface;
use GeebyDeeby\Db\Entity\SeriesReviewEntityInterface;
use GeebyDeeby\Db\Entity\UserEntityInterface;

/**
 * Class to manage database persistence operations.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org Main Site
 */
class PersistenceManager
{
    /**
     * These classes should be ignored by the logger.
     *
     * @var string[]
     */
    protected $doNotLog = [
        CollectionEntityInterface::class,
        ItemsReviewEntityInterface::class,
        SeriesReviewEntityInterface::class,
        UserEntityInterface::class,
    ];

    /**
     * Constructor
     *
     * @param EntityManager $entityManager Entity manager
     * @param ?int          $activeUserId  ID of current logged in user (or null if none)
     * @param ?string       $logDir        Directory to store logs in (null to disable logging)
     */
    public function __construct(
        protected EntityManager $entityManager,
        protected ?int $activeUserId,
        protected ?string $logDir
    ) {
    }

    /**
     * Persist an entity.
     *
     * @param EntityInterface $entity Entity to persist
     *
     * @return void
     */
    public function persistEntity(EntityInterface $entity): void
    {
        $this->logActivity($entity, 'PERSIST');
        if (!($entity instanceof AbstractEntity)) {
            throw new \Exception('Unexpected entity type');
        }
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * Delete an entity.
     *
     * @param EntityInterface $entity Entity to persist
     *
     * @return void
     */
    public function deleteEntity(EntityInterface $entity): void
    {
        $this->logActivity($entity, 'DELETE');
        if ($entity instanceof AbstractRowGateway) {
            $entity->delete();
        } elseif ($entity instanceof AbstractEntity) {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
        } else {
            throw new \Exception('Unexpected entity type');
        }
    }

    /**
     * Get log message.
     *
     * @param EntityInterface $entity Entity being logged
     * @param string          $extras Extra details to add to log message
     *
     * @return string
     */
    protected function getLogMessage(EntityInterface $entity, string $extras = ''): string
    {
        $keys = [];
        // Add key details if applicable:
        if ($entity instanceof EntityInterface) {
            $entityArray = $entity->toArray();
            foreach ($entity->getPrimaryKeyColumn() as $key) {
                $keys[] = $key . ':' . ($entityArray[$key] ?? 'NEW');
            }
        }
        $classNameParts = explode('\\', $entity::class);
        $entityType = array_pop($classNameParts);
        return trim(
            date('Y-m-d H:i:s') . ' ' . $entityType . ' ' . implode('; ', $keys) . ' ' . $extras
        ) . "\n";
    }

    /**
     * Log user activity if configured to do so.
     *
     * @param EntityInterface $entity Entity being logged
     * @param string          $extras Extra details to add to log message
     *
     * @return void
     */
    protected function logActivity(EntityInterface $entity, string $extras = ''): void
    {
        if ($this->activeUserId && $this->logDir) {
            foreach ($this->doNotLog as $noLogClass) {
                if ($entity instanceof $noLogClass) {
                    return;
                }
            }
            $filename = 'user-' . $this->activeUserId . '.log';
            $log = rtrim($this->logDir, '/') . "/{$filename}";
            $handle = fopen($log, 'a');
            if ($handle) {
                fwrite($handle, $this->getLogMessage($entity, $extras));
                fclose($handle);
            }
        }
    }
}
