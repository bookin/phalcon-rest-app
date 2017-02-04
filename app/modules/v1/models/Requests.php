<?php
namespace v1\models;

use Phalcon\Mvc\Model\Message;
use Phalcon\Mvc\Model\Validator\Numericality;
use Rest\Components\Model;
use Rest\Components\RestException;

class Requests extends Model
{
    public $video_id;
    public $user_id;
    public $time_start;
    public $time_end;
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
        return (string)$this->_id;
    }

    /**
     * @param mixed $id
     * @return null||self
     */
    public static function findById($id){
        try{
            $id = new \MongoId($id);
            return parent::findById($id);
        }catch (\MongoException $e){
            return null;
        }
    }

    public function beforeSave(){
        $this->user_id = self::getDI()->get('user')->token;
    }

    public function afterFetch(){
        /* Update all status after fetch*/
        $this->updateStatus();
    }

    /**
     * @return bool
     */
    public function validation()
    {
        $this->validate(new Numericality([
                "field"=>"from"
            ])
        );

        $this->validate(new Numericality([
                "field"=>"to"
            ])
        );

        if($this->to > $this->getVideo()->duration){
            $this->appendMessage(new Message("Parameter 'to' cannot be more then video duration"));
        }

        return $this->validationHasFailed() !== true;
    }

    /**
     * Update status or make fake status
     * @param $status
     */
    public function updateStatus($status=null){
        if(($status !== null) && in_array($status, self::$statuses)){
            $this->status = $status;
            $this->update();
        }else if(!in_array($this->status, [self::STATUS_DONE, self::STATUS_FAILED]) && mt_rand(0,1)){
            if(time() > $this->time_end){
                $this->status = self::STATUS_DONE;
            }else{
                $this->status = self::STATUS_PROCESSING;
            }
            if(rand(0, 1)){
                $this->status = self::STATUS_FAILED;
            }
            $this->update();
        }
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

    /**
     * @param Video $model
     * @return bool
     */
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

    /**
     * Relation
     * @return null|Video
     */
    public function getVideo(){
        return Video::findById($this->video_id);
    }

    public static function createRequest($video_id, $from, $to){
        $request = new Requests();
        $request->time_start = time();
        $request->time_end = time()+(60*rand(2,10));
        $request->video_id = $video_id;
        $request->from = $from;
        $request->to = $to;
        $request->status = self::STATUS_SCHEDULED;
        $request->save();
        return $request;
    }
}