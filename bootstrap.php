<?php
use Jankx\Twig\TwigEngine;
use Jankx\Twig\Timber\Compatible;

class Jankx_Twig_Engine_Bootstrap
{
    public function __construct()
    {
        add_filter('jankx_template_engines', array($this, 'registerTwigEngine'));
        add_filter('jankx/template/engine/apply', array($this, 'changeEngine'), 10, 2);

        add_action('after_setup_theme', array($this, 'makeCompatibleWithTimber'));
    }

    public function registerTwigEngine($engines)
    {
        if (!isset($engines[TwigEngine::ENGINE_NAME])) {
            $engines[TwigEngine::ENGINE_NAME] = TwigEngine::class;
        }
        return $engines;
    }

    public function changeEngine()
    {
        return TwigEngine::ENGINE_NAME;
    }

    public function makeCompatibleWithTimber() {
        $timberCompatible = new Compatible();
        add_action(
            'jankx_setup_twig_environment',
            array($timberCompatible, 'compatible'),
            10,
            2
        );
    }
}

new Jankx_Twig_Engine_Bootstrap();
