<?php
namespace v1\components;

interface IVideoManager
{
    public function startTrim($video_id, $from, $to);
    public function checkTrim($video_id);
}