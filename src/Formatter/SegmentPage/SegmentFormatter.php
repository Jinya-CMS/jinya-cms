<?php

namespace Jinya\Formatter\SegmentPage;

use Jinya\Entity\SegmentPage\Segment;
use Jinya\Formatter\Form\FormFormatterInterface;
use Jinya\Formatter\Media\FileFormatterInterface;
use Jinya\Formatter\Media\GalleryFormatterInterface;

class SegmentFormatter implements SegmentFormatterInterface
{
    /** @var array */
    private $formatted;

    /** @var Segment */
    private $segment;

    /** @var GalleryFormatterInterface */
    private $galleryFormatter;

    /** @var FileFormatterInterface */
    private $fileFormatter;

    /** @var FormFormatterInterface */
    private $formFormatter;

    /**
     * SegmentFormatter constructor.
     * @param FormFormatterInterface $formFormatter
     * @param FileFormatterInterface $fileFormatter
     * @param GalleryFormatterInterface $galleryFormatter
     */
    public function __construct(
        FormFormatterInterface $formFormatter,
        FileFormatterInterface $fileFormatter,
        GalleryFormatterInterface $galleryFormatter
    ) {
        $this->formFormatter = $formFormatter;
        $this->galleryFormatter = $galleryFormatter;
        $this->fileFormatter = $fileFormatter;
    }

    /**
     * Formats the content of the @return array
     * @see FormatterInterface into an array
     */
    public function format(): array
    {
        return $this->formatted;
    }

    /**
     * Initializes the formatting
     *
     * @param Segment $segment
     * @return SegmentFormatterInterface
     */
    public function init(Segment $segment): SegmentFormatterInterface
    {
        $this->formatted = [];
        $this->segment = $segment;

        return $this;
    }

    /**
     * Formats the id
     *
     * @return SegmentFormatterInterface
     */
    public function id(): SegmentFormatterInterface
    {
        $this->formatted['id'] = $this->segment->getId();

        return $this;
    }

    /**
     * Formats the action
     *
     * @return SegmentFormatterInterface
     */
    public function action(): SegmentFormatterInterface
    {
        if ($this->segment->getFile()) {
            $this->formatted['action'] = $this->segment->getAction();
        }

        return $this;
    }

    /**
     * Formats the target
     *
     * @return SegmentFormatterInterface
     */
    public function target(): SegmentFormatterInterface
    {
        if ($this->segment->getFile()) {
            $this->formatted['target'] = $this->segment->getTarget();
        }

        return $this;
    }

    /**
     * Formats the script
     *
     * @return SegmentFormatterInterface
     */
    public function script(): SegmentFormatterInterface
    {
        if ($this->segment->getFile()) {
            $this->formatted['script'] = $this->segment->getScript();
        }

        return $this;
    }

    /**
     * Formats the gallery
     *
     * @return SegmentFormatterInterface
     */
    public function gallery(): SegmentFormatterInterface
    {
        if (null !== $this->segment->getGallery()) {
            $this->formatted['gallery'] = $this
                ->galleryFormatter
                ->init($this->segment->getGallery())
                ->slug()
                ->id()
                ->name()
                ->description()
                ->format();
        }

        return $this;
    }

    /**
     * Formats the file
     *
     * @return SegmentFormatterInterface
     */
    public function file(): SegmentFormatterInterface
    {
        if (null !== $this->segment->getFile()) {
            $this->formatted['file'] = $this
                ->fileFormatter
                ->init($this->segment->getFile())
                ->id()
                ->name()
                ->path()
                ->type()
                ->format();
        }

        return $this;
    }

    /**
     * Formats the form
     *
     * @return SegmentFormatterInterface
     */
    public function form(): SegmentFormatterInterface
    {
        if (null !== $this->segment->getForm()) {
            $this->formatted['form'] = $this
                ->formFormatter
                ->init($this->segment->getForm())
                ->slug()
                ->name()
                ->description()
                ->format();
        }

        return $this;
    }

    /**
     * Formats the html
     *
     * @return SegmentFormatterInterface
     */
    public function html(): SegmentFormatterInterface
    {
        if (null !== $this->segment->getHtml()) {
            $this->formatted['html'] = $this->segment->getHtml();
        }

        return $this;
    }

    /**
     * Formats the position
     *
     * @return SegmentFormatterInterface
     */
    public function position(): SegmentFormatterInterface
    {
        $this->formatted['position'] = $this->segment->getPosition();

        return $this;
    }
}
