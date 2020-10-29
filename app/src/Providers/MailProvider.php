<?php
declare(strict_types=1);

namespace Harpya\IP\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;

class MailProvider implements ServiceProviderInterface
{
    public function register(DiInterface $di): void
    {
        $di->setShared('mailer', function () use ($di) {
            $generalConfig = $di->getShared('config');
            $mailConfig = $generalConfig->get('communication')->get('mail')->toArray();
            $transport = (new \Swift_SmtpTransport($mailConfig['host'], $mailConfig['port']))
          ->setUsername($mailConfig['username'])
          ->setPassword($mailConfig['password']);

            // Create the Mailer using your created Transport
            $mailer = new \Swift_Mailer($transport);

            return $mailer;
        });
    }
}
