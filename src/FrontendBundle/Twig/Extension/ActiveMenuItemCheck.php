<?php

namespace FrontendBundle\Twig\Extension;

use DataBundle\Entity\MenuItem;
use Symfony\Component\Routing\RequestContext;

class ActiveMenuItemCheck extends \Twig_Extension
{
    /** @var RequestContext */
    private $requestContext;

    /**
     * ActiveMenuItemCheck constructor.
     * @param RequestContext $requestContext
     */
    public function __construct(RequestContext $requestContext)
    {
        $this->requestContext = $requestContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'active_menu_item_check';
    }

    public function getFunctions()
    {
        return [
            'isActiveMenuItem' => new \Twig_Function('isActiveMenuItem', [$this, 'isActiveMenuItem']),
            'isChildActiveMenuItem' => new \Twig_Function('isChildActiveMenuItem', [$this, 'isChildActiveMenuItem'])
        ];
    }

    /**
     * @param MenuItem $menuItem
     * @return bool
     */
    public function isChildActiveMenuItem(MenuItem $menuItem): bool
    {
        foreach ($menuItem->getChildren()->toArray() as $item) {
            if ($this->isActiveMenuItem($item) || $this->isChildActiveMenuItem($item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param MenuItem $menuItem
     * @return bool
     */
    public function isActiveMenuItem(MenuItem $menuItem): bool
    {
        $url = $menuItem->getRoute()->getUrl();
        $pathInfo = $this->requestContext->getPathInfo();

        return $url === $pathInfo;
    }
}
