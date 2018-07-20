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
use Jinya\Framework\BaseApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends BaseApiController
{
    /** @var string */
    private $jinyaVersion;

    /**
     * @Route("/api/support/like", methods={"POST"}, name="api_support_like")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     *
     * @param Client $client
     * @return Response
     */
    public function submitAction(Client $client): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($client) {
            /** @var \Jinya\Entity\Artist\User $user */
            $user = $this->getUser();
            $like = [
                'who' => $user->getFirstname(),
                'message' => $this->getValue('message', null),
            ];

            $response = $client->request('POST', 'https://api.jinya.de/tracker/like', [
                RequestOptions::JSON => $like,
                RequestOptions::HEADERS => [
                    'Content-Type' => 'application/json',
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        }, Response::HTTP_NO_CONTENT);

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
