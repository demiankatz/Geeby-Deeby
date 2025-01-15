<?php

/**
 * Class for sending emails
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
 * @package  Email
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby;

use Symfony\Component\Mailer;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * Class for sending emails
 *
 * @category GeebyDeeby
 * @package  Email
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EmailService
{
    /**
     * Mail transport
     *
     * @var Mailer\MailerInterface
     */
    protected $transport;

    /**
     * Constructor
     *
     * @param string $transportConfig Transport configuration
     */
    public function __construct($transportConfig = 'sendmail://default')
    {
        $this->transport = new Mailer\Mailer(Mailer\Transport::fromDsn($transportConfig));
    }

    /**
     * Send an email. Throws an exception if something goes wrong.
     *
     * @param string $recipient Recipient email address
     * @param string $subject   Subject line
     * @param string $body      Message body
     * @param string $sender    Sender email address
     *
     * @return void
     * @throws \Exception
     */
    public function send($recipient, $subject, $body, $sender)
    {
        $message = new Email();
        $message->addFrom(new Address($sender));
        $message->addTo(new Address($recipient));
        $message->subject($subject);
        $message->text($body);
        $this->transport->send($message);
    }
}
