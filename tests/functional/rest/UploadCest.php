<?php


class UploadCest
{
    public function _before(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer 13cf378b52dbdf455b2bdc8bd3f0549c');
    }

    public function _after(FunctionalTester $I)
    {
    }

    /*
     *  TODO fixtures and check errors
        $rout->get('/get-url', 'getUrl'), //get url for video hosting
        $rout->post('/video/{rand}', 'uploadVideo') //fake method for upload video
    */
    public function getUrlForUploadVideoTest(FunctionalTester $I)
    {
        $I->wantTo('Open method with video list');
        $I->sendGET('upload/get-url');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('server_url');
    }

    public function uploadVideoFileTest(FunctionalTester $I)
    {
        $I->wantTo('Open method with video list');
        $I->sendPOST('upload/video/e922ffaf36', null, ['file'=>codecept_data_dir('Sky-Mobile.mp4')]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('id');
    }
}
