<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 25.08.18
 * Time: 01:03
 */

namespace Jinya\Framework\Events\User;

use Symfony\Component\EventDispatcher\Event;

class TwoFactorCodeSubmissionEvent extends Event
{
    public const PRE_CODE_SUBMISSION = 'TwoFactorCodePreSubmission';

    public const POST_CODE_SUBMISSION = 'TwoFactorCodePostSubmission';

    /** @var string */
    private $username;

    /** @var string */
    private $twoFactorCode;

    /** @var boolean */
    private $sent = false;

    /**
     * TwoFactorCodeSubmissionEvent constructor.
     * @param string $username
     * @param string $twoFactorCode
     */
    public function __construct(string $username, string $twoFactorCode)
    {
        $this->username = $username;
        $this->twoFactorCode = $twoFactorCode;
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
    public function getTwoFactorCode(): string
    {
        return $this->twoFactorCode;
    }

    /**
     * @return bool
     */
    public function isSent(): bool
    {
        return $this->sent;
    }

    /**
     * @param bool $sent
     */
    public function setSent(bool $sent): void
    {
        $this->sent = $sent;
    }
}