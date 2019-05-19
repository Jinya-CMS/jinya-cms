<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 09.11.2017
 * Time: 19:37
 */

namespace Jinya\Services\Slug;

use Behat\Transliterator\Transliterator;
use function function_exists;
use function transliterator_transliterate;

class SlugService implements SlugServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function generateSlug(string $name): string
    {
        if (function_exists('transliterator_transliterate')) {
            $slug = transliterator_transliterate(
                'Any-Latin; NFD; [:Nonspacing Mark:] Remove; NFC; [:Punctuation:] Remove; Lower();',
                $name
            );
            $slug = preg_replace('/[-\s]+/', '-', $slug);
        } else {
            $slug = Transliterator::transliterate($name, '-');
        }

        return trim($slug, '-');
    }
}
