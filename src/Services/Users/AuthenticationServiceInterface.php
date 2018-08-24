<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 25.08.18
 * Time: 01:07
 */

namespace Jinya\Services\Users;

use Jinya\Entity\Authentication\KnownDevice;

interface AuthenticationServiceInterface
{
    /**
     * Sets the two factor code and sends the verification mail
     *
     * @param string $username
     */
    public function setAndSendTwoFactorCode(string $username): void;

    /**
     * Adds a new device code to the user
     *
     * @param string $username
     * @return string
     */
    public function addKnownDevice(string $username): string;

    /**
     * Deletes the given known device
     *
     * @param string $username
     * @param string $deviceCode
     */
    public function deleteKnownDevice(string $username, string $deviceCode): void;

    /**
     * Gets all known devices for the given user
     *
     * @param string $username
     * @return KnownDevice[]
     */
    public function getKnownDevices(string $username): array;
}
