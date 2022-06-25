Theme extensions
===

Apart from the link and configuration extensions Jinya CMS provides a few other extensions.

Menu extensions
---

There are currently three menu extensions available, one is to get the active menu item, one to check if the given menu
item is the active menu item and one to check if the given menu item has a child which is the active item.

getActiveMenuItem
~~~

This extension gives you access to the currently active menu item based on its route. It is simply invoked by
calling ``$this->getActiveMenuItem()`` in your theme. It returns an instance of ``MenuItem``.

isActiveMenuItem
~~~

This extension allows you to check if any menu item is the currently active menu item based on its route. It is simply
invoked by calling ``$this->isActiveMenuItem($menuItem)`` in your theme. It returns a boolean indicating whether the menu
item you gave is active or not.

isChildActiveMenuItem
~~~

This extension allows you to check if any menu item is the currently active menu item based on its route. It is simply
invoked by calling ``$this->isActiveMenuItem($menuItem)`` in your theme. It returns a boolean indicating whether the menu
item you gave is active or not.

File extensions
---

There are two file extensions as of now. Both of them are used to render resolution dependent images. Either using a
picture tag or using the srcset attribute of either img or picture tags.

srcset and sizes
~~~

This extension automatically creates the needed value for the srcset attribute of img tags. The srcset functions takes
a ``File`` as parameter. It also accepts an optional parameter to convert the image to a different file type. The
parameter is of type ``ImageType``.

To make browsers pick up the proper file, you also have to use the sizes function.

Check below to see how to use them:

.. code-block:: php

    <img srcset="<?= $this->srcset($file, \App\Utils\ImageType::Webp) ?>" sizes="<?= $this->sizes() ?>">

pictureSources
---

This extension gives you a simple way to generate a variety of source tags for a give file. The extension takes a ``File``
and image types. The image types are values of the ``ImageType`` enum. Possible values are as follows:

.. code-block:: php

    \App\Utils\ImageType::Webp
    \App\Utils\ImageType::Png
    \App\Utils\ImageType::Jpg
    \App\Utils\ImageType::Gif
    \App\Utils\ImageType::Bmp

Check below to see how to use it:

.. code-block:: php

    <picture>
        <?= $this->pictureSources($file, \App\Utils\ImageType::Webp, \App\Utils\ImageType::Png) ?>
    </picture>

This code generates the following html code:

.. code-block:: html

    <picture>
        <source srcset="/api/media/file/747/content?width=480&type=webp" media="(min-width: 480px)" type="image/webp">
        <source srcset="/api/media/file/747/content?width=720&type=webp" media="(min-width: 720px)" type="image/webp">
        <source srcset="/api/media/file/747/content?width=768&type=webp" media="(min-width: 768px)" type="image/webp">
        <source srcset="/api/media/file/747/content?width=800&type=webp" media="(min-width: 800px)" type="image/webp">
        <source srcset="/api/media/file/747/content?width=864&type=webp" media="(min-width: 864px)" type="image/webp">
        <source srcset="/api/media/file/747/content?width=900&type=webp" media="(min-width: 900px)" type="image/webp">
        <source srcset="/api/media/file/747/content?width=1024&type=webp" media="(min-width: 1024px)" type="image/webp">
        <source srcset="/api/media/file/747/content?width=1080&type=webp" media="(min-width: 1080px)" type="image/webp">
        <source srcset="/api/media/file/747/content?width=2160&type=webp" media="(min-width: 2160px)" type="image/webp">
        <source srcset="/api/media/file/747/content?width=4320&type=webp" media="(min-width: 4320px)" type="image/webp">
        <source srcset="/api/media/file/747/content?width=480&type=png" media="(min-width: 480px)" type="image/png">
        <source srcset="/api/media/file/747/content?width=720&type=png" media="(min-width: 720px)" type="image/png">
        <source srcset="/api/media/file/747/content?width=768&type=png" media="(min-width: 768px)" type="image/png">
        <source srcset="/api/media/file/747/content?width=800&type=png" media="(min-width: 800px)" type="image/png">
        <source srcset="/api/media/file/747/content?width=864&type=png" media="(min-width: 864px)" type="image/png">
        <source srcset="/api/media/file/747/content?width=900&type=png" media="(min-width: 900px)" type="image/png">
        <source srcset="/api/media/file/747/content?width=1024&type=png" media="(min-width: 1024px)" type="image/png">
        <source srcset="/api/media/file/747/content?width=1080&type=png" media="(min-width: 1080px)" type="image/png">
        <source srcset="/api/media/file/747/content?width=2160&type=png" media="(min-width: 2160px)" type="image/png">
        <source srcset="/api/media/file/747/content?width=4320&type=png" media="(min-width: 4320px)" type="image/png">
    </picture>