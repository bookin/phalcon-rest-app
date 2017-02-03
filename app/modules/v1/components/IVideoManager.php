<?php
namespace v1\components;

interface IVideoManager
{
    public function startTrim($filename);
    public function checkTrim($filename);
}