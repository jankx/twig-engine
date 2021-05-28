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
    protected static $compatible = false;

    public static function isCompatible()
    {
        return static::$compatible;
    }

    public function compatible($twig, $engine)
    {
        if (static::isCompatible()) {
            return;
        }

        $twig->addGlobal('site', new Site());

        add_filter(
            'jankx_twig_context',
            array($this, 'compatibleTimberContext')
        );

        class_alias(JankxTimber::class, Timber::class);

        static::$compatible = true;
    }

    public function compatibleTimberContext($context)
    {
        return apply_filters('timber/context', $context);
    }
}
