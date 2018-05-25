<?php

namespace Jinya\Twig\Extension;

use Symfony\Component\Routing\RequestContext;

class RoutingHelper extends \Twig_Extension
{
    /** @var RequestContext */
    private $request;

    /**
     * RoutingHelper constructor.
     * @param RequestContext $request
     */
    public function __construct(RequestContext $request)
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'routing_helper';
    }

    public function getFunctions()
    {
        return [
            'get_url' => new \Twig_Function('get_url', [$this, 'getUrl']),
        ];
    }

    public function getUrl()
    {
        $url = $this->request->getScheme() . '://' . $this->request->getHost();

        if ('http' === $this->request->getScheme() && 80 !== $this->request->getHttpPort()) {
            $url .= ':' . $this->request->getHttpPort();
        } elseif ('https' === $this->request->getScheme() && 443 !== $this->request->getHttpsPort()) {
            $url .= ':' . $this->request->getHttpsPort();
        }

        $url .= $this->request->getPathInfo();

        if (!empty($this->request->getQueryString())) {
            $url .= '?' . $this->request->getQueryString();
        }

        return $url;
    }
}
