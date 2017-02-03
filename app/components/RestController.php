<?php
namespace Rest\components;

use Application\components\response\TextResponse;
use Rest\Components\Response\JsonResponse;
use Rest\Components\Response\Response;

class RestController extends BaseController
{
    protected $statusCode = 200;
    protected $headers    = array();
    protected $format     = '';


    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return string
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return !empty($this->format) ? $this->format : $this->config->rest->format;
    }

    /**
     * Setters
     */
    public function setStatusCode($code)
    {
        $this->statusCode = $code;
    }

    public function setHeaders($key, $value)
    {
        $this->headers[$key] = $value;
    }


    public function setFormat($format)
    {
        $this->format = $format;
    }

    protected function initResponse()
    {
        $this->statusCode = 200;
        $this->headers    = array();
        $this->payload    = '';
    }

    protected function response($content=[], Response $response = null)
    {
        $this->initResponse();

        $status = $this->getStatusCode();
        $description = Response::getStatusDescription($status);
        $headers = $this->getHeaders();

        if(!$response){
            $format = $this->getFormat();
            switch ($format)
            {
                case 'text':
                    $response = new TextResponse();
                    break;
                case 'xml':
                    $response = new JsonResponse();
                    break;
                default:
                    $response = new JsonResponse();
                    break;
            }
        }

        $response->setHeaders($headers);
        $response->getResponse()->setStatusCode($status, $description);
        $response->getResponse()->setHeader('Access-Control-Allow-Origin', '*');
        $response->getResponse()->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');
        $response->send($content);

        return $response->getContent();
    }


}