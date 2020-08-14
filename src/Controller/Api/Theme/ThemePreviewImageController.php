<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.03.2018
 * Time: 08:12
 */

namespace Jinya\Controller\Api\Theme;

use Jinya\Entity\Theme\Theme;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Theme\ThemeServiceInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThemePreviewImageController extends BaseApiController
{
    /**
     * @Route("/api/theme/{name}/preview", methods={"GET"}, name="api_theme_preview_get")
     */
    public function getAction(string $name, ThemeServiceInterface $themeService): Response
    {
        /** @var $data Theme|array */
        [$data, $status] = $this->tryExecute(static function () use ($name, $themeService) {
            $theme = $themeService->getTheme($name);

            if (empty($theme->getPreviewImage())) {
                throw new FileNotFoundException($theme->getName());
            }

            return $theme;
        });

        if (200 !== $status) {
            return $this->json($data, $status);
        }

        return $this->file($data->getPreviewImage(), $data->getDisplayName() . '.jpg');
    }
}
