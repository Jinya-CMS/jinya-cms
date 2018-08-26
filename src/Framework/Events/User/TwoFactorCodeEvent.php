<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 25.08.18
 * Time: 00:57
 */

namespace Jinya\Framework\Events\User;

use Symfony\Component\EventDispatcher\Event;

class TwoFactorCodeEvent extends Event
{
    public const PRE_CODE_GENERATION = 'TwoFactorCodePreGeneration';

    public const POST_CODE_GENERATION = 'TwoFactorCodePostGeneration';

    /** @var string */
    private $username;

    /** @var string */
    private $twoFactorCode;

    /**
     * TwoFactorCodeEvent constructor.
     * @param string $username
     */
    public function __construct(string $username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
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

    /**
     * @param string $twoFactorCode
     */
    public function setTwoFactorCode(string $twoFactorCode): void
    {
        $this->twoFactorCode = $twoFactorCode;
    }
}
