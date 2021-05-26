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
        return array();
    }

    public function getAvailableFunctions()
    {
        $templateFunctions = $this->getTemplateFunctions();
        $userDefineFuncs   = apply_filters(
            'jankx_twig_engine_functions',
            array(
                'component' => 'jankx_component',
                'do_action',
                'jankx_template_has_footer',
            )
        );

        return array_merge($this->built_in_funcs, $templateFunctions, $userDefineFuncs);
    }
}
