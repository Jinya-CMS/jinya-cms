<?php

namespace App\Web\Actions\Theme;

use App\Database;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Theming;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/theme/{id}/styling', JinyaAction::GET)]
#[Authenticated(Authenticated::WRITER)]
#[OpenApiRequest('This action gets the styling variables of the given theme')]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiResponse('Successfully got the style variables', example: [
    '$jinya-debug' => 'false',
    '$white' => '#fff',
    '$black' => '#000',
    '$primary' => '#504a56',
    '$secondary' => '#966554',
    '$positive' => '#146621',
    '$info' => '#182B70',
    '$negative' => '#5C0B0B',
    '$link-color-primary' => '$primary-dark',
    '$link-hover-color-primary' => '$primary-darker',
    '$breakpoint-small-max-width' => '799px',
    '$breakpoint-medium-min-width' => '800px',
    '$breakpoint-medium-max-width' => '1365px',
    '$breakpoint-large-min-width' => '1366px',
    '$breakpoint-large-max-width' => '1919px',
    '$breakpoint-extra-large-min-width' => '1920px',
    '$menu-font-family' => '\'Open Sans\', \'sans serif\'',
    '$paragraph-font-family' => '\'Open Sans\', \'sans serif\'',
    '$heading-font-family' => 'Raleway, \'sans serif\'',
    '$brand-font-family' => '\'Josefin Sans\', \'sans serif\'',
    '$jinya-header-font-size' => '1.5rem',
    '$jinya-header-color' => '$primary',
    '$jinya-header-border-bottom-color' => '$secondary',
    '$jinya-header-padding' => '1rem',
    '$blockquote-color' => '$secondary-light',
    '$jinya-brand-font-size' => '$jinya-header-font-size',
    '$jinya-brand-font-family' => '$brand-font-family',
    '$jinya-brand-color' => '$jinya-header-color',
    '$jinya-brand-padding' => '0.5rem 0 0.5rem 0.5rem',
    '$jinya-menu-font-size' => '$jinya-header-font-size',
    '$jinya-menu-font-family' => '$menu-font-family',
    '$jinya-menu-item-color' => '$secondary',
    '$jinya-menu-item-active-color' => '$primary',
    '$jinya-menu-item-hover-color' => '$secondary-darker',
    '$jinya-menu-item-padding' => '0.5rem',
    '$jinya-menu-item-mobile-color' => '$primary-dark',
    '$jinya-submenu-margin' => '2rem -0.5rem 0',
    '$jinya-submenu-item-color' => '$secondary-darker',
    '$jinya-submenu-item-active-color' => '$primary',
    '$jinya-submenu-item-hover-color' => '$secondary-darkest',
    '$jinya-submenu-item-padding' => '0.5rem',
    '$jinya-submenu-item-mobile-color' => '$secondary-dark',
    '$jinya-footer-padding' => '0.5rem 0',
    '$jinya-footer-margin' => '2rem 0',
    '$jinya-footer-border-top-color' => '$secondary-light',
    '$jinya-footer-menu-item-padding' => '0.5rem',
    '$jinya-footer-menu-item-color' => '$secondary-dark',
    '$jinya-footer-menu-item-hover-color' => '$secondary-darkest',
    '$jinya-footer-menu-item-font-family' => '$menu-font-family',
    '$jinya-vertical-gallery-artwork-width' => 'calc(50% - 0.5rem)',
    '$jinya-vertical-gallery-caption-width' => 'calc(50% - 0.5rem)',
    '$jinya-vertical-gallery-caption-font-weight' => 'lighter',
    '$jinya-vertical-gallery-caption-font-size' => '2rem',
    '$jinya-vertical-gallery-artwork-border-bottom-color' => '$secondary-lightest',
    '$jinya-form-center-items' => 'true',
    '$jinya-page-title-font-family' => '$heading-font-family',
    '$jinya-page-title-font-size' => '3rem',
    '$jinya-page-center-title' => 'true',
    '$jinya-form-input-width' => '30rem',
    '$jinya-form-input-font-family' => '$paragraph-font-family',
    '$jinya-form-input-font-size' => '0.875rem',
    '$jinya-form-input-border-color' => '$primary',
    '$jinya-form-input-padding' => '0.5em',
    '$jinya-form-input-border-radius' => '5px',
    '$jinya-form-input-invalid-color' => '$negative',
    '$jinya-form-input-background-color' => '$white',
    '$jinya-form-button-border-radius' => '5px',
    '$jinya-form-button-padding' => '0.5rem 1rem',
    '$jinya-form-button-primary-color' => '$primary',
    '$jinya-form-button-primary-color-light' => '$primary-light',
    '$jinya-form-button-white-color' => '$white'
], exampleName: 'Styling for jinya default theme', map: true)]
#[OpenApiResponse('Theme not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Theme not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class GetStyleVariablesAction extends ThemeAction
{
    /**
     * {@inheritDoc}
     * @throws Database\Exceptions\ForeignKeyFailedException
     * @throws Database\Exceptions\InvalidQueryException
     * @throws Database\Exceptions\UniqueFailedException
     * @throws NoResultException
     */
    public function action(): Response
    {
        $themeId = $this->args['id'];
        $dbTheme = Database\Theme::findById($themeId);
        if (!$dbTheme) {
            throw new NoResultException($this->request, 'Theme not found');
        }

        $theme = new Theming\Theme($dbTheme);
        $vars = $theme->getStyleVariables();
        $dbVars = $dbTheme->scssVariables;

        return $this->respond(array_merge($vars, $dbVars));
    }
}
