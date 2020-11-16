<?php

namespace Jinya\Formatter\SegmentPage;

use Jinya\Entity\SegmentPage\Segment;
use Jinya\Formatter\Form\FormFormatterInterface;
use Jinya\Formatter\Media\FileFormatterInterface;
use Jinya\Formatter\Media\GalleryFormatterInterface;

class SegmentFormatter implements SegmentFormatterInterface
{
    private array $formatted;

    private Segment $segment;

    private GalleryFormatterInterface $galleryFormatter;

    private FileFormatterInterface $fileFormatter;

    private FormFormatterInterface $formFormatter;

    /**
     * SegmentFormatter constructor.
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
     */
    public function init(Segment $segment): SegmentFormatterInterface
    {
        $this->formatted = [];
        $this->segment = $segment;

        return $this;
    }

    /**
     * Formats the id
     */
    public function id(): SegmentFormatterInterface
    {
        $this->formatted['id'] = $this->segment->getId();

        return $this;
    }

    /**
     * Formats the action
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
     */
    public function position(): SegmentFormatterInterface
    {
        $this->formatted['position'] = $this->segment->getPosition();

        return $this;
    }
}
