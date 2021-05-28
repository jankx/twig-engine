<?php
namespace Jankx\Twig;

/**
 * TemplateFunctions class
 *
 * Define all TwigFunction can call in .twig template file
 */
class Functions
{
    protected $built_in_funcs = array(
        'action' => 'do_action',
        'filters' => 'apply_filters',
    );

    protected function getTemplateFunctions()
    {
        return array(
            'body_class' => function ($classes = '') {
                $classes = get_body_class($classes);
                return implode(' ', $classes);
            },
            'wp_title',
            'wp_head',
            'wp_footer'
        );
    }

    public function exe_function($function_name)
    {
        $args = func_get_args();
        array_shift($args);

        if (is_string($function_name)) {
            $function_name = trim($function_name);
        }
        return call_user_func_array($function_name, $args);
    }

    public function exe_component($component_name, $props = array(), $array_data = true)
    {
        $component = jankx_component($component_name, $props, false);
        if ($array_data) {
            return $component->buildComponentData();
        }
        return $component->render();
    }

    public function getAvailableFunctions()
    {
        $templateFunctions = $this->getTemplateFunctions();
        $userDefineFuncs   = apply_filters(
            'jankx_twig_engine_functions',
            array(
                'function'  => array(&$this, 'exe_function'),
                'component' => array(&$this, 'exe_component'),
            )
        );

        return array_merge($this->built_in_funcs, $templateFunctions, $userDefineFuncs);
    }
}
