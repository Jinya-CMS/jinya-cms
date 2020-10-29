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
     */
    public function setAndSendTwoFactorCode(string $username): void;

    /**
     * Adds a new device code to the user
     */
    public function addKnownDevice(string $username): string;

    /**
     * Deletes the given known device
     */
    public function deleteKnownDevice(string $username, string $deviceCode): void;

    /**
     * Gets all known devices for the given user
     *
     * @return KnownDevice[]
     */
    public function getKnownDevices(string $username): array;
}
