<?php

use Jinya\Cms\Configuration\JinyaConfiguration;
use Jinya\Cms\Database\File;
use Jinya\Cms\Storage\ConversionService;
use Jinya\Cms\Storage\StorageBaseService;
use Jinya\Cms\Theming\Extensions\FileExtension;
use Jinya\Cms\Utils\ImageType;

require_once __DIR__ . '/startup.php';

function handle_images(bool $exit = true): void
{
    // Avoid PSR-7 object creation: we only need query params.
    $idRaw = $_GET['id'] ?? null;
    if ($idRaw === null || $idRaw === '' || !is_numeric($idRaw)) {
        http_response_code(404);
        if ($exit) {
            exit(404);
        }
        return;
    }

    $id = (int)$idRaw;

    // Cache configuration defaults + WEBP support detection across requests (great in FrankenPHP worker mode).
    static $defaultWidth = null;
    static $defaultType = null;

    if ($defaultWidth === null) {
        $defaultWidth = (int)JinyaConfiguration::getConfiguration()->get(
            'default_width',
            'image_cache',
            FileExtension::RESOLUTIONS_FOR_SOURCE[2]
        );
    }

    if ($defaultType === null) {
        $webpSupported = false;
        if (class_exists(Imagick::class, false)) {
            // queryFormats() is expensive; do it once.
            $webpSupported = !empty(Imagick::queryFormats('WEBP'));
        }

        $defaultType = JinyaConfiguration::getConfiguration()->get(
            'default_type',
            'image_cache',
            ($webpSupported ? ImageType::Webp : ImageType::Jpg)->string()
        );
    }

    $width = isset($_GET['width']) ? (int)$_GET['width'] : $defaultWidth;
    $type = isset($_GET['type']) ? (string)$_GET['type'] : $defaultType;

    $file = File::findById($id);
    if ($file === null) {
        http_response_code(404);
        if ($exit) {
            exit(404);
        }
        return;
    }

    $relativeTarget = $file->path . '-' . $width . 'w.' . $type;
    $fullpath = StorageBaseService::BASE_PATH . '/public/' . $relativeTarget;

    if (is_file($fullpath)) {
        header('Location: ' . $relativeTarget, true, 302);
        if ($exit) {
            exit(302);
        }
        return;
    }

    // Slow path: conversion. Consider offloading/queueing instead of doing it inline.
    try {
        $conversionService = new ConversionService();
        $conversionService->convertFile($id);
    } catch (Throwable) {
        http_response_code(500);
        if ($exit) {
            exit(500);
        }
        return;
    }

    header('Location: ' . $relativeTarget, true, 302);
    if ($exit) {
        exit(302);
    }
}
