<?php

/**
 * Edit note controller
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

use GeebyDeeby\Db\Service\NoteService;

/**
 * Edit note controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditNoteController extends AbstractBase
{
    /**
     * Display a list of notes
     *
     * @return mixed
     */
    public function listAction()
    {
        return $this->getGenericList(
            NoteService::class,
            'notes',
            'geeby-deeby/edit-note/render-notes'
        );
    }

    /**
     * Operate on a single note
     *
     * @return mixed
     */
    public function indexAction()
    {
        $assignMap = ['note' => 'setNote'];
        [$response] = $this->handleGenericItem(NoteService::class, $assignMap, 'note');
        return $response;
    }
}
