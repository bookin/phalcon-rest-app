<?php
namespace v1\models;

use Phalcon\Mvc\Model\Validator\StringLength;
use Rest\Components\Model;

class Video extends Model
{
    public $filename;
    public $user_id;
    public $server;
    public $duration;
    public $trim_date;

    public function validation()
    {
        $this->validate(
            new StringLength(
                [
                    "field"          => "filename",
                    "min"            => 3,
                    "messageMinimum" => "We want more than just their initials",
                ]
            )
        );

        return $this->validationHasFailed() !== true;
    }


    /**
     * Create Fake video
     * @return Video
     */
    public static function createFakeVideo(){
        $filename = self::generateRandomString(5).'avi';
        $model = new self();
        $model->filename = $filename;
        $model->duration = rand(10, 30);
        $model->server = long2ip(rand(0, "4294967295"));
        $model->save();
        return $model;
    }

    public static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}