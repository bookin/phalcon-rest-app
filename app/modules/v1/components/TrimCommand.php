<?php
namespace v1\components;


class TrimCommand
{
    protected $_server;

    public function __construct(IVideoManager $server)
    {
        $this->_server = $server;
    }

    public function execute($filename){
        $this->_server->startTrim($filename);
    }
}