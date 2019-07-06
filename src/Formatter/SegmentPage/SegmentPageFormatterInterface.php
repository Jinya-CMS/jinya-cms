<?php

namespace Jinya\Formatter\SegmentPage;

use Jinya\Entity\SegmentPage\SegmentPage;
use Jinya\Formatter\FormatterInterface;

interface SegmentPageFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the formatting
     *
     * @param SegmentPage $segmentPage
     * @return SegmentPageFormatterInterface
     */
    public function init(SegmentPage $segmentPage): self;

    /**
     * Formats the slug
     *
     * @return SegmentPageFormatterInterface
     */
    public function slug(): self;

    /**
     * Formats the name
     *
     * @return SegmentPageFormatterInterface
     */
    public function name(): self;

    /**
     * Formats the segments
     *
     * @return SegmentPageFormatterInterface
     */
    public function segments(): self;

    /**
     * Formats the created info
     *
     * @return SegmentPageFormatterInterface
     */
    public function created(): self;

    /**
     * Formats the updated info
     *
     * @return SegmentPageFormatterInterface
     */
    public function updated(): self;

    /**
     * Formats the history
     *
     * @return SegmentPageFormatterInterface
     */
    public function history(): self;
}