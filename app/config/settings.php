<?php

class settings
{
    protected $path;
    protected $settings;
    private static $instance;


    private function __construct()
    {
        $this->path = PROJECT_PATH . '/../settings.ini';
    }

    private function init()
    {
        $this->settings = parse_ini_file($this->path, true);
    }

    public function get($key)
    {
        if (is_null($this->settings)) {
            $this->init();
        }
        if (empty($this->settings)) {
            return null;
        }

        $keys = explode('/', $key);
        $settings = $this->settings;
        foreach ($keys as $key) {
            $settings = (array_key_exists($key, $settings)) ? $settings[$key] : null;
        }

        return $settings;
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
