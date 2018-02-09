<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 17:49
 */

namespace DataBundle\Entity;


interface ArtEntityInterface
{
    public function getSlug(): ?string;

    public function setSlug(string $slug);

    public function getName(): ?string;

    public function setName(string $name);

    public function getDescription(): ?string;

    public function setDescription(string $description);
}