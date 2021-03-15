<?php
use Jankx\Twig\TwigEngine;

class Jankx_Twig_Engine_Bootstrap {
    public function __construct() {
        add_filter('jankx_template_engines', array($this, 'registerTwigEngine'));
        add_filter('jankx_theme_template_engine', array($this, 'changeEngine'), 10, 2);
    }

    public function registerTwigEngine($engines) {
        if (!isset($engines[TwigEngine::ENGINE_NAME])) {
            $engines[TwigEngine::ENGINE_NAME] = TwigEngine::class;
        }
        return $engines;
    }

    public function changeEngine($currentEngine, $jankxFramework) {
        return TwigEngine::ENGINE_NAME;
    }
}

new Jankx_Twig_Engine_Bootstrap();
