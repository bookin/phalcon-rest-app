<?php
namespace v1\models;

use Phalcon\Http\Request\FileInterface;
use Phalcon\Mvc\Model\Message;
use Phalcon\Mvc\Model\Validator\StringLength;
use Rest\Components\Model;
use Rest\Components\RestException;

class Video extends Model
{
    public $name;
    public $filename;
    public $user_id;
    public $server;
    public $duration;
    public $trim_date;

    /**
     * @var FileInterface
     */
    protected $file;
    static $allowedTypes = [
        'video/mp4',
        'video/avi'
    ];

    /**
     * @return string
     */
    public function getId(){
        return (string)$this->_id;
    }

    /**
     * @param mixed $id
     * @return null|self
     */
    public static function findById($id){
        try{
            $id = new \MongoId($id);
            return parent::findById($id);
        }catch (\MongoException $e){
            return null;
        }
    }

    public function validation()
    {
        if(empty($this->user_id)){
            $this->appendMessage(new Message('user_id cannot be empty'));
        }

        $this->validate(new StringLength([
                "field"=>"name",
                "min"=> 3,
                "messageMinimum"=>"We want more than just their initials"
            ])
        );

        if(!$this->isTypeAllowed($this->file->getRealType())){
            $this->appendMessage(new Message("Allowed types ".implode(',', self::$allowedTypes)));
        }

        return $this->validationHasFailed() !== true;
    }

    public function afterSave(){
        if(!$this->getMessages()){
            $this->file->moveTo(PUBLIC_PATH.'/files/'.$this->filename);
        }
    }

    /**
     * Check allowed video formats
     * @param  string $extension Extension (eg 'avi')
     * @return boolean
     */
    public function isTypeAllowed($extension)
    {
        return in_array($extension, self::$allowedTypes);
    }


    /**
     * @param mixed $user_id
     * @param \Phalcon\Http\Request\File $file
     * @return Video
     */
    public static function saveVideo($user_id, $file){
        $model = new self();
        $model->user_id = $user_id;
        $model->name = $file->getName();
        $model->duration = rand(10, 30);
        $model->server = long2ip(rand(0, "4294967295"));
        $model->file = $file;
        $model->filename = md5(microtime()).'.'.$model->file->getExtension();
        $model->save();
        return $model;
    }

    public function getUrl(){
        return self::getDI()->get('url')->get('public/files/'.$this->filename);
    }

    /**
     * Get list of Video
     * @param $user_id
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public static function getListByUserId($user_id, $limit=25, $offset=0){
        return self::find([['user_id'=>$user_id], "limit"=>$limit, "skip"=>$offset]);
    }

}