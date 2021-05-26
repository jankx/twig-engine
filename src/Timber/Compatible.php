<?php
namespace Jankx\Twig\Timber;

use Jankx\Twig\Timber\Lib\Site;
use Jankx\Twig\Timber\Timber as JankxTimber;
use Timber\Timber;

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

        add_filter(
            'jankx_twig_context',
            array($this, 'compatibleTimberContext')
        );

        class_alias(JankxTimber::class, Timber::class);
    }

    public function compatibleTimberContext($context)
    {
        return apply_filters('timber/context', $context);
    }
}
