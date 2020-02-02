<?php

namespace Jinya\Formatter\SegmentPage;

use Jinya\Entity\SegmentPage\Segment;
use Jinya\Formatter\FormatterInterface;

interface SegmentFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the formatting
     *
     * @param Segment $segment
     * @return SegmentFormatterInterface
     */
    public function init(Segment $segment): self;

    /**
     * Formats the id
     *
     * @return SegmentFormatterInterface
     */
    public function id(): self;

    /**
     * Formats the action
     *
     * @return SegmentFormatterInterface
     */
    public function action(): self;

    /**
     * Formats the target
     *
     * @return SegmentFormatterInterface
     */
    public function target(): self;

    /**
     * Formats the script
     *
     * @return SegmentFormatterInterface
     */
    public function script(): self;

    /**
     * Formats the form
     *
     * @return SegmentFormatterInterface
     */
    public function form(): self;

    /**
     * Formats the html
     *
     * @return SegmentFormatterInterface
     */
    public function html(): self;

    /**
     * Formats the position
     *
     * @return SegmentFormatterInterface
     */
    public function position(): self;

    /**
     * Formats the gallery
     *
     * @return SegmentFormatterInterface
     */
    public function gallery(): self;

    /**
     * Formats the file
     *
     * @return SegmentFormatterInterface
     */
    public function file(): self;
}
