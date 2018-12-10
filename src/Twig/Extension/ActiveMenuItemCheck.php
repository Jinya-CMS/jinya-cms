<?php

namespace Jinya\Twig\Extension;

use Jinya\Entity\Menu\MenuItem;
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
            'isActiveMenuItem' => new \Twig_Function('isActiveMenuItem', [$this, 'isActiveMenuItem'], [
                'needs_context' => true,
                'needs_environment' => true,
            ]),
            'isChildActiveMenuItem' => new \Twig_Function('isChildActiveMenuItem', [$this, 'isChildActiveMenuItem'], [
                'needs_context' => true,
                'needs_environment' => true,
            ]),
        ];
    }

    /**
     * @param MenuItem $menuItem
     * @return bool
     */
    public function isChildActiveMenuItem(\Twig_Environment $environment, $context, MenuItem $menuItem): bool
    {
        foreach ($menuItem->getChildren()->toArray() as $item) {
            if ($this->isActiveMenuItem($environment, $context, $item) || $this->isChildActiveMenuItem($environment, $context, $item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param MenuItem $menuItem
     * @return bool
     */
    public function isActiveMenuItem(\Twig_Environment $environment, $context, MenuItem $menuItem): bool
    {
        $url = $menuItem->getRoute()->getUrl();
        $pathInfo = $this->requestContext->getPathInfo();

        if (array_key_exists('active', $context) && $context['active'] == $url) {
            return true;
        }

        return $url === $pathInfo;
    }
}
