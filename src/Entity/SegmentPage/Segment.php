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
     * @var SegmentPage
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\SegmentPage\SegmentPage", inversedBy="segments", cascade={"persist"})
     */
    private $page;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @var Form|null
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Form\Form")
     */
    private $form;

    /**
     * @var Gallery|null
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Media\Gallery")
     */
    private $gallery;

    /**
     * @var File|null
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Media\File")
     */
    private $file;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $html;

    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    private $action;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $script;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $target;

    /**
     * @return SegmentPage
     */
    public function getPage(): SegmentPage
    {
        return $this->page;
    }

    /**
     * @param SegmentPage $page
     */
    public function setPage(SegmentPage $page): void
    {
        $this->page = $page;
    }

    private function reset(): void
    {
        $this->page = null;
        $this->form = null;
        $this->html = null;
        $this->file = null;
        $this->gallery = null;
    }

    /**
     * @return Gallery|null
     */
    public function getGallery(): ?Gallery
    {
        return $this->gallery;
    }

    /**
     * @param Gallery|null $gallery
     */
    public function setGallery(?Gallery $gallery): void
    {
        $this->gallery = $gallery;
    }

    /**
     * @return File|null
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * @param File|null $file
     */
    public function setFile(?File $file): void
    {
        $this->file = $file;
    }

    /**
     * @return Form|null
     */
    public function getForm(): ?Form
    {
        return $this->form;
    }

    /**
     * @param Form|null $form
     */
    public function setForm(?Form $form): void
    {
        $this->reset();
        $this->form = $form;
    }

    /**
     * @return string|null
     */
    public function getHtml(): ?string
    {
        return $this->html;
    }

    /**
     * @param string|null $html
     */
    public function setHtml(?string $html): void
    {
        $this->reset();
        $this->html = $html;
    }

    /**
     * @return string|null
     */
    public function getAction(): ?string
    {
        return $this->action;
    }

    /**
     * @param string|null $action
     */
    public function setAction(?string $action): void
    {
        $this->action = $action;
    }

    /**
     * @return string|null
     */
    public function getScript(): ?string
    {
        return $this->script;
    }

    /**
     * @param string|null $script
     */
    public function setScript(?string $script): void
    {
        $this->script = $script;
    }

    /**
     * @return string|null
     */
    public function getTarget(): ?string
    {
        return $this->target;
    }

    /**
     * @param string|null $target
     */
    public function setTarget(?string $target): void
    {
        $this->target = $target;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }
}
