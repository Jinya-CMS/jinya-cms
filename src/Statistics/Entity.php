<?php

namespace App\Statistics;

use App\Database\Utils\LoadableEntity;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Helper class to get the share of entities in the database
 * @codeCoverageIgnore
 */
abstract class Entity
{
    /**
     * Gets the count of files, galleries, simple pages, segment pages, forms, blog posts and blog categories
     *
     * @return array{files: int, galleries: int, simplePages: int, segmentPages: int, forms: int, blogPosts: int, blogCategories: int}
     */
    #[ArrayShape([
        'files' => 'int',
        'galleries' => 'int',
        'simplePages' => 'int',
        'segmentPages' => 'int',
        'forms' => 'int',
        'blogPosts' => 'int',
        'blogCategories' => 'int',
    ])] public static function getEntityShare(): array
    {
        $fileCount = 'SELECT COUNT(*) FROM file';
        $galleryCount = 'SELECT COUNT(*) FROM gallery';
        $simplePageCount = 'SELECT COUNT(*) FROM page';
        $segmentPageCount = 'SELECT COUNT(*) FROM segment_page';
        $formCount = 'SELECT COUNT(*) FROM form';
        $blogPostCount = 'SELECT COUNT(*) FROM blog_post';
        $blogCategoryCount = 'SELECT COUNT(*) FROM blog_category';

        return [
            'files' => (int)LoadableEntity::fetchColumn($fileCount),
            'galleries' => (int)LoadableEntity::fetchColumn($galleryCount),
            'simplePages' => (int)LoadableEntity::fetchColumn($simplePageCount),
            'segmentPages' => (int)LoadableEntity::fetchColumn($segmentPageCount),
            'forms' => (int)LoadableEntity::fetchColumn($formCount),
            'blogPosts' => (int)LoadableEntity::fetchColumn($blogPostCount),
            'blogCategories' => (int)LoadableEntity::fetchColumn($blogCategoryCount),
        ];
    }
}