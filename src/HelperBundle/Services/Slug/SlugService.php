<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 09.11.2017
 * Time: 19:37
 */

namespace HelperBundle\Services\Slug;


class SlugService implements SlugServiceInterface
{

    /**
     * @inheritdoc
     */
    public function generateSlug(string $name): string
    {
        $slug = transliterator_transliterate("Any-Latin; NFD; [:Nonspacing Mark:] Remove; NFC; [:Punctuation:] Remove; Lower();", $name);
        $slug = preg_replace('/[-\s]+/', '-', $slug);
        return trim($slug, '-');
    }
}