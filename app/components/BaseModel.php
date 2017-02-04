<?php
namespace Rest\Components;

use \Phalcon\Mvc\Collection;

class BaseModel extends Collection
{
    /**
     * @return array
     */
    public function getErrors(){
        $errors = [];
        $messages = $this->getMessages();
        if($messages){
            foreach ($messages as $message){
                $errors[]=$message->getMessage();
            }
        }
        return $errors;
    }
}