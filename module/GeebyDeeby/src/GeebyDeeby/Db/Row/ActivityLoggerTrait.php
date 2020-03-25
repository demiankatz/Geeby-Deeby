<?php
/**
 * Trait for adding activity logging to a row gateway.
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
namespace GeebyDeeby\Db\Row;

/**
 * Trait for adding activity logging to a row gateway.
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
     * @return string
     */
    protected function getLogMessage()
    {
        $keys = [];
        foreach ($this->primaryKeyColumn as $key) {
            $keys[] = $key . ':' . $this->$key;
        }
        return date('Y-m-d H:i:s') . ' ' . (string)$this->table . ' '
            . implode('; ', $keys) . "\n";
    }

    /**
     * Log user activity if configured to do so.
     *
     * @return void
     */
    protected function logActivity()
    {
        if (static::$activeUserId && static::$logDir) {
            $filename = 'user-' . static::$activeUserId . '.log';
            $log = rtrim(static::$logDir, '/') . "/{$filename}";
            $handle = fopen($log, 'a');
            if ($handle) {
                fputs($handle, $this->getLogMessage());
                fclose($handle);
            }
        }
    }
}
