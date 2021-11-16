<?php

namespace code\mailer;

use code\applications\ApiAppFactory;
use code\service\ServiceInterface;
use code\service\ServiceTypes;
use Symfony\Component\Mailer\Mailer as SymfonyMailer;
use Symfony\Component\Mailer\Transport;

class Mailer implements ServiceInterface, MailerInterface {

    private $mailer;
    private $settings;

    public function getSettings() {
        return $this->settings;
    }

    public function init() {

        $config = ApiAppFactory::getApp()->getService(ServiceTypes::CONFIGURATIONS);
        $this->settings = $config->get("env.smtp");
        $dsn = sprintf(
                '%s://%s:%s@%s:%s',
                $this->settings['type'],
                $this->settings['username'],
                $this->settings['password'],
                $this->settings['host'],
                $this->settings['port']
        );
        $this->mailer = new SymfonyMailer(Transport::fromDsn($dsn));
    }

}
