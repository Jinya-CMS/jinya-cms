<?php

namespace Jinya\Controller\Api\Maintenance;

use Jinya\Framework\BaseApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EnvironmentController extends BaseApiController
{
    /**
     * @Route("/api/environment", methods={"GET"}, name="api_environment_get_all")
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function getValuesAction(): Response
    {
        $data = [];
        $data[] = ['key' => 'APP_SECRET', 'value' => getenv('APP_SECRET')];
        $data[] = ['key' => 'APP_DEBUG', 'value' => getenv('APP_DEBUG')];
        $data[] = ['key' => 'APP_ENV', 'value' => getenv('APP_ENV')];
        $data[] = ['key' => 'APP_PROFILING', 'value' => getenv('APP_PROFILING')];
        $data[] = ['key' => 'MAILER_URL', 'value' => $this->cleanUrl(getenv('MAILER_DSN'))];
        $data[] = ['key' => 'MAILER_SENDER', 'value' => getenv('MAILER_SENDER')];
        $data[] = ['key' => 'DATABASE_URL', 'value' => $this->cleanUrl(getenv('DATABASE_URL'))];

        return $this->json($data);
    }

    private function cleanUrl(string $url): string
    {
        $parts = parse_url($url);

        $newUrl = '';
        if (array_key_exists('scheme', $parts)) {
            $newUrl = $parts['scheme'] . '://';
        }
        if (array_key_exists('user', $parts)) {
            $newUrl .= $parts['user'];
            if (array_key_exists('pass', $parts)) {
                $newUrl .= ':******';
            }
            $newUrl .= '@';
        }

        if (array_key_exists('host', $parts)) {
            $newUrl .= $parts['host'];
        }

        if (array_key_exists('port', $parts)) {
            $newUrl .= ':' . $parts['port'];
        }

        if (array_key_exists('path', $parts)) {
            $newUrl .= $parts['path'];
        }

        if (array_key_exists('query', $parts)) {
            $newUrl .= '?' . $parts['query'];
        }

        if (array_key_exists('fragment', $parts)) {
            $newUrl .= '#' . $parts['fragment'];
        }

        return $newUrl;
    }
}
