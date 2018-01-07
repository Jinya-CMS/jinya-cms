<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 07.01.2018
 * Time: 19:59
 */

namespace DataBundle\Services\Labels;


use DataBundle\Entity\Label;

interface LabelServiceInterface
{
    /**
     * Gets all labels
     *
     * @return Label[]
     */
    public function getAllLabels(): array;

    /**
     * Gets all labels that are attached to artworks
     *
     * @return Label[]
     */
    public function getAllLabelsWithArtworks(): array;

    /**
     * Gets all labels that are attached to galleries
     *
     * @return Label[]
     */
    public function getAllLabelsWithGalleries(): array;

    /**
     * Gets the specific label
     *
     * @param string $name
     * @return Label
     */
    public function getLabel(string $name): Label;

    /**
     * Adds a label with the given name
     *
     * @param string $name
     * @return Label
     */
    public function addLabel(string $name): Label;

    /**
     * Deletes the given label
     *
     * @param string $name
     */
    public function deleteLabel(string $name): void;

    /**
     * Updates the given label
     *
     * @param Label $label
     * @return Label
     */
    public function updateLabel(Label $label): Label;

    /**
     * Gets all label names
     *
     * @return string[]
     */
    public function getAllLabelNames(): array;

    /**
     * Creates all missing labels
     *
     * @param array $labels
     */
    public function createMissingLabels(array $labels): void;
}