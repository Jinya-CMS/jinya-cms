<?php

namespace Jinya\Cms\Mailing\Types;

use Jinya\Cms\Logging\Logger;
use Jinya\Cms\Theming\Engine;
use Jinya\Plates\Engine as PlatesEngine;
use Locale;
use Psr\Log\LoggerInterface;

abstract readonly class BaseMail
{
    protected PlatesEngine $templateEngine;
    protected LoggerInterface $logger;
    /**
     * @var array<string, array<string, string>>
     */
    private array $strings;

    public function __construct()
    {
        $this->logger = Logger::getLogger();
        $this->templateEngine = Engine::getPlatesEngine();
        $this->templateEngine->functions->add('translate', [$this, 'translate']);
        $this->strings = include __DIR__ . '/../Translations/strings.php';
    }

    public function translate(string $key, mixed ...$parameters): string
    {
        $language = Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en');
        if ($language && stripos($language, 'de') !== false) {
            $language = 'de';
        } else {
            $language = 'en';
        }

        $data = $this->strings[$language] ?? [];
        $string = $data[$key] ?? $key;

        return sprintf($string, ...$parameters);
    }
}
