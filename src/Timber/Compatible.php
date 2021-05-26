<?php
namespace Jankx\Twig\Timber;

use Jankx\Twig\Timber\Lib\Site;

/**
 * class Compatible
 *
 * This class use to support Timber synyax in Twig template.
 * This feature is not a Timber library it support Twig syntax like Timber only.
 */
class Compatible
{
    public function compatible($twig, $engine)
    {
        $twig->addGlobal('site', new Site());
    }
}
