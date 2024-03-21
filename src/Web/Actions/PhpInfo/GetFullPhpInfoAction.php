<?php

namespace App\Web\Actions\PhpInfo;

use App\Web\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to grab all information available from phpinfo
 * @codeCoverageIgnore
 */
class GetFullPhpInfoAction extends Action
{
    /**
     * Gets the full data of PHP info by parsing the HTML output
     *
     * @return Response
     */
    protected function action(): Response
    {
        $info = $this->phpinfo2array();

        return $this->respond($info);
    }

    /**
     * Converts the output of phpinfo() into an array of values
     * This comment on the php.net website for phpinfo helped a lot, see the link below:
     * @link https://www.php.net/manual/en/function.phpinfo.php#117961
     *
     * @return array<array<mixed>>
     */
    private function phpinfo2array(): array
    {
        /**
         * @phpstan-ignore-next-line
         */
        $entitiesToUtf8 = static fn ($input) => preg_replace_callback('/(&#[0-9]+;)/', static fn (array $m) => mb_convert_encoding($m[1], 'UTF-8', 'HTML-ENTITIES'), $input);
        $plainText = static fn ($input) => trim(html_entity_decode($entitiesToUtf8(strip_tags($input))));

        ob_start();
        phpinfo(-1);

        $phpinfo = ['phpinfo' => []];
        $matches = [];

        // Strip everything after the <h1>Configuration</h1> tag (other h1's)
        /**
         * @phpstan-ignore-next-line
         */
        if (!preg_match('#(.*<h1[^>]*>\s*Configuration.*)<h1#s', ob_get_clean(), $matches)) {
            return [];
        }

        $input = $matches[1];
        $matches = [];

        if (preg_match_all(
            '#<h2.*?>(?:<a.*?>)?(.*?)(?:</a>)?</h2>|<tr.*?><t[hd].*?>(.*?)\s*</t[hd]>(?:<t[hd].*?>(.*?)\s*</t[hd]>(?:<t[hd].*?>(.*?)\s*</t[hd]>)?)?</tr>#s',
            $input,
            $matches,
            PREG_SET_ORDER
        )) {
            foreach ($matches as $match) {
                if ($match[1] !== '') {
                    $phpinfo[$match[1]] = [];
                } elseif (isset($match[3]) && $plainText($match[2]) !== 'Directive') {
                    $keys1 = array_keys($phpinfo);
                    $phpinfo[end($keys1)][$plainText($match[2])] = $plainText($match[3]);
                }
            }
        }

        $cleanedInfo = ['extensions' => []];
        foreach ($phpinfo as $groupKey => $group) {
            $iniValues = [];
            $configuration = [];
            foreach ($group as $key => $value) {
                if ($key === 'Variable' && $value === 'Value') {
                    continue;
                }

                $cleanedValue = match (str_contains($key, 'PASSWORD')) {
                    true => '••••••',
                    false => $value,
                };

                if (str_starts_with($key, "$groupKey.")) {
                    $iniValues[preg_replace("/$groupKey\./", '', $key)] = $cleanedValue;
                } elseif ($groupKey === 'Zend OPcache' && str_starts_with($key, 'opcache.')) {
                    $iniValues[preg_replace('/opcache./', '', $key)] = $cleanedValue;
                } elseif ($groupKey === 'apcu' && str_starts_with($key, 'apc.')) {
                    $iniValues[preg_replace('/apc./', '', $key)] = $cleanedValue;
                } elseif ($groupKey === 'cgi-fcgi' && str_starts_with($key, 'cgi.')) {
                    $iniValues[preg_replace('/cgi./', '', $key)] = $cleanedValue;
                } else {
                    $configuration[$key] = $cleanedValue;
                }
            }

            $cleanedGroupKey = match (strtolower($groupKey)) {
                'apache environment' => 'apache',
                'environment' => 'environment',
                'additional modules' => 'modules',
                'php variables' => 'variables',
                'http headers information' => 'headers',
                'phpinfo' => 'about',
                default => $groupKey,
            };

            if ($cleanedGroupKey === $groupKey) {
                $cleanedInfo['extensions'][$cleanedGroupKey] = [
                    ...$configuration,
                    'ini' => $iniValues,
                ];
            } else {
                $cleanedInfo[$cleanedGroupKey] = $configuration;
            }
        }

        return $cleanedInfo;
    }
}
