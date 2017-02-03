<?php
namespace v1\models;

use Rest\Components\Model;

class Requests extends Model
{
    public $video_id;
    public $time_start;
    public $from;
    public $to;
    public $status;


    const STATUS_FAILED = -10;
    const STATUS_SCHEDULED = 0;
    const STATUS_PROCESSING = 10;
    const STATUS_DONE = 20;


    public static $statuses = [
        self::STATUS_FAILED => 'failed',
        self::STATUS_SCHEDULED => 'scheduled',
        self::STATUS_PROCESSING => 'processing',
        self::STATUS_DONE => 'done',

    ];

    /**
     * @return mixed
     */
    public function getId(){
        return $this->_id;
    }

    /**
     * Fake trim video
     * @param Video $model
     * @return bool
     */
    public static function startTrim(Video $model, $from, $to)
    {
        $model = new self();
        if($model){
            $model->trim_date = time()+(60*rand(2,10));
            if($model->update()){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function checkTrim(Video $model){
        if($model){
            $model->trim_date = time()+(60*rand(2,10));
            if($model->update()){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function changeStatus(Video $model){

    }

    /**
     * Relation
     * @return Video
     */
    public function getVideo(){
        return Video::findFirst(['_id'=>$this->video_id]);
    }

}