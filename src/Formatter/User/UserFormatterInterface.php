<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 15:55.
 */

namespace Jinya\Formatter\User;

use Jinya\Entity\User;
use Jinya\Formatter\FormatterInterface;

interface UserFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the formatting.
     *
     * @param User $user
     *
     * @return UserFormatterInterface
     */
    public function init(User $user): UserFormatterInterface;

    /**
     * Formats the firstname.
     *
     * @return UserFormatterInterface
     */
    public function firstname(): UserFormatterInterface;

    /**
     * Formats the lastname.
     *
     * @return UserFormatterInterface
     */
    public function lastname(): UserFormatterInterface;

    /**
     * Formats the roles.
     *
     * @return UserFormatterInterface
     */
    public function roles(): UserFormatterInterface;

    /**
     * Formats the email.
     *
     * @return UserFormatterInterface
     */
    public function email(): UserFormatterInterface;

    /**
     * Formats the enable state.
     *
     * @return UserFormatterInterface
     */
    public function enabled(): UserFormatterInterface;

    /**
     * Formats the profile picture.
     *
     * @return UserFormatterInterface
     */
    public function profilePicture(): UserFormatterInterface;

    /**
     * Shorthand for @see UserFormatterInterface::profilePicture(), @see UserFormatterInterface::firstname(), @see UserFormatterInterface::lastname(), @see UserFormatterInterface::email().
     *
     * @return UserFormatterInterface
     */
    public function profile(): UserFormatterInterface;

    /**
     * Formats the id.
     *
     * @return UserFormatterInterface
     */
    public function id(): UserFormatterInterface;

    /**
     * Formats the created artworks.
     *
     * @return UserFormatterInterface
     */
    public function createdArtworks(): UserFormatterInterface;

    /**
     * Formats the created galleries.
     *
     * @return UserFormatterInterface
     */
    public function createdGalleries(): UserFormatterInterface;

    /**
     * Formats the created pages.
     *
     * @return UserFormatterInterface
     */
    public function createdPages(): UserFormatterInterface;

    /**
     * Formats the created forms.
     *
     * @return UserFormatterInterface
     */
    public function createdForms(): UserFormatterInterface;
}
