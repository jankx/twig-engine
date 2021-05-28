<?php
namespace Jankx\Twig\Timber;

use Jankx;
use Jankx\Twig\Timber\Lib\Loader;

class Timber
{
    public static function render($filenames, $data = array(), $expires = false, $cache_mode = Loader::CACHE_USE_DEFAULT)
    {
        if (is_array($filenames)) {
            $filenames = array_map(function ($filename) {
                return str_replace('.twig', '', $filename);
            }, $filenames);
        } else {
            $filenames = str_replace('.twig', '', $filenames);
        }
        Jankx::render($filenames, $data, true);
    }

    public static function get_sidebar($sidebar_name, $sidebar_context = array())
    {
    }

    // dynamic_sidebar
    public static function get_widgets()
    {
    }
}
