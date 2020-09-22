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
     */
    private string $databaseHost;

    /**
     * @InstallAssert\Port(message="install.database.port.invalid")
     */
    private int $databasePort = 3306;

    /**
     * @Assert\NotBlank(message="install.database.user.not_blank")
     */
    private string $databaseUser;

    /**
     * @Assert\NotBlank(message="install.database.password.not_blank")
     */
    private string $databasePassword;

    /**
     * @Assert\NotBlank(message="install.database.name.not_blank")
     */
    private string $databaseName;

    /**
     * @Assert\NotBlank(message="install.mailer.transport.not_blank")
     */
    private string $mailerTransport;

    /**
     * @Assert\NotBlank(message="install.mailer.host.not_blank")
     */
    private string $mailerHost;

    /**
     * @InstallAssert\Port(message="install.mailer.port.invalid")
     */
    private int $mailerPort = 25;

    /**
     * @Assert\NotBlank(message="install.mailer.user.not_blank")
     */
    private string $mailerUser;

    /**
     * @Assert\NotBlank(message="install.mailer.password.not_blank")
     */
    private string $mailerPassword;

    /**
     * @Assert\NotBlank(message="install.mailer.sender.not_blank")
     */
    private string $mailerSender;

    private string $environment = 'prod';

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

    public function setMailerSender(string $mailerSender): void
    {
        $this->mailerSender = $mailerSender;
    }
}
