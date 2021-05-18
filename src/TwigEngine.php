<?php
namespace Jankx\Twig;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use Jankx\TemplateEngine\Engine;
use Jankx\TemplateEngine\Data;

class TwigEngine extends Engine
{
    const ENGINE_NAME = 'twig';

    protected $id;
    protected $twig;
    protected $directories = array();

    protected $extension = 'twig';

    public function getName()
    {
        return static::ENGINE_NAME;
    }

    public static function isDebug()
    {
        return defined('JANKX_TWIG_ENGINE_DEBUG') && JANKX_TWIG_ENGINE_DEBUG;
    }

    public function setupEnvironment()
    {
        $this->twig = new Environment(
            new FilesystemLoader(array_reverse($this->directories)),
            array('cache' => $this->getTemplateCaches())
        );
        $functions = new TemplateFunctions();
        foreach ($functions->getAvailableFunctions() as $functionName => $function) {
            if (gettype($functionName) === 'integer') {
                $functionName = $function;
            }
            $this->twig->addFunction(new TwigFunction($functionName, $function));
        }
        if (static::isDebug()) {
            $this->twig->enableDebug();
        }
        do_action('jankx_setup_twig_environment', $this->twig, $this);
    }

    protected function getTemplateCaches()
    {
        if (static::isDebug()) {
            return false;
        }

        $cacheDir   = sprintf('%s/twig', rtrim(JANKX_CACHE_DIR, '/'));
        return apply_filters(
            'jankx_twig_engine_cache_directory',
            $cacheDir,
            $this->id,
            $this->directories,
            $this->args
        );
    }

    public function setDefaultTemplateDir($dir)
    {
        if (preg_match('/jankx\/core\/templates$/', $dir)) {
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
        $loader = $this->twig->getLoader();
        $paths = $loader->getPaths();
        foreach ($paths as $path) {
            if (is_array($templates)) {
                foreach ($templates as $template) {
                    $full_filename = sprintf('%s/%s.%s', $path, $template, $this->extension);
                    if (file_exists($full_filename)) {
                        return $full_filename;
                    }
                }
            } else {
                $full_filename = sprintf('%s/%s.%s', $path, $templates, $this->extension);
                if (file_exists($full_filename)) {
                    return $full_filename;
                }
            }

            return false;
        }
    }

    public function render($templates, $data = [], $echo = true)
    {
        // Merge local data with global data is shared
        $data = array_merge(Data::all(), $data);
        $error_msg = '';

        foreach ((array)$templates as $template) {
            try {
                $template = $this->twig->load(sprintf('%s.%s', $template, $this->extension));
                if (!$echo) {
                    return $template->render($data);
                }

                echo $template->render($data);
                return;
            } catch (\Twig\Error\LoaderError $e) {
                if (static::isDebug()) {
                    $error_msg = $e->getMessage();
                }
            }
        }
        error_log($error_msg);
    }

    public function isRenderDirectly()
    {
        return false;
    }

    public function getEngine()
    {
        return $this->twig;
    }
}
