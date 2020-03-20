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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeatureController extends BaseApiController
{
    /** @var string */
    private string $jinyaVersion;

    /**
     * @Route("/api/support/feature", methods={"POST"}, name="api_support_feature")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     *
     * @param Client $client
     * @return Response
     */
    public function submitAction(Client $client): Response
    {
        [$data, $status] = $this->tryExecute(function () use ($client) {
            /** @var User $user */
            $user = $this->getUser();
            $feature = [
                'title' => $this->getValue('title'),
                'details' => $this->getValue('details'),
                'who' => $user->getArtistName(),
            ];

            $response = $client->request('POST', 'https://api.jinya.de/tracker/feature', [
                RequestOptions::JSON => $feature,
                RequestOptions::HEADERS => [
                    'Content-Type' => 'application/json',
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
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
