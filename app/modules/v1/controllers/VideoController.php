<?php
namespace v1\Controllers;

use Rest\Components\RestController;
use Rest\Components\RestException;
use v1\components\TrimCommand;
use v1\components\VideoManager;
use v1\models\Requests;
use v1\models\Video;

class VideoController extends RestController{

    /**
     * Get video list
     * @return mixed
     */
    public function index()
    {
        $response = [];
        $models = Video::find(['user_id'=>'/*...*/']);
        if($models){
            foreach($models as $model){
                $response[] = [
                    'filename'=>$model->filename,
                    'duration'=>$model->duration,
                    'url'=>$this->di->get('url')->get('/public/'.$model->filename)
                ];
            }
        }
        return $this->response($response);
    }

    /**
     * Get requests list
     * @return mixed
     */
    public function getRequests()
    {
        $requests = [];
        $models = Requests::find(['user_id'=>'/*...*/']);
        if($models){
            /** @var Requests $model */
            foreach($models as $model){
                $requests[] = [
                    'id'=>$model->getId(),
                    'filename'=>$model->getVideo()->filename,
                    'status'=>Requests::$statuses[$model->status],
                    'url'=>$this->di->get('url')->get('/public/'.$model->filename)
                ];
            }
        }
        return $this->response($requests);
    }

    /**
     * Get information about video
     * @param $id
     * @return mixed
     */
    public function view($id){
        $model = Video::findFirst(['_id'=>$id]);
        $response = [
            'filename'=>$model->filename,
            'duration'=>$model->duration,
            'url'=>$this->di->get('url')->get('/public/'.$model->filename)
        ];
        return $this->response($response);
    }

    /**
     * Check trim request by id
     *
     * @param $request_id
     * @return mixed
     * @throws RestException
     */
    public function checkTrim($request_id){
        /** @var Requests $request */
        $request = Requests::findFirst(['_id'=>$request_id]);
        if(!$request){
            throw new RestException(404, ['devMessage'=>'Request not found']);
        }

        return $this->response(['status'=>Requests::$statuses[$request->status]]);
    }

    /**
     * Start trim video (create request)
     * @param $video_id
     * @return mixed
     * @throws RestException
     */
    public function startTrim($video_id){
        /** @var Video $video */
        $video = Video::findFirst(['_id'=>$video_id]);
        if(!$video){
            throw new RestException(404, ['devMessage'=>'Video not found']);
        }

        $manager = new VideoManager();
        $command = new TrimCommand($manager);
        $command->execute($video->filename);

        return $this->response(['status'=>Requests::$statuses[Requests::STATUS_SCHEDULED]]);
    }

    /**
     * Restart trim video (create request)
     * @param $request_id
     * @return mixed
     * @throws RestException
     */
    public function restartTrim($request_id){
        /** @var Requests $request */
        $request = Requests::findFirst(['_id'=>$request_id]);
        if(!$request){
            throw new RestException(404, ['devMessage'=>'Request not found']);
        }

        /** @var Video $video */
        $video = Video::findFirst(['_id'=>$request->video_id]);
        if(!$video){
            throw new RestException(404, ['devMessage'=>'Video not found']);
        }

        $manager = new VideoManager();
        $command = new TrimCommand($manager);
        $command->execute($video->filename);

        return $this->response(['status'=>Requests::$statuses[Requests::STATUS_SCHEDULED]]);
    }
}