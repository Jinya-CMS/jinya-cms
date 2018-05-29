<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 17:49
 */

namespace Jinya\Entity;

interface ArtEntityInterface
{
    /**
     * Gets the slug of the art entity
     *
     * @return null|string
     */
    public function getSlug(): ?string;

    /**
     * Sets the slug of the art entity
     *
     * @param string $slug
     */
    public function setSlug(string $slug): void;

    /**
     * Gets the name of the art entity
     *
     * @return null|string
     */
    public function getName(): ?string;

    /**
     * Sets the name of the art entity
     * @param string $name
     */
    public function setName(string $name): void;

    /**
     * Gets the description of the art entity
     *
     * @return null|string
     */
    public function getDescription(): ?string;

    /**
     * Sets the description of the art entity
     *
     * @param string $description
     */
    public function setDescription(string $description): void;
}
