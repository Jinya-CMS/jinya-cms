<?php

namespace App\Console;

use App\Web\Attributes\JinyaAction;
use App\Web\Routes\RouteResolver;
use ReflectionException;

/**
 *
 */
#[JinyaCommand('routes')]
class RoutesCommand extends AbstractCommand
{

    public function run(): void
    {
        $routeResolver = new RouteResolver();
        try {
            $actions = iterator_to_array($routeResolver->getRoutes());

            usort($actions, static fn(JinyaAction $a, JinyaAction $b) => strcmp($a->method, $b->method));
            usort($actions, static fn(JinyaAction $a, JinyaAction $b) => strcmp($a->url, $b->url));

            foreach ($actions as $action) {
                /** @var JinyaAction $action */
                $method = match ($action->method) {
                    JinyaAction::GET => '<background_green><white> ' . JinyaAction::GET . '    </white></background_green>',
                    JinyaAction::POST => '<background_blue><white> ' . JinyaAction::POST . '   </white></background_blue>',
                    JinyaAction::PUT => '<background_magenta><white> ' . JinyaAction::PUT . '    </white></background_magenta>',
                    JinyaAction::DELETE => '<background_red><white> ' . JinyaAction::DELETE . ' </white></background_red>',
                    JinyaAction::HEAD => '<background_light_magenta><white> ' . JinyaAction::HEAD . '   </white></background_light_magenta>',
                    default => '',
                };
                $this->climate->padding(10, ' ')->label($method)->result($action->url);
            }
        } catch (ReflectionException $e) {
            $this->climate->to('error')->error('Failed to get routes');
            $this->climate->to('error')->error($e->getMessage());
            $this->climate->to('error')->error($e->getTraceAsString());
        }
    }
}