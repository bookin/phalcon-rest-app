<?php
namespace v1\components;


class VideoManager implements IVideoManager
{

    public function startTrim($video_id, $from, $to){
        return true;
    }

    public function checkTrim($video_id){}
}