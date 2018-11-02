<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 01.11.18
 * Time: 01:50
 */

namespace Jinya\EventSubscriber\Profiling;

use Jinya\Framework\Profiling\Formatting\ProfileFormatterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Component\Yaml\Yaml;
use Throwable;

class JinyaProfilerEventSubscriber implements EventSubscriberInterface
{
    /** @var Profiler */
    private $profiler;

    /** @var string */
    private $jinyaProfilerOutDir;

    /** @var Filesystem */
    private $fs;

    /** @var ProfileFormatterInterface */
    private $profileFormatter;

    /**
     * JinyaProfilerEventSubscriber constructor.
     * @param Profiler $profiler
     * @param string $jinyaProfilerOutDir
     */
    public function __construct(Profiler $profiler, string $jinyaProfilerOutDir, ProfileFormatterInterface $profileFormatter)
    {
        $this->profiler = $profiler;
        $this->jinyaProfilerOutDir = $jinyaProfilerOutDir;
        $this->fs = new Filesystem();
        $this->profileFormatter = $profileFormatter;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::TERMINATE => ['onKernelTerminate', -2048],
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (getenv('APP_ENV') === 'dev' && function_exists('tideways_xhprof_enable') && $event->isMasterRequest()) {
            tideways_xhprof_enable(TIDEWAYS_XHPROF_FLAGS_CPU | TIDEWAYS_XHPROF_FLAGS_MEMORY_MU | TIDEWAYS_XHPROF_FLAGS_MEMORY_PMU | TIDEWAYS_XHPROF_FLAGS_MEMORY | TIDEWAYS_XHPROF_FLAGS_NO_BUILTINS);
        }
    }

    public function onKernelTerminate(PostResponseEvent $event)
    {
        if (getenv('APP_ENV') === 'dev') {
            $profile = $this->profiler->loadProfileFromResponse($event->getResponse());
            $baseDir = $this->jinyaProfilerOutDir . '/' . $_SERVER['REQUEST_URI'] . '/';
            if (!file_exists($baseDir)) {
                mkdir($baseDir, 0777, true);
            }
            $date = date(DATE_ISO8601);

            if ($profile) {
                $relevantData = $this->profileFormatter->format($profile);
                $symfonyFile = $baseDir . "$date.jinya.symfony";

                $this->fs->dumpFile($symfonyFile, Yaml::dump($relevantData, 1024));
            }

            $this->dumpTidewaysData($baseDir, $date);
        }
    }

    /**
     * @param string $baseDir
     * @param string $date
     */
    private function dumpTidewaysData(string $baseDir, string $date): void
    {
        if (function_exists('tideways_xhprof_disable')) {
            try {
                $data = Yaml::dump(tideways_xhprof_disable());
                $xhprofFile = $baseDir . "$date.jinya.xhprof";
                $this->fs->dumpFile($xhprofFile, $data);
            } catch (Throwable $exception) {
                $errorLog = $this->jinyaProfilerOutDir . '/error.log';
                $this->fs->appendToFile($errorLog, $exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
            }
        }
    }
}