<?php
namespace Rest\Components\Response;


class JsonResponse extends Response
{

    public function send($records, $error=false){

        $success = ($error) ? self::RESPONSE_ERROR : self::RESPONSE_SUCCESS;

        /* If the query string 'envelope' is set to false, do not use the envelope. Instead, return headers. */
        $request = $this->di->get('request');
        if($request->get('envelope', null, null) === 'false'){
            $this->envelope = false;
        }

        /* Convert keys to Snake */
        if($this->snake){
            $records = $this->arrayKeysToSnake($records);
        }

        if($this->envelope){
            /* Provide an envelope for JSON responses.  '_meta' and 'records' are the objects.*/
            $message = [];
            $message['_meta'] = [
                'status' => $success,
                'count' => ($error) ? 1 : count($records)
            ];

            // Handle 0 record responses, or assign the records
            if($message['_meta']['count'] === 0){
                /*
                 * This is required to make the response JSON return an empty JS object.  Without
                 * this, the JSON return an empty array:  [] instead of {}
                 */
                $message['records'] = new \stdClass();
            } else {
                $message['records'] = $records;
            }

        } else {
            $this->getResponse()->setHeader('X-Record-Count', count($records));
            $this->getResponse()->setHeader('X-Status', $success);
            $message = $records;
        }

        $this->getResponse()->setContentType('application/json');

        /*
         * HEAD requests are detected in the parent constructor. HEAD does everything exactly the
         * same as GET, but contains no body.
         */
        if(!$this->head){
            $this->getResponse()->setJsonContent($message);
        }

        $this->setContent($message['records']);
        $this->getResponse()->send();

        return $this;
    }


}