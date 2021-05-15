<?php
namespace Jankx\Twig;

/**
 * TemplateFunctions class
 *
 * Define all TwigFunction can call in .twig template file
 */
class TemplateFunctions
{
    protected $built_in_funcs = array(
    );

    protected function getTemplateFunctions()
    {
        return array(
            'bloginfo' => 'get_bloginfo',
            'wp_title',
            'wp_head',
            'wp_footer',
            'wp_body_open',
            'body_class',
            'is_active_sidebar',
            'dynamic_sidebar',
            'jankx_template',
            'jankx_open_container',
            'jankx_close_container'
        );
    }

    public function getAvailableFunctions()
    {
        $templateFunctions = $this->getTemplateFunctions();
        $userDefineFuncs   = apply_filters(
            'jankx_twig_engine_functions',
            array(
                'do_action',
                'jankx_template_has_footer',
                'jankx_component'
            )
        );

        $funcs = array_merge($this->built_in_funcs, $templateFunctions, $userDefineFuncs);

        return array_unique($funcs);
    }
}
