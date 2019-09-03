<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 27.10.2017
 * Time: 23:04
 */

namespace Jinya\Form\Install;

use Jinya\Validator\Constraints as InstallAssert;
use Symfony\Component\Validator\Constraints as Assert;

class SetupData
{
    /**
     * @Assert\NotBlank(message="install.database.host.not_blank")
     * @var string
     */
    private $databaseHost;

    /**
     * @InstallAssert\Port(message="install.database.port.invalid")
     * @var int
     */
    private $databasePort = 3306;

    /**
     * @Assert\NotBlank(message="install.database.user.not_blank")
     * @var string
     */
    private $databaseUser;

    /**
     * @Assert\NotBlank(message="install.database.password.not_blank")
     * @var string
     */
    private $databasePassword;

    /**
     * @Assert\NotBlank(message="install.database.name.not_blank")
     * @var string
     */
    private $databaseName;

    /**
     * @Assert\NotBlank(message="install.mailer.transport.not_blank")
     * @var string
     */
    private $mailerTransport;

    /**
     * @Assert\NotBlank(message="install.mailer.host.not_blank")
     * @var string
     */
    private $mailerHost;

    /**
     * @InstallAssert\Port(message="install.mailer.port.invalid")
     * @var int
     */
    private $mailerPort = 25;

    /**
     * @Assert\NotBlank(message="install.mailer.user.not_blank")
     * @var string
     */
    private $mailerUser;

    /**
     * @Assert\NotBlank(message="install.mailer.password.not_blank")
     * @var string
     */
    private $mailerPassword;

    /**
     * @Assert\NotBlank(message="install.mailer.sender.not_blank")
     * @var string
     */
    private $mailerSender;

    /**
     * @var string
     */
    private $environment = 'prod';

    /**
     * @return string
     */
    public function getEnvironment(): ?string
    {
        return $this->environment;
    }

    /**
     * @param string $environment
     */
    public function setEnvironment(?string $environment): void
    {
        $this->environment = $environment;
    }

    /**
     * @return string
     */
    public function getMailerTransport(): ?string
    {
        return $this->mailerTransport;
    }

    /**
     * @param string $mailerTransport
     */
    public function setMailerTransport(string $mailerTransport): void
    {
        $this->mailerTransport = $mailerTransport;
    }

    /**
     * @return string
     */
    public function getMailerHost(): ?string
    {
        return $this->mailerHost;
    }

    /**
     * @param string $mailerHost
     */
    public function setMailerHost(string $mailerHost): void
    {
        $this->mailerHost = $mailerHost;
    }

    /**
     * @return string
     */
    public function getMailerUser(): ?string
    {
        return $this->mailerUser;
    }

    /**
     * @param string $mailerUser
     */
    public function setMailerUser(string $mailerUser): void
    {
        $this->mailerUser = $mailerUser;
    }

    /**
     * @return string
     */
    public function getMailerPassword(): ?string
    {
        return $this->mailerPassword;
    }

    /**
     * @param string $mailerPassword
     */
    public function setMailerPassword(string $mailerPassword): void
    {
        $this->mailerPassword = $mailerPassword;
    }

    /**
     * @return string
     */
    public function getDatabaseHost(): ?string
    {
        return $this->databaseHost;
    }

    /**
     * @param string $databaseHost
     */
    public function setDatabaseHost(string $databaseHost): void
    {
        $this->databaseHost = $databaseHost;
    }

    /**
     * @return int
     */
    public function getDatabasePort(): ?int
    {
        return $this->databasePort;
    }

    /**
     * @param int $databasePort
     */
    public function setDatabasePort(int $databasePort): void
    {
        $this->databasePort = $databasePort;
    }

    /**
     * @return string
     */
    public function getDatabaseUser(): ?string
    {
        return $this->databaseUser;
    }

    /**
     * @param string $databaseUser
     */
    public function setDatabaseUser(string $databaseUser): void
    {
        $this->databaseUser = $databaseUser;
    }

    /**
     * @return string
     */
    public function getDatabasePassword(): ?string
    {
        return $this->databasePassword;
    }

    /**
     * @param string $databasePassword
     */
    public function setDatabasePassword(string $databasePassword): void
    {
        $this->databasePassword = $databasePassword;
    }

    /**
     * @return string
     */
    public function getDatabaseName(): ?string
    {
        return $this->databaseName;
    }

    /**
     * @param string $databaseName
     */
    public function setDatabaseName(string $databaseName): void
    {
        $this->databaseName = $databaseName;
    }

    /**
     * @return int
     */
    public function getMailerPort(): ?int
    {
        return $this->mailerPort;
    }

    /**
     * @param int $mailerPort
     */
    public function setMailerPort(int $mailerPort): void
    {
        $this->mailerPort = $mailerPort;
    }

    /**
     * @return string
     */
    public function getMailerSender(): ?string
    {
        return $this->mailerSender;
    }

    /**
     * @param string $mailerSender
     */
    public function setMailerSender(string $mailerSender): void
    {
        $this->mailerSender = $mailerSender;
    }
}
