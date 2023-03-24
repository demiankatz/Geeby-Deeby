<?php

/**
 * Signup controller
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
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Controller;

use Laminas\Crypt\Password\Bcrypt;

/**
 * Signup controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SignupController extends AbstractBase
{
    /**
     * Signup action
     *
     * @return mixed
     */
    public function indexAction()
    {
        $defaultReason = 'I want to submit spam to the site.';
        $view = $this->createViewModel(
            ['reason' => $this->params()->fromPost('Reason', $defaultReason)]
        );
        if (null !== $this->params()->fromPost('submit')) {
            $view->user = $this->params()->fromPost('Username');
            $view->fullname = $this->params()->fromPost('Fullname');
            $view->address = $this->params()->fromPost('Address');
            $password1 = $this->params()->fromPost('Password1');
            $password2 = $this->params()->fromPost('Password2');
            if ($view->user == '' || $view->fullname == '' || $password1 == '') {
                $view->error = 'Please fill out all required fields.';
            } elseif (!preg_match('/^[0-9a-zA-Z-_]+$/', $view->user)) {
                $view->error = 'Username must consist of letters, '
                    . 'numbers, dashes and underscores.';
            } elseif (
                $password1 != $password2
                || strpos($view->address, '://') !== false // block spam addresses
            ) {
                $view->error = 'Your passwords did not match. Please try again.';
            } else {
                $table = $this->getDbTable('user');
                $exists = $table->select(['Username' => $view->user]);
                if (count($exists) > 0) {
                    $view->error = 'The username you selected is already in use.';
                } else {
                    $bcrypt = new Bcrypt();
                    $table->insert(
                        [
                            'Username' => $view->user,
                            'Password_Hash' => $bcrypt->create($password1),
                            'Name' => $view->fullname,
                            'Address' => $view->address,
                            'Join_Reason' => $view->reason,
                            'Person_ID' => 0,
                        ]
                    );
                    $view->setTemplate('geeby-deeby/signup/success');
                }
            }
        }
        return $view;
    }
}
