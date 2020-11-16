<?php

namespace Jinya\Entity\SegmentPage;

use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Base\BaseEntity;
use Jinya\Entity\Form\Form;
use Jinya\Entity\Media\File;
use Jinya\Entity\Media\Gallery;

/**
 * @ORM\Entity
 * @ORM\Table(name="segment")
 */
class Segment
{
    public const ACTION_SCRIPT = 'script';

    public const ACTION_LINK = 'link';

    public const ACTION_NONE = 'none';

    use BaseEntity;

    /**
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\SegmentPage\SegmentPage", inversedBy="segments", cascade={"persist"})
     */
    private SegmentPage $page;

    /**
     * @ORM\Column(type="integer")
     */
    private int $position;

    /**
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Form\Form")
     */
    private ?Form $form = null;

    /**
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Media\Gallery")
     */
    private ?Gallery $gallery = null;

    /**
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Media\File")
     */
    private ?File $file = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $html = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $action = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $script = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $target = null;

    public function getPage(): SegmentPage
    {
        return $this->page;
    }

    public function setPage(SegmentPage $page): void
    {
        $this->page = $page;
    }

    public function getGallery(): ?Gallery
    {
        return $this->gallery;
    }

    public function setGallery(?Gallery $gallery): void
    {
        $this->gallery = $gallery;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): void
    {
        $this->file = $file;
    }

    public function getForm(): ?Form
    {
        return $this->form;
    }

    public function setForm(?Form $form): void
    {
        $this->reset();
        $this->form = $form;
    }

    private function reset(): void
    {
        $this->form = null;
        $this->html = null;
        $this->file = null;
        $this->gallery = null;
    }

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function setHtml(?string $html): void
    {
        $this->reset();
        $this->html = $html;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(?string $action): void
    {
        $this->action = $action;
    }

    public function getScript(): ?string
    {
        return $this->script;
    }

    public function setScript(?string $script): void
    {
        $this->script = $script;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setTarget(?string $target): void
    {
        $this->target = $target;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }
}
