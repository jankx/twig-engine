<?php
namespace Jankx\Twig;

use Jankx\TemplateEngine\Context;
use Jankx\TemplateEngine\Engine;
use Jankx\TemplateEngine\Filters;
use Jankx\TemplateEngine\Functions;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use Twig\TwigFilter;

class TwigEngine extends Engine
{
    const ENGINE_NAME = 'twig';

    protected $id;

    /**
     * @var \Twig\Environment
     */
    protected $twig;

    protected $directories = array();

    protected $extension = 'twig';

    /**
     * @var \Jankx\Twig\Twig
     */
    protected $functions;

    public function getName()
    {
        return static::ENGINE_NAME;
    }

    public static function isDebug()
    {
        return defined('JANKX_TWIG_ENGINE_DEBUG') && constant('JANKX_TWIG_ENGINE_DEBUG') === true;
    }

    public static function isDisableCache()
    {
        return defined('JANKX_DISABLE_TWIG_CACHE')
            && constant('JANKX_DISABLE_TWIG_CACHE') === true;
    }

    public function setupEnvironment()
    {
        $this->twig = new Environment(
            new FilesystemLoader(array_reverse($this->directories)),
            array('cache' => $this->getTemplateCaches())
        );
        $functions = new Functions();
        foreach ($functions->getAvailableFunctions() as $functionName => $function) {
            if (gettype($functionName) === 'integer') {
                $functionName = $function;
            }
            $this->registerFunction($functionName, $function);
        }
        if (static::isDebug()) {
            $this->twig->addFunction(new TwigFunction('var_dump', 'var_dump'));
            $this->twig->enableDebug();
        }

        $filters = new Filters();
        foreach ($filters->getFilters() as $filterName => $filter) {
            if (gettype($filterName) === 'integer') {
                $filterName = $filter;
            }
            $this->twig->addFilter(new TwigFilter($filterName, $filter));
        }

        do_action("jankx_setup_{$this->id}_twig_environment", $this->twig, $this);
        do_action('jankx_setup_twig_environment', $this->twig, $this);
    }

    protected function getTemplateCaches()
    {
        if (static::isDisableCache()) {
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
        if (preg_match('/jankx\/core\/templates$/', str_replace(DIRECTORY_SEPARATOR, '/', $dir))) {
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
        /**
         * @var Twig\Loader\LoaderInterface;
         */
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

    public function render($templates, $context = [], $echo = true)
    {
        // Merge local data with global data is shared
        $context = array_merge(Context::get(), $context);
        $context = apply_filters('jankx_twig_context', $context, $templates);
        $error_msg = '';

        foreach ((array)$templates as $template) {
            try {
                $template = $this->twig->load(sprintf('%s.%s', $template, $this->extension));
                if (!$echo) {
                    return $template->render($context);
                }

                echo $template->render($context);
                return;
            } catch (\Twig\Error\LoaderError $e) {
                if (static::isDebug()) {
                    $error_msg = $e->getMessage();
                }
            }
        }
        error_log($error_msg);
    }

    /**
     * @return boolean
     */
    public function isDirectRender()
    {
        return false;
    }

    /**
     * @return \Twig\Environment
     */
    public function getEngine()
    {
        return $this->twig;
    }

    /**
     * @param string $name The function name
     * @param callable $callback
     *
     * @return void
     */
    public function registerFunction($name, $callback, $options = [])
    {
        // If the callback is can not call. The function is skipped
        if (!is_callable($callback)) {
            return;
        }
        $this->twig->addFunction(new TwigFunction($name, $callback, $options));
    }
}
