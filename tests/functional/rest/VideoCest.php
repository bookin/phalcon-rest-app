<?php


class VideoCest
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
        $rout->get('/', 'index'), //get video list
        $rout->get('/{id}', 'view'), //get information about video
        $rout->get('/requests', 'getRequests'), //get requests list
        $rout->get('/requests/{request_id}', 'viewRequest'), //get processing status by request_id
        $rout->put('/requests/{request_id}', 'restartTrim'), //restart request for trim video by request_id
        $rout->post('/trim/{video_id}', 'startTrim'), //request for trim video by video_id, parameters: from, to
    */
    public function getVideoListTest(FunctionalTester $I)
    {
        $I->wantTo('Open method with video list');
        $I->sendGET('video');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('success');
    }

    public function getVideoTest(FunctionalTester $I)
    {
        $I->wantTo('Open method with one video');
        $I->sendGET('video/589640d4d87459081f000034');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('success');
    }


    public function getRequestListTest(FunctionalTester $I)
    {
        $I->wantTo('Open method with request list');
        $I->sendGET('video/requests');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('success');
    }

    public function getRequestTest(FunctionalTester $I)
    {
        $I->wantTo('Open method with  information about request');
        $I->sendGET('video/requests/58964b94d874597015000029');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('success');
    }

    public function restartRequestTest(FunctionalTester $I)
    {
        $I->wantTo('Open method for restart request');
        $I->sendPUT('video/requests/58964b94d874597015000029');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('success');
    }

    public function startVideoTrimTest(FunctionalTester $I)
    {
        $I->wantTo('Open method for trim video');
        $I->sendPOST('video/trim/589640d4d87459081f000034');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('success');
    }
}
