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
        $user = $this->di->get('user');
        $response = [];
        $models = Video::find([['user_id'=>$user->token]]);
        if($models){
            /** @var Video $model */
            foreach($models as $model){
                $response[] = [
                    'id'=>$model->getId(),
                    'filename'=>$model->name,
                    'duration'=>$model->duration,
                    'url'=>$this->di->get('url')->get('public/'.$model->filename)
                ];
            }
        }
        return $this->response($response);
    }

    /**
     * Get information about video
     * @param $id
     * @return mixed
     */
    public function view($id){
        $response=[];
        $model = Video::findById($id);
        if($model){
            $response = [
                'id'=>$model->getId(),
                'filename'=>$model->name,
                'duration'=>$model->duration,
                'url'=>$this->di->get('url')->get('/public/'.$model->filename)
            ];
        }
        return $this->response($response);
    }

    /**
     * Get requests list
     * @return mixed
     */
    public function getRequests()
    {
        $user = $this->di->get('user');
        $requests = [];
        $models = Requests::find([['user_id'=>$user->token]]);
        if($models){
            /** @var Requests $model */
            foreach($models as $model){
                $video = $model->getVideo();
                $requests[] = [
                    'id'=>$model->getId(),
                    'filename'=>$video->name,
                    'url'=>$this->di->get('url')->get('public/'.$video->filename),
                    'status'=>Requests::$statuses[$model->status]
                ];
            }
        }
        return $this->response($requests);
    }

    /**
     * Check trim request by id
     *
     * @param $request_id
     * @return mixed
     * @throws RestException
     */
    public function viewRequest($request_id){
        /** @var Requests $model */
        $model = Requests::findById($request_id);
        if(!$model){
            throw new RestException(404, 'Request not found');
        }
        $model->updateStatus();
        $video = $model->getVideo();
        return $this->response([
            'id'=>$model->getId(),
            'filename'=>$video->name,
            'url'=>$this->di->get('url')->get('public/'.$video->filename),
            'status'=>Requests::$statuses[$model->status]
        ]);
    }

    /**
     * Start trim video (create request)
     * @param $video_id
     * @return mixed
     * @throws RestException
     */
    public function startTrim($video_id){
        $post = $this->request->getPost();

        if(!isset($post['from'])||!isset($post['to'])){
            throw new RestException(400, "Need set options 'from' and 'to'");
        }

        /** @var Video $video */
        $video = Video::findById($video_id);
        if(!$video){
            throw new RestException(404, 'Video not found');
        }

        $manager = new VideoManager();
        $command = new TrimCommand($manager);
        if($command->execute($video, $post['from'], $post['to'])){
            $model = Requests::createRequest($video_id, $post['from'], $post['to']);
            if(($errors=$model->getErrors())){
                throw new RestException(500, $errors[0], ['devMessage'=>$errors]);
            }

            return $this->response([
                'id'=>$model->getId(),
                'filename'=>$video->name,
                'url'=>$this->di->get('url')->get('public/'.$video->filename),
                'status'=>Requests::$statuses[$model->status]
            ]);
        }else{
            throw new RestException(500, 'Server could not start trim ');
        }

    }

    /**
     * Restart trim video (create request)
     * @param $request_id
     * @return mixed
     * @throws RestException
     */
    public function restartTrim($request_id){
        /** @var Requests $model */
        $model = Requests::findById($request_id);
        if(!$model){
            throw new RestException(404, '', ['devMessage'=>'Request not found']);
        }

        $video = $model->getVideo();
        $manager = new VideoManager();
        $command = new TrimCommand($manager);
        if($command->execute($video, $model->from, $model->to)){
            $model->updateStatus(Requests::STATUS_SCHEDULED);
            if(($errors=$model->getErrors())){
                throw new RestException(500, $errors[0], ['devMessage'=>$errors]);
            }

            return $this->response([
                'id'=>$model->getId(),
                'filename'=>$video->name,
                'url'=>$this->di->get('url')->get('public/'.$video->filename),
                'status'=>Requests::$statuses[$model->status]
            ]);
        }else{
            throw new RestException(500, 'Server could not start trim ');
        }
    }
}