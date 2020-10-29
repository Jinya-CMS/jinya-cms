<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.03.2018
 * Time: 22:53
 */

namespace Jinya\Formatter\Page;

use Jinya\Entity\Page\Page;
use Jinya\Formatter\User\UserFormatterInterface;

class PageFormatter implements PageFormatterInterface
{
    /** @var array */
    private array $formattedData;

    /** @var Page */
    private Page $page;

    /** @var UserFormatterInterface */
    private UserFormatterInterface $userFormatter;

    /**
     * PageFormatter constructor.
     */
    public function __construct(UserFormatterInterface $userFormatter)
    {
        $this->userFormatter = $userFormatter;
    }

    /**
     * Formats the content of the @return array
     * @see FormatterInterface into an array
     */
    public function format(): array
    {
        return $this->formattedData;
    }

    /**
     * Initializes the formatter
     */
    public function init(Page $page): PageFormatterInterface
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Formats the id
     */
    public function id(): PageFormatterInterface
    {
        $this->formattedData['id'] = $this->page->getId();

        return $this;
    }

    /**
     * Formats the created info
     */
    public function created(): PageFormatterInterface
    {
        $this->formattedData['created']['by'] = $this->userFormatter
            ->init($this->page->getCreator())
            ->profile()
            ->format();
        $this->formattedData['created']['at'] = $this->page->getCreatedAt();

        return $this;
    }

    /**
     * Formats the updated info
     */
    public function updated(): PageFormatterInterface
    {
        $this->formattedData['updated']['by'] = $this->userFormatter
            ->init($this->page->getUpdatedBy())
            ->profile()
            ->format();
        $this->formattedData['updated']['at'] = $this->page->getLastUpdatedAt();

        return $this;
    }

    /**
     * Formats the history
     */
    public function history(): PageFormatterInterface
    {
        $this->formattedData['history'] = $this->page->getHistory();

        return $this;
    }

    /**
     * Formats the content
     */
    public function content(): PageFormatterInterface
    {
        $this->formattedData['content'] = $this->page->getContent();

        return $this;
    }

    /**
     * Formats the title
     */
    public function title(): PageFormatterInterface
    {
        $this->formattedData['title'] = $this->page->getTitle();

        return $this;
    }

    /**
     * Formats the slug
     */
    public function slug(): PageFormatterInterface
    {
        $this->formattedData['slug'] = $this->page->getSlug();

        return $this;
    }

    /**
     * Formats the name
     */
    public function name(): PageFormatterInterface
    {
        $this->formattedData['name'] = $this->page->getName();

        return $this;
    }
}
