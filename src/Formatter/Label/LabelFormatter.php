<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 15:37
 */

namespace Jinya\Formatter\Label;

use Jinya\Entity\Label;

class LabelFormatter implements LabelFormatterInterface
{

    /** @var Label */
    private $label;

    /** @var array */
    private $formattedData;

    /**
     * Formats the given label into an array
     *
     * @param Label $label
     * @return LabelFormatterInterface
     */
    public function init(Label $label): LabelFormatterInterface
    {
        $this->label = $label;
        $this->formattedData = [];

        return $this;
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
     * Formats the name
     *
     * @return LabelFormatterInterface
     */
    public function name(): LabelFormatterInterface
    {
        $this->formattedData['name'] = $this->label->getName();

        return $this;
    }

    /**
     * Formats the artworks
     *
     * @return LabelFormatterInterface
     */
    public function artworks(): LabelFormatterInterface
    {
        foreach ($this->label->getArtworks() as $artwork) {
            $this->formattedData['artwork'][] = [
                'name' => $artwork->getName(),
                'slug' => $artwork->getSlug()
            ];
        }

        return $this;
    }

    /**
     * Formats the galleries
     *
     * @return LabelFormatterInterface
     */
    public function galleries(): LabelFormatterInterface
    {
        foreach ($this->label->getGalleries() as $gallery) {
            $this->formattedData['galleries'][] = [
                'name' => $gallery->getName(),
                'slug' => $gallery->getSlug()
            ];
        }

        return $this;
    }
}