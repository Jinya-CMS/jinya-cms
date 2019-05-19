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
    private $formattedData;

    /** @var Page */
    private $page;

    /** @var UserFormatterInterface */
    private $userFormatter;

    /**
     * PageFormatter constructor.
     * @param UserFormatterInterface $userFormatter
     */
    public function __construct(UserFormatterInterface $userFormatter)
    {
        $this->userFormatter = $userFormatter;
    }

    /**
     * Formats the content of the @return array
     * @see FormatterInterface into an array
     *
     */
    public function format(): array
    {
        return $this->formattedData;
    }

    /**
     * Initializes the formatter
     *
     * @param Page $page
     * @return PageFormatterInterface
     */
    public function init(Page $page): PageFormatterInterface
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Formats the id
     *
     * @return PageFormatterInterface
     */
    public function id(): PageFormatterInterface
    {
        $this->formattedData['id'] = $this->page->getId();

        return $this;
    }

    /**
     * Formats the created info
     *
     * @return PageFormatterInterface
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
     *
     * @return PageFormatterInterface
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
     *
     * @return PageFormatterInterface
     */
    public function history(): PageFormatterInterface
    {
        $this->formattedData['history'] = $this->page->getHistory();

        return $this;
    }

    /**
     * Formats the content
     *
     * @return PageFormatterInterface
     */
    public function content(): PageFormatterInterface
    {
        $this->formattedData['content'] = $this->page->getContent();

        return $this;
    }

    /**
     * Formats the title
     *
     * @return PageFormatterInterface
     */
    public function title(): PageFormatterInterface
    {
        $this->formattedData['title'] = $this->page->getTitle();

        return $this;
    }

    /**
     * Formats the slug
     *
     * @return PageFormatterInterface
     */
    public function slug(): PageFormatterInterface
    {
        $this->formattedData['slug'] = $this->page->getSlug();

        return $this;
    }

    /**
     * Formats the name
     *
     * @return PageFormatterInterface
     */
    public function name(): PageFormatterInterface
    {
        $this->formattedData['name'] = $this->page->getName();

        return $this;
    }
}
