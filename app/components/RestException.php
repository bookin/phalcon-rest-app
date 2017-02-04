<?php
namespace Rest\Components;

use Rest\Components\Response\JsonResponse;
use Rest\Components\Response\Response;

class RestException extends \Exception{

    public $devInfo;
    public $devCode;
    public $devMessage;
    public $response;

    /**
     * RestException constructor.
     * @param int $code
     * @param array $errorArray
     */
    public function __construct($code, $errorArray=[]){
        $this->code = $code;
        $this->message = $this->response = Response::getStatusDescription($code);
        $this->devCode = @$errorArray['devCode'];
        $this->devMessage = @$errorArray['devMessage'];
        $this->devInfo = @$errorArray['devInfo'];
    }

    public function send(){
        $di = \Phalcon\DI::getDefault();

        $res = $di->get('response');
        $req = $di->get('request');

        //query string, filter, default
        if(!$req->get('suppress_response_codes', null, null)){
            $res->setStatusCode($this->getCode(), $this->response)->sendHeaders();
        } else {
            $res->setStatusCode('200', 'OK')->sendHeaders();
        }

        $error = array(
            'code' => $this->getCode(),
            'description' => $this->getMessage()
        );

        if(APP_ENV == 'dev'){
            $error['devCode'] = $this->devCode;
            $error['devMessage'] = $this->devMessage;
            $error['devInfo'] = $this->devInfo;
            $error['file'] = [
                'path'=>$this->getFile(),
                'line'=>$this->getLine()
            ];
        }


        if(!$req->get('type') || $req->get('type') == 'json'){
            $response = new JsonResponse();
            $response->send($error, true);
        }else{
            /* TODO - Add another types */
        }

        error_log('HTTPException: '.$this->getFile().' at '.$this->getLine());

        return true;
    }
}