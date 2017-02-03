<?php
namespace v1\Controllers;


use Rest\components\RestController;
use Rest\Components\RestException;
use Rest\models\Tokens;

class AuthController extends RestController
{
    /**
     * Fake action for get new token
     * @return mixed
     * @throws RestException
     */
    public function getToken(){
        $model = new Tokens();
        if(!$model->save()){
            throw new RestException(500, ['devMessage'=>$model->getMessages()]);
        }else{
            return $this->response(['user_id'=>$model->_id, 'token'=>$model->token]);
        }
    }
}