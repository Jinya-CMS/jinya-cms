<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 16:17
 */

namespace Jinya\Formatter\User;


use Jinya\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserFormatter implements UserFormatterInterface
{
    /** @var User */
    private $user;

    /** @var array */
    private $formattedData;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /**
     * UserFormatter constructor.
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Formats the content of the @see FormatterInterface into an array
     *
     * @return array
     */
    public function format(): array
    {
        return $this->formattedData;
    }

    /**
     * Initializes the formatting
     *
     * @param User $user
     * @return UserFormatterInterface
     */
    public function init(User $user): UserFormatterInterface
    {
        $this->user = $user;
        $this->formattedData = [];

        return $this;
    }

    /**
     * Formats the lastname
     *
     * @return UserFormatterInterface
     */
    public function lastname(): UserFormatterInterface
    {
        $this->formattedData['lastname'] = $this->user->getLastname();

        return $this;
    }

    /**
     * Formats the roles
     *
     * @return UserFormatterInterface
     */
    public function roles(): UserFormatterInterface
    {
        $this->formattedData['roles'] = $this->user->getRoles();

        return $this;
    }

    /**
     * Formats the email
     *
     * @return UserFormatterInterface
     */
    public function email(): UserFormatterInterface
    {
        $this->formattedData['email'] = $this->user->getEmail();

        return $this;
    }

    /**
     * Formats the enable state
     *
     * @return UserFormatterInterface
     */
    public function enabled(): UserFormatterInterface
    {
        $this->formattedData['enabled'] = $this->user->isEnabled();

        return $this;
    }

    /**
     * Formats the profile picture
     *
     * @return UserFormatterInterface
     */
    public function profilePicture(): UserFormatterInterface
    {
        $this->formattedData['profilePicture'] = $this->urlGenerator->generate('api_user_profilepicture_get', ['id' => $this->user->getId()]);

        return $this;
    }

    /**
     * Shorthand for @see UserFormatterInterface::profilePicture(), @see UserFormatterInterface::firstname(), @see UserFormatterInterface::lastname(), @see UserFormatterInterface::email()
     *
     * @return UserFormatterInterface
     */
    public function profile(): UserFormatterInterface
    {
        return $this
            ->firstname()
            ->lastname()
            ->email()
            ->profilePicture();
    }

    /**
     * Formats the firstname
     *
     * @return UserFormatterInterface
     */
    public function firstname(): UserFormatterInterface
    {
        $this->formattedData['firstname'] = $this->user->getFirstname();

        return $this;
    }

    /**
     * Formats the id
     *
     * @return UserFormatterInterface
     */
    public function id(): UserFormatterInterface
    {
        $this->formattedData['id'] = $this->user->getId();

        return $this;
    }
}