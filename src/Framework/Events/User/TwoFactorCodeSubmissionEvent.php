<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 25.08.18
 * Time: 01:03
 */

namespace Jinya\Framework\Events\User;

use Symfony\Contracts\EventDispatcher\Event;

class TwoFactorCodeSubmissionEvent extends Event
{
    public const PRE_CODE_SUBMISSION = 'TwoFactorCodePreSubmission';

    public const POST_CODE_SUBMISSION = 'TwoFactorCodePostSubmission';

    /** @var string */
    private string $username;

    /** @var string */
    private string $twoFactorCode;

    /** @var bool */
    private bool $sent = false;

    /**
     * TwoFactorCodeSubmissionEvent constructor.
     */
    public function __construct(string $username, string $twoFactorCode)
    {
        $this->username = $username;
        $this->twoFactorCode = $twoFactorCode;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getTwoFactorCode(): string
    {
        return $this->twoFactorCode;
    }

    public function isSent(): bool
    {
        return $this->sent;
    }

    public function setSent(bool $sent): void
    {
        $this->sent = $sent;
    }
}
