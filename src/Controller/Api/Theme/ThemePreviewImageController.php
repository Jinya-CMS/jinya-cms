<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.03.2018
 * Time: 08:12
 */

namespace Jinya\Controller\Api\Theme;


use Jinya\Framework\BaseApiController;
use Jinya\Services\Theme\ThemeServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function file_get_contents;

class ThemePreviewImageController extends BaseApiController
{
    /**
     * @Route("/api/theme/{name}/preview", methods={"GET"}, name="api_theme_preview_get")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $name
     * @param ThemeServiceInterface $themeService
     * @return Response
     */
    public function getAction(string $name, ThemeServiceInterface $themeService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($name, $themeService) {
            $theme = $themeService->getTheme($name);

            if (empty($theme->getPreviewImage())) {
                throw new FileNotFoundException($theme->getName());
            }

            return $theme->getPreviewImage();
        });

        if ($status !== 200) {
            return $this->json($data, $status);
        } else {
            return $this->file(file_get_contents($data));
        }
    }
}