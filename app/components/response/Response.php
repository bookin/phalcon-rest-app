<?php
namespace Rest\Components\Response;

use Phalcon\Di;

abstract class Response extends \Phalcon\DI\Injectable{

    protected $content;

    protected $snake = false;
    protected $envelope = true;

    const RESPONSE_SUCCESS = 'success';
    const RESPONSE_ERROR = 'error';

    protected $head = false;

    protected $response;

    public function __construct(){
        $di = Di::getDefault();
        $this->setDI($di);
        if(strtolower($this->di->get('request')->getMethod()) === 'head'){
            $this->head = true;
        }
        $this->setResponse($this->di->get('response'));
    }

    /**
     * In-Place, recursive conversion of array keys in snake_Case to camelCase
     * @param  array $snakeArray Array with snake_keys
     * @return array
     */
    protected function arrayKeysToSnake($snakeArray=[]){
        foreach($snakeArray as $k=>$v){
            if (is_array($v)){
                $v = $this->arrayKeysToSnake($v);
            }
            $snakeArray[$this->snakeToCamel($k)] = $v;
            if($this->snakeToCamel($k) != $k){
                unset($snakeArray[$k]);
            }
        }
        return $snakeArray;
    }

    /**
     * Replaces underscores with spaces, uppercases the first letters of each word,
     * lowercases the very first letter, then strips the spaces
     * @param string $val String to be converted
     * @return string  Converted string
     */
    protected function snakeToCamel($val) {
        return str_replace(' ', '', lcfirst(ucwords(str_replace('_', ' ', $val))));
    }

    /**
     * @param $object \Phalcon\Http\Response
     */
    public function setResponse($object){
        $this->response = $object;
    }

    /**
     * @return \Phalcon\Http\Response
     */
    public function getResponse(){
        return $this->response;
    }

    /**
     * @param $snake
     * @return $this
     */
    public function isSnakeCase($snake){
        $this->snake = (bool) $snake;
        return $this;
    }

    /**
     * @param $envelope
     * @return $this
     */
    public function isEnvelope($envelope){
        $this->envelope = (bool) $envelope;
        return $this;
    }

    public function setHeaders($headers){
        foreach ($headers as $key => $value) {
            $this->getResponse()->setHeader($key, $value);
        }
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }


    public static function getStatusDescription($code)
    {
        $codes = [

            // Informational 1xx
            100 => 'Continue',
            101 => 'Switching Protocols',

            // Success 2xx
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',

            // Redirection 3xx
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',  // 1.1
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            // 306 is deprecated but reserved
            307 => 'Temporary Redirect',

            // Client Error 4xx
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',

            // Server Error 5xx
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            509 => 'Bandwidth Limit Exceeded'
        ];

        $result = (isset($codes[$code])) ? $codes[$code] : 'Unknown Status Code';

        return $result;
    }

}