<?php
namespace Rest\components;

use Phalcon\Config;
use Phalcon\DiInterface;
use Phalcon\Mvc\ModuleDefinitionInterface;

class BaseModule implements ModuleDefinitionInterface
{
    /**
     * Registers an autoloader related to the module
     *
     * @param DiInterface $di
     */
    public function registerAutoloaders(DiInterface $di = null)
    {}

    /**
     * Registers services related to the module
     *
     * @param DiInterface $di
     */
    public function registerServices(DiInterface $di)
    {
        /**
         * Try to load local routers
         */
        $routersConfig = $this->getFilePath('/config/routers.php');
        if (file_exists($routersConfig)) {
            $di->set('collections', function () use($routersConfig){
                $routers = include($routersConfig);
                return $routers;
            });
        }

        /**
         * Try to load local configuration
         */
        if (file_exists(__DIR__ . '/config/config.php')) {

            $config = $di->get('config');
            $override = new Config(include __DIR__ . '/config/config.php');

            if ($config instanceof Config) {
                $config->merge($override);
            } else {
                $di->set('config',$config);
            }
        }
    }

    public function getFilePath($file) {
        $reflection = new \ReflectionClass($this);
        $directory = dirname($reflection->getFileName());

        return $directory . $file;
    }
}