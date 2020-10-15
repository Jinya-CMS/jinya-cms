<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 25.08.18
 * Time: 00:57
 */

namespace Jinya\Framework\Events\User;

use Symfony\Contracts\EventDispatcher\Event;

class TwoFactorCodeEvent extends Event
{
    public const PRE_CODE_GENERATION = 'TwoFactorCodePreGeneration';

    /** @var string */
    private string $username;

    /** @var string */
    private string $twoFactorCode = '';

    /**
     * TwoFactorCodeEvent constructor.
     */
    public function __construct(string $username)
    {
        $this->username = $username;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getTwoFactorCode(): ?string
    {
        return $this->twoFactorCode;
    }

    public function setTwoFactorCode(string $twoFactorCode): void
    {
        $this->twoFactorCode = $twoFactorCode;
    }
}
