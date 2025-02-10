<?php

/**
 * Trait for adding activity logging to a row or table gateway.
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2020.
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

namespace GeebyDeeby\Db;

/**
 * Trait for adding activity logging to a row or table gateway.
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
trait ActivityLoggerTrait
{
    /**
     * Active user's ID
     *
     * @var string
     */
    protected static $activeUserId = null;

    /**
     * Directory path for logging
     *
     * @var string
     */
    protected static $logDir = null;

    /**
     * Should we disable logging for this class?
     *
     * @var bool
     */
    protected static $doNotLog = false;

    /**
     * Set directory for storing log files.
     *
     * @param string $user Active user ID
     * @param string $dir  Directory path
     *
     * @return void
     */
    public function activateLogging($user, $dir)
    {
        static::$activeUserId = $user;
        static::$logDir = $dir;
    }

    /**
     * Get log message.
     *
     * @param string $extras Extra details to add to log message.
     *
     * @return string
     */
    protected function getLogMessage($extras = '')
    {
        $keys = [];
        // Add key details if applicable:
        if ($this instanceof \Laminas\Db\RowGateway\RowGateway) {
            foreach ($this->primaryKeyColumn as $key) {
                try {
                    $keys[] = $key . ':' . $this->$key;
                } catch (\Throwable $e) {
                    // Exception means we're making a new row:
                    $keys[] = "$key:NEW";
                }
            }
        }
        return trim(
            date('Y-m-d H:i:s') . ' ' . (string)$this->table . ' '
            . implode('; ', $keys) . $extras
        ) . "\n";
    }

    /**
     * Log user activity if configured to do so.
     *
     * @param string $extras Extra details to add to log message.
     *
     * @return void
     */
    protected function logActivity($extras = '')
    {
        if (static::$activeUserId && static::$logDir && !static::$doNotLog) {
            $filename = 'user-' . static::$activeUserId . '.log';
            $log = rtrim(static::$logDir, '/') . "/{$filename}";
            $handle = fopen($log, 'a');
            if ($handle) {
                fwrite($handle, $this->getLogMessage($extras));
                fclose($handle);
            }
        }
    }
}
