<?php
namespace Rest\models;

use Rest\Components\Model;

class Tokens extends Model
{
    public $token;

    public function beforeSave()
    {
        $this->token = crypt(md5(microtime()));
    }

    public function getId(){
        return (string)$this->_id;
    }

    public static function getUserById($id){
        return self::findFirst(['user_id'=>$id])?:null;
    }

}