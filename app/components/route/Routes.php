<?php
namespace Rest\Components\Route;

use Phalcon\Mvc\Micro\Collection;
use Phalcon\Mvc\Micro\CollectionInterface;

/**
 * Class Routes
 *
 * @package Rest\Components
 */
class Routes extends Collection implements IRoutes
{
    protected $collections = [];

    public function add($handler, $prefix, $routes, $lazy=false){
        $collection = new self();
        $collection->setPrefix($prefix);
        $collection->setHandler($handler, $lazy);
        $collection->cloneMap($this);
        $this->collections[] = $collection;
        $this->clearMap();
    }

    /**
     * Remove all routes
     */
    protected function clearMap(){
        $this->_handlers = [];
    }

    /**
     * Just set array to array with routes
     * @param $rout
     */
    protected function arrayToMap($rout){
        $this->_handlers[]=$rout;
    }

    /**
     * @param CollectionInterface $from
     */
    protected function cloneMap($from){
        $handlers = $from->getHandlers();
        if(is_array($handlers)){
            /** @var  $handler */
            foreach ($handlers as $handler){
                $this->arrayToMap($handler);
            }
        }
    }

    /**
     * @return Collection[]
     */
    public function getCollections()
    {
        return $this->collections;
    }
}