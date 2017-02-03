<?php
namespace v1\components;

use Rest\components\Component;

class CDNServer extends Component
{
    public function getServerKey(){
        return substr(md5(time()), 0, 10);
    }
}