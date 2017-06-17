<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class MY_Loader
 * Adding entities to the core loader.
 */
class MY_Loader extends CI_Loader {

    protected $path = APPPATH . 'entities' . DIRECTORY_SEPARATOR;

    /**
     * Load an entity or array of entities.
     *
     * @param string|string[] $entity
     * @return $this
     */
    public function entity($entity)
    {
        if (empty($entity))
        {
            return $this;
        }

        if (is_array($entity))
        {
            array_map(array($this, __FUNCTION__), $entity);

            return $this;
        }

        if (!class_exists($entity, false))
        {
            $filename = $this->path . $entity . '.php';

            if (!file_exists($filename))
            {
                log_message('error', 'Unable to load the requested entity: ' . $entity);
                show_error('Unable to load the requested entity: ' . $entity);
            }

            require_once $filename;
        }

        return $this;
    }

    /**
     * Autoload entities defined in autoload.php config file.
     */
    protected function _ci_autoloader()
    {
        parent::_ci_autoloader();

        if (file_exists(APPPATH.'config/autoload.php'))
        {
            include(APPPATH.'config/autoload.php');
        }

        if (file_exists(APPPATH.'config/'.ENVIRONMENT.'/autoload.php'))
        {
            include(APPPATH.'config/'.ENVIRONMENT.'/autoload.php');
        }

        if ( ! isset($autoload))
        {
            return;
        }

        // Autoload entities
        if (isset($autoload['entity']))
        {
            $this->entity($autoload['entity']);
        }
    }
}