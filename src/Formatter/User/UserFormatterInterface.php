<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 15:55
 */

namespace Jinya\Formatter\User;

use Jinya\Entity\Artist\User;
use Jinya\Formatter\FormatterInterface;

interface UserFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the formatting
     *
     * @param User $user
     * @return UserFormatterInterface
     */
    public function init(User $user): self;

    /**
     * Formats the firstname
     *
     * @return UserFormatterInterface
     */
    public function firstname(): self;

    /**
     * Formats the lastname
     *
     * @return UserFormatterInterface
     */
    public function lastname(): self;

    /**
     * Formats the artist name
     *
     * @return UserFormatterInterface
     */
    public function artistName(): self;

    /**
     * Formats the roles
     *
     * @return UserFormatterInterface
     */
    public function roles(): self;

    /**
     * Formats the email
     *
     * @return UserFormatterInterface
     */
    public function email(): self;

    /**
     * Formats the enable state
     *
     * @return UserFormatterInterface
     */
    public function enabled(): self;

    /**
     * Formats the profile picture
     *
     * @return UserFormatterInterface
     */
    public function profilePicture(): self;

    /**
     * Shorthand for @return UserFormatterInterface
     * @see UserFormatterInterface::profilePicture(), @see UserFormatterInterface::firstname(), @see UserFormatterInterface::lastname(), @see UserFormatterInterface::email()
     */
    public function profile(): self;

    /**
     * Formats the id
     *
     * @return UserFormatterInterface
     */
    public function id(): self;

    /**
     * Formats the created artworks
     *
     * @return UserFormatterInterface
     */
    public function createdArtworks(): self;

    /**
     * Formats the created galleries
     *
     * @return UserFormatterInterface
     */
    public function createdGalleries(): self;

    /**
     * Formats the created pages
     *
     * @return UserFormatterInterface
     */
    public function createdPages(): self;

    /**
     * Formats the created forms
     *
     * @return UserFormatterInterface
     */
    public function createdForms(): self;
}
