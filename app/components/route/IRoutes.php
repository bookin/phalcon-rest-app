<?php
namespace Rest\Components\Route;

use Phalcon\Mvc\Micro\Collection;

interface IRoutes
{
    /**
     * @return Collection[]
     */
    public function getCollections();
}