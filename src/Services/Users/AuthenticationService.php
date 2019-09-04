<?php

/** @noinspection HtmlRequiredTitleElement */

/** @noinspection HtmlRequiredLangAttribute */

namespace Jinya\Services\Users;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Jinya\Entity\Artist\User;
use Jinya\Entity\Authentication\KnownDevice;
use Jinya\Framework\Events\User\TwoFactorCodeEvent;
use Jinya\Framework\Events\User\TwoFactorCodeSubmissionEvent;
use Swift_Mailer;
use Swift_Message;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AuthenticationService implements AuthenticationServiceInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var Swift_Mailer */
    private $swift;

    /** @var string */
    private $mailerSender;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var UserServiceInterface */
    private $userService;

    /**
     * AuthenticationService constructor.
     * @param EntityManagerInterface $entityManager
     * @param Swift_Mailer $swift
     * @param string $mailerSender
     * @param EventDispatcherInterface $eventDispatcher
     * @param UserServiceInterface $userService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        Swift_Mailer $swift,
        string $mailerSender,
        EventDispatcherInterface $eventDispatcher,
        UserServiceInterface $userService
    ) {
        $this->entityManager = $entityManager;
        $this->swift = $swift;
        $this->mailerSender = $mailerSender;
        $this->eventDispatcher = $eventDispatcher;
        $this->userService = $userService;
    }

    /**
     * Sets the two factor code and sends the verification mail
     *
     * @param string $username
     * @throws Exception
     */
    public function setAndSendTwoFactorCode(string $username): void
    {
        $pre = $this->eventDispatcher->dispatch(
            new TwoFactorCodeEvent($username),
            TwoFactorCodeEvent::PRE_CODE_GENERATION
        );
        $user = $this->userService->getUserByEmail($username);
        $code = '';

        if (empty($pre->getTwoFactorCode())) {
            for ($i = 0; $i < 6; ++$i) {
                $code .= random_int(0, 9);
            }
        } else {
            $code = $pre->getTwoFactorCode();
        }

        $user->setTwoFactorToken($code);
        $this->entityManager->flush();

        $submissionEvent = $this->eventDispatcher->dispatch(
            new TwoFactorCodeSubmissionEvent($username, $code),
            TwoFactorCodeSubmissionEvent::PRE_CODE_SUBMISSION
        );
        if (!$submissionEvent->isSent()) {
            /** @var Swift_Message $message */
            $message = $this->swift->createMessage();
            $message->addTo($user->getEmail());
            $message->setSubject('Your two factor code');
            $message->setBody($this->formatBody($user), 'text/html');
            $message->setFrom($this->mailerSender);
            $this->swift->send($message);
        }

        $this->eventDispatcher->dispatch(
            new TwoFactorCodeSubmissionEvent($username, $code),
            TwoFactorCodeSubmissionEvent::POST_CODE_SUBMISSION
        );
    }

    private function formatBody(User $user): string
    {
        $name = $user->getArtistName();
        $code = $user->getTwoFactorToken();

        return "<html>
<head></head>
<body style='font-family: -apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,\"Helvetica Neue\",Arial,sans-serif'>
    <table style='width: 100%; height: 100%;'>
    <tr>
        <td colspan='3' style='height: 15%;'></td>
    </tr>
    <tr>
    <td style='width: 35%;'></td>
    <td style='width: 30%;'>
        <p style='margin-top: 15%;'>
            Hello $name,<br /><br />
            you tried to login from a new device, please verify with this code that this was actually you.
        </p>
        <p style='text-align: center;'>
            <code style='background: #EFEFEF; padding: 6pt; text-align: center; font-size: 16pt; font-family: Consolas, monospace'>$code</code>
        </p>
        <p>
            Greetings,<br />
            Your Jinya Team
        </p>
    </td>
    <td style='width: 35%;'></td>
</tr>
</table>
</body>
</html>";
    }

    /**
     * Adds a new device code to the user
     *
     * @param string $username
     * @return string
     */
    public function addKnownDevice(string $username): string
    {
        $user = $this->userService->getUserByEmail($username);
        $knownDevice = new KnownDevice();
        $knownDevice->setUser($user);
        $knownDevice->setRemoteAddress($_SERVER['REMOTE_ADDR']);
        $knownDevice->setUserAgent($_SERVER['HTTP_USER_AGENT']);

        try {
            $code = bin2hex(random_bytes(20));
        } catch (Exception $exception) {
            $code = sha1((string)time());
        }

        $knownDevice->setKey($code);

        $this->entityManager->persist($knownDevice);
        $this->entityManager->flush();

        return $code;
    }

    /**
     * Gets all known devices for the given user
     *
     * @param string $username
     * @return KnownDevice[]
     */
    public function getKnownDevices(string $username): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('known_device')
            ->from(KnownDevice::class, 'known_device')
            ->join('known_device.user', 'user')
            ->where('user.email = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getResult();
    }

    /**
     * Deletes the given known device
     *
     * @param string $username
     * @param string $deviceCode
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function deleteKnownDevice(string $username, string $deviceCode): void
    {
        $knownDevice = $this->entityManager->createQueryBuilder()
            ->select('known_device')
            ->from(KnownDevice::class, 'known_device')
            ->join('known_device.user', 'user')
            ->where('user.email = :username')
            ->andWhere('known_device.key = :deviceCode')
            ->setParameter('deviceCode', $deviceCode)
            ->setParameter('username', $username)
            ->getQuery()
            ->getSingleResult();

        $this->entityManager->remove($knownDevice);
        $this->entityManager->flush();
    }
}
