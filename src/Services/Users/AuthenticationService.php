<?php

namespace Jinya\Services\Users;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Jinya\Entity\Authentication\KnownDevice;
use Jinya\Framework\Events\User\TwoFactorCodeEvent;
use Jinya\Framework\Events\User\TwoFactorCodeSubmissionEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AuthenticationService implements AuthenticationServiceInterface
{
    /** @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /** @var MailerInterface */
    private MailerInterface $mailer;

    /** @var string */
    private string $mailerSender;

    /** @var EventDispatcherInterface */
    private EventDispatcherInterface $eventDispatcher;

    /** @var UserServiceInterface */
    private UserServiceInterface $userService;

    /**
     * AuthenticationService constructor.
     * @param EntityManagerInterface $entityManager
     * @param MailerInterface $mailer
     * @param string $mailerSender
     * @param EventDispatcherInterface $eventDispatcher
     * @param UserServiceInterface $userService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        string $mailerSender,
        EventDispatcherInterface $eventDispatcher,
        UserServiceInterface $userService
    ) {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->mailerSender = $mailerSender;
        $this->eventDispatcher = $eventDispatcher;
        $this->userService = $userService;
    }

    /**
     * Sets the two factor code and sends the verification mail
     *
     * @param string $username
     * @throws TransportExceptionInterface
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
            $message = new TemplatedEmail();
            $message->to($user->getEmail());
            $message->subject('Your two factor code');
            $message->htmlTemplate('@Jinya\Email\twoFactor.html.twig');
            $message->from($this->mailerSender);
            $message->context(['artist' => $user]);
            $this->mailer->send($message);
        }

        $this->eventDispatcher->dispatch(
            new TwoFactorCodeSubmissionEvent($username, $code),
            TwoFactorCodeSubmissionEvent::POST_CODE_SUBMISSION
        );
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
