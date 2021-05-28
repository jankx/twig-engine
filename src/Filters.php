<?php
namespace Jankx\Twig;

class Filters
{
    public function filter_component()
    {
    }

    public function getFilters()
    {
        return array(
            'component' => array(&$this, 'filter_component'),
        );
    }
}
