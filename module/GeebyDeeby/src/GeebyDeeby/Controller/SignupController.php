<?php

/**
 * Signup controller
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

use GeebyDeeby\Crypt\PasswordHasher;
use GeebyDeeby\Db\Service\UserService;

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
            if (!empty($view->address)) {
                $emailValidator = new \Laminas\Validator\EmailAddress();
                $validEmail = $emailValidator->isValid($view->address);
            } else {
                $validEmail = true;
            }
            $password1 = $this->params()->fromPost('Password1');
            $password2 = $this->params()->fromPost('Password2');
            if ($view->user == '' || $view->fullname == '' || $password1 == '') {
                $view->error = 'Please fill out all required fields.';
            } elseif (!preg_match('/^[0-9a-zA-Z-_]+$/', $view->user)) {
                $view->error = 'Username must consist of letters, '
                    . 'numbers, dashes and underscores.';
            } elseif (
                $password1 != $password2
                || str_contains($view->address, '://')   // block spam addresses
            ) {
                $view->error = 'Your passwords did not match. Please try again.';
            } elseif (!$validEmail) {
                $view->error = 'The email address you provided is invalid. Please try again.';
            } else {
                $service = $this->getDbService(UserService::class);
                $exists = $service->getByUsername($view->user);
                if ($exists) {
                    $view->error = 'The username you selected is already in use.';
                } else {
                    $hasher = new PasswordHasher();
                    $newUser = $service->createEntity()
                        ->setUsername($view->user)
                        ->setPasswordHash($hasher->create($password1))
                        ->setName($view->fullname)
                        ->setAddress($view->address)
                        ->setJoinReason($view->reason)
                        ->setPerson(null);
                    $service->persistEntity($newUser);
                    $view->setTemplate('geeby-deeby/signup/success');
                }
            }
        }
        return $view;
    }
}
