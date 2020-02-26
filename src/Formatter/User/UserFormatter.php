<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 16:17
 */

namespace Jinya\Formatter\User;

use Jinya\Entity\Artist\User;
use Jinya\Entity\Form\Form;
use Jinya\Entity\Page\Page;
use Jinya\Formatter\Form\FormFormatterInterface;
use Jinya\Formatter\Page\PageFormatterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use function array_map;

class UserFormatter implements UserFormatterInterface
{
    /** @var User */
    private User $user;

    /** @var array */
    private array $formattedData;

    /** @var UrlGeneratorInterface */
    private UrlGeneratorInterface $urlGenerator;

    /** @var PageFormatterInterface */
    private PageFormatterInterface $pageFormatter;

    /** @var FormFormatterInterface */
    private FormFormatterInterface $formFormatter;

    /**
     * UserFormatter constructor.
     * @param RoleHierarchyInterface $roleHierarchy
     */
    public function __construct(RoleHierarchyInterface $roleHierarchy)
    {
        $this->roleHierarchy = $roleHierarchy;
    }

    /**
     * @param PageFormatterInterface $pageFormatter
     */
    public function setPageFormatter(PageFormatterInterface $pageFormatter): void
    {
        $this->pageFormatter = $pageFormatter;
    }

    /**
     * @param FormFormatterInterface $formFormatter
     */
    public function setFormFormatter(FormFormatterInterface $formFormatter): void
    {
        $this->formFormatter = $formFormatter;
    }

    /**
     * Formats the content of the @return array
     * @see FormatterInterface into an array
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
        $this->formattedData['roles'] = $this->roleHierarchy->getReachableRoleNames($this->user->getRoles());

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
        $this->formattedData['profilePicture'] = $this->user->getProfilePicture();

        return $this;
    }

    /**
     * Shorthand for
     * @return UserFormatterInterface
     * @see UserFormatterInterface::firstname()
     * @see UserFormatterInterface::lastname()
     * @see UserFormatterInterface::email()
     * @see UserFormatterInterface::profilePicture()
     */
    public function profile(): UserFormatterInterface
    {
        return $this
            ->artistName()
            ->email()
            ->profilePicture();
    }

    /**
     * Formats the artist name
     *
     * @return UserFormatterInterface
     */
    public function artistName(): UserFormatterInterface
    {
        $this->formattedData['artistName'] = $this->user->getArtistName();

        return $this;
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

    /**
     * Formats the created pages
     *
     * @return UserFormatterInterface
     */
    public function createdPages(): UserFormatterInterface
    {
        $this->formattedData['created']['pages'] = array_map(function (Page $page) {
            return $this->pageFormatter
                ->init($page)
                ->name()
                ->slug()
                ->format();
        }, $this->user->getCreatedPages()->toArray());

        return $this;
    }

    /**
     * Formats the created forms
     *
     * @return UserFormatterInterface
     */
    public function createdForms(): UserFormatterInterface
    {
        $this->formattedData['created']['forms'] = array_map(function (Form $form) {
            return $this->formFormatter
                ->init($form)
                ->name()
                ->slug()
                ->format();
        }, $this->user->getCreatedForms()->toArray());

        return $this;
    }
}
