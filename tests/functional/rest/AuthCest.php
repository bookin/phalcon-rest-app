<?php


class AuthCest
{
    private $token = '';

    public function _before(FunctionalTester $I)
    {
    }

    public function _after(FunctionalTester $I)
    {
    }

    /*
     * TODO fixtures and check errors
     */
    public function getTokenTest(FunctionalTester $I)
    {
        $I->wantTo('Get token for new user');
        $I->sendGET('auth');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('token');
    }

    public function openMethodWithTokenTest(FunctionalTester $I)
    {
        $I->wantTo('Open method with token');
        $I->haveHttpHeader('Authorization', 'Bearer 13cf378b52dbdf455b2bdc8bd3f0549c');
        $I->sendGET('video');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('success');
    }

    public function openMethodWithoutTokenTest(FunctionalTester $I)
    {
        $I->wantTo('Open method without token');
        $I->sendGET('video');
        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
        $I->seeResponseContains('error');
    }
}
