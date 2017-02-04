<?php
namespace v1\components;


class TrimCommand
{
    protected $_server;

    public function __construct(IVideoManager $server)
    {
        $this->_server = $server;
    }

    /**
     * @param \v1\models\Video $video
     * @return boolean
     */
    public function execute($video, $from, $to){
        return $this->_server->startTrim($video->filename, $from, $to);
    }
}