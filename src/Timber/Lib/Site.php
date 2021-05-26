<?php
namespace Jankx\Twig\Timber\Lib;

class Site
{
    protected static $supportFields = array(
        'name' => 'name',
        'title' => 'name',
        'rdf' => 'rdf_url',
        'rss' => 'rss_url',
        'rss2' => 'rss2_url',
        'atom' => 'atom_url',
        'charset' => 'charset',
        'pingback' => 'pingback_url',
        'pingback_url' => 'pingback_url',
        'description' => 'description',
        'admin_email' => 'admin_email',
        'language' => true,
        'url' => true,
        'home_url' => true,
        'site_url' => true,
        'theme' => true,
        'multisite' => true,
        'blog_id' => true,
    );

    public function __isset($name)
    {
        return in_array($name, array_keys(static::$supportFields));
    }

    public function __get($name)
    {
        switch ($name) {
            case 'language':
                return $this->$name = $this->language_attributes();
            case 'blog_id':
            case 'id':
                return $this->$name = get_current_blog_id();
            case 'url':
            case 'home_url':
                return $this->$name = home_url();
            case 'site_url':
                return $this->$name = site_url();
            case 'theme':
                return $this->$name = new Theme();
            case 'multisite':
                return $this->$name = is_multisite();
            default:
                return $this->$name = get_bloginfo($name);
        }
    }

    public function language_attributes()
    {
        return get_language_attributes();
    }

    public function link()
    {
        return $this->__get('url');
    }

    public function meta($field)
    {
        return $this->__get($field);
    }

    public function update($key, $value)
    {
        $value = apply_filters('timber_site_set_meta', $value, $key, $this->ID, $this);
        if (is_multisite()) {
            update_blog_option($this->ID, $key, $value);
        } else {
            update_option($key, $value);
        }
        $this->$key = $value;
    }

    public function url()
    {
        return $this->link();
    }
}
