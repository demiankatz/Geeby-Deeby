<?php

/**
 * File controller
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
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Controller;

use GeebyDeeby\Db\Service\FileService;

use function count;

/**
 * File controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class FileController extends AbstractBase
{
    /**
     * File list
     *
     * @return mixed
     */
    public function listAction()
    {
        // Get file group configuration:
        $config = $this->serviceLocator->get('config');
        $groups = $config['geeby-deeby']['file_groups'] ?? [];

        // Initialize values:
        $files = $excludes = [];
        $service = $this->getDbService(FileService::class);

        // Build custom groups:
        foreach ($groups as $name => $includes) {
            $files[$name] = $service->getFilesByType($includes);
            if (count($files[$name]) == 0) {
                unset($files[$name]);
            }
            $excludes = array_merge($includes, $excludes);
        }

        // Build standard groups:
        $list = $service->getFilesByType(null, empty($excludes) ? null : $excludes);
        foreach ($list as $current) {
            if (!isset($files[$current['File_Type']])) {
                $files[$current['File_Type']] = [];
            }
            $files[$current['File_Type']][] = $current;
        }

        // Sort by array key:
        ksort($files);

        // Send back the details:
        return $this->createViewModel(['files' => $files]);
    }
}
