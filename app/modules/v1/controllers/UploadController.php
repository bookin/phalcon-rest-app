<?php
namespace v1\Controllers;

use Rest\Components\RestController;
use Rest\Components\RestException;
use v1\components\CDNServer;
use v1\models\Video;

class UploadController extends RestController {

    /**
     * Action for return server_url to upload video
     * @return mixed
     */
    public function getUrl()
    {
        $cdn = new CDNServer();
        return $this->response([
            'server_url'=>$this->di->get('url')->get('v1/upload/video/'.$cdn->getServerKey())
        ]);
    }

    /**
     * Fake method for upload video
     * @return mixed
     * @throws RestException
     */
    public function uploadVideo()
    {
        $fileNameKey = 'file';
        if ($this->request->hasFiles() == true) {
            foreach ($this->request->getUploadedFiles() as $file) {
                if($file->getKey() == $fileNameKey){
                    $model = Video::saveVideo($file);
                    if(!$model || $model->getMessages()){
                        $errors = $model->getErrors();
                        throw new RestException(400, $errors[0], ['devMessage'=>$errors]);
                    }else{
                        return $this->response([
                            'filename'=>$model->filename,
                            'duration'=>$model->duration,
                            'url'=>$this->di->get('url')->get('/public/'.$model->filename)
                        ]);
                    }
                }else{
                    throw new RestException(400, 'Need send file');
                }
            }
        }else{
            throw new RestException(400, 'Need send file');
        }
    }
}