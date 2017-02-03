<?php

namespace Rest\Components;

use Phalcon\Config;
use Phalcon\Exception;
use Phalcon\Mvc\ModuleDefinitionInterface;

class Loader {
    /**
     * @var \Phalcon\DI
     */
    protected $di;

    /**
     * @var \Phalcon\Config\;
     */
    protected $config;

    /**
     * @param \Phalcon\DiInterface $di
     * @throws Exception
     */
    public function __construct(\Phalcon\DiInterface $di) {
        try {
            $this->di = $di;
            $this->config = $this->di->get('config');
        }
        catch(Exception $e){
            throw new Exception($e->getMessage(),$e->getCode());
        }
    }

    public function loadModules(){
        $loader = new \Phalcon\Loader();
        /* Get modules from config */
        if(!empty($this->config->rest->modules) && is_object($this->config->rest->modules)){
            foreach($this->config->rest->modules as $index=>$module){
                if($module instanceof Config){
                    /* Check Module class by path */
                    $class = $module['path'].DIRECTORY_SEPARATOR.'Module.php';
                    if($module['enable'] && file_exists($class)){
                        /* Register module namespace */
                        $loader->registerNamespaces([$module['namespaces']=>$module['path']])->register();
                        $className = $module['namespaces'].'\Module';
                        /* Init module */
                        $module = new $className();
                        if($module instanceof ModuleDefinitionInterface){
                            $module->registerAutoloaders($this->di);
                            $module->registerServices($this->di);
                        }
                    }
                }
            }
        }
    }
}