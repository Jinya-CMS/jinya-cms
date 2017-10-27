<?php

namespace BackendBundle\Twig\Extension;

class ControllerUtils extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            'get_controller_name' => new \Twig_Function('get_controller_name', [$this, 'getControllerName']),
            'get_action_name' => new \Twig_Function('get_action_name', [$this, 'getActionName']),
        ];
    }

    public function getControllerName($request_attributes): string
    {
        $pattern = '/Controller\\\\([a-zA-Z]*)Controller/';
        $matches = [];
        preg_match($pattern, $request_attributes->get('_controller'), $matches);

        return $matches[1];
    }

    public function getActionName($request_attributes): string
    {
        $pattern = '/::.*Action/';
        $matches = [];
        preg_match($pattern, $request_attributes->get('_controller'), $matches);

        $action = $matches[0];
        $action = preg_replace('/::/', '', $action);
        $action = preg_replace('/Action/', '', $action);

        return $action;
    }
}
