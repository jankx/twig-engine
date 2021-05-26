<?php
namespace Jankx\Twig\Timber;

use Jankx;
use Jankx\Twig\Timber\Lib\Loader;

class Timber
{
    public static function render($filenames, $data = array(), $expires = false, $cache_mode = Loader::CACHE_USE_DEFAULT)
    {
        return Jankx::render($filenames);
    }

    public static function get_sidebar($sidebar_name, $sidebar_context = array())
    {
    }

    // dynamic_sidebar
    public static function get_widgets()
    {
    }
}
