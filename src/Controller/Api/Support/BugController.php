<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 15.05.18
 * Time: 17:40
 */

namespace Jinya\Controller\Api\Support;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Jinya\Entity\Artist\User;
use Jinya\Framework\BaseApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BugController extends BaseApiController
{
    /** @var string */
    private $jinyaVersion;

    /**
     * @Route("/api/support/bug", methods={"POST"}, name="api_support_bug")
     *
     * @param Request $request
     * @param Client $client
     * @return Response
     */
    public function submitAction(Request $request, Client $client): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($request, $client) {
            ob_start();
            phpinfo(INFO_GENERAL + INFO_CONFIGURATION + INFO_MODULES);
            $phpInfo = ob_get_contents();
            ob_clean();

            /** @var User $user */
            $user = $this->getUser();

            $bug = [
                'phpInfo' => $phpInfo,
                'title' => $this->getValue('title'),
                'details' => $this->getValue('details'),
                'reproduce' => $this->getValue('reproduce'),
                'severity' => $this->getValue('severity'),
                'who' => $user->getFirstname(),
                'url' => $request->headers->has('referer') ? $request->headers->get('referer') : '',
                'jinyaVersion' => $this->jinyaVersion,
            ];

            $response = $client->request('POST', 'https://api.jinya.de/tracker/bug', [
                RequestOptions::JSON => $bug,
                RequestOptions::HEADERS => [
                    'Content-Type' => 'application/json',
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        });

        return $this->json($data, $status);
    }

    /**
     * @param string $jinyaVersion
     */
    public function setJinyaVersion(string $jinyaVersion): void
    {
        $this->jinyaVersion = $jinyaVersion;
    }
}
