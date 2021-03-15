<?php
namespace Jankx\Twig;

use Jankx\Template\Engine\Engine;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\Error\LoaderError;

class TwigEngine extends Engine
{
    const ENGINE_NAME = 'twig';

    protected $twig;
    protected $directories = array();

    protected $extension = 'twig';

    public function __construct($id, $template_directory, $template_location, $args)
    {
        parent::__construct($id, $template_directory, $template_location, $args);

        $cacheDir   = sprintf('%s/caches/twig', constant('WP_CONTENT_DIR'));
        $loader     = new FilesystemLoader(array_reverse($this->directories));
        $this->twig = new Environment($loader, array('cache' => $this->getTemplateCaches()));
    }

    public static function isDebug() {
        return defined('JANKX_TWIG_ENGINE_DEBUG') && JANKX_TWIG_ENGINE_DEBUG;
    }

    protected function getTemplateCaches()
    {
        if (static::isDebug()) {
            return false;
        }

        return apply_filters(
            'jankx_twig_engine_cache_directory',
            $cacheDir,
            $id,
            $template_directory,
            $template_location,
            $args
        );
    }

    public function setDefaultTemplateDir($dir)
    {
        if (preg_match('/jankx\/template\/default$/', $dir)) {
            array_push($this->directories, sprintf('%s/twig', dirname(__DIR__)));
        } else {
            array_push($this->directories, $dir);
        }
    }

    public function setDirectoryInTheme($dirName)
    {
        $templateDirectory = sprintf('%s/%s', get_template_directory(), $dirName);
        if (file_exists($templateDirectory)) {
            array_push($this->directories, $templateDirectory);
        }

        if (is_child_theme()) {
            $stylesheetDirectory = sprintf('%s/%s', get_stylesheet_directory(), $dirName);
            if (file_exists($stylesheetDirectory)) {
                array_push($this->directories, $stylesheetDirectory);
            }
        }
    }

    public function searchTemplate($templates)
    {
    }

    public function render($templates, $data = [], $echo = true)
    {
        foreach ((array)$templates as $template) {
            try {
                $template = $this->twig->load(sprintf('%s.%s', $template, $this->extension));
                if (!$echo) {
                    return $template->render($data);
                }

                echo $template->render($data);
                break;
            } catch(\Twig\Error\LoaderError $e) {
                if (static::isDebug()) {
                    error_log($e->getMessage());
                }
            }
        }
    }
}
