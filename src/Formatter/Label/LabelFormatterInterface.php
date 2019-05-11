<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 15:36
 */

namespace Jinya\Formatter\Label;

use Jinya\Entity\Label\Label;
use Jinya\Formatter\FormatterInterface;

interface LabelFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the formatting
     *
     * @param Label $label
     * @return LabelFormatterInterface
     */
    public function init(Label $label): self;

    /**
     * Formats the label
     *
     * @return LabelFormatterInterface
     */
    public function name(): self;

    /**
     * Formats the artworks
     *
     * @return LabelFormatterInterface
     */
    public function artworks(): self;

    /**
     * Formats the galleries
     *
     * @return LabelFormatterInterface
     */
    public function galleries(): self;
}
