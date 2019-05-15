<?php
namespace tests\functional;

class UserCest
{
    private $login = null;
    private $pass = null;
    private $listFiles;

    private function getLogin () 
    {
        return $this->login = $this->login ?? $this->generateRandomString(6);
    }
    
    private function getPass ()
    {
        return $this->pass = $this->pass ?? $this->generateRandomString(6);
    }

    private function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function createUser(\FunctionalTester $I)
    {
        $I->sendPost('/api/createuser', ['login' => $this->getLogin(), 'pass' => $this->getPass()]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseMatchesJsonType(['status' => 'integer']);
        $I->seeResponseContainsJson(['status' => 1]);
    }
    
    public function recreateUser(\FunctionalTester $I)
    {
        $I->sendPost('/api/createuser', ['login' => $this->getLogin(), 'pass' => $this->getPass()]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseMatchesJsonType(['status' => 'integer', 'message' => 'string']);
        $I->seeResponseContainsJson(['status' => 0]);
    }
    
    public function loginUser(\FunctionalTester $I)
    {
        $I->sendPost('/api/login', ['login' => $this->getLogin(), 'pass' => $this->getPass()]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['status' => 1]);
        return $I;
    }
    
    public function addNewFile(\FunctionalTester $I)
    {
        $I = $this->loginUser($I);
        $I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
        $I->sendPut('/api/files_put?filename=test.jpg',file_get_contents(codecept_data_dir('Mercedes-Benz.jpg')));
        $I->seeResponseMatchesJsonType(['status' => 'integer']);
        $I->seeResponseContainsJson(['status' => 1]);
    }
    public function addDoubleFile(\FunctionalTester $I)
    {
        $I = $this->loginUser($I);
        $I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
        $I->sendPut('/api/files_put?filename=test.jpg',file_get_contents(codecept_data_dir('Mercedes-Benz.jpg')));
        $I->seeResponseMatchesJsonType(['status' => 'integer', 'message' => 'string']);
        $I->seeResponseContainsJson(['message' => 'Такой файл уже сушествует']);
    }
    public function getListFiles(\FunctionalTester $I)
    {
        $I = $this->loginUser($I);
        $I->sendGet('/api/files_list');
        $I->seeResponseMatchesJsonType(['status' => 'integer']);
        $I->seeResponseContainsJson(['status' => 1]);
        $obj = json_decode($I->grabResponse());
        $id = (int) $obj->files[0]->id;
        $ext = $obj->files[0]->ext;
        $this->listFiles = ['id' => $id, 'ext' => $ext];
        return $I;
    }
    
    public function updateFile(\FunctionalTester $I)
    {
        $I = $this->getListFiles($I);
        $I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
        $I->sendPut('/api/file_update?filename=kamaz.jpg&id=' . $this->listFiles['id'], file_get_contents(codecept_data_dir('kamaz.jpg')));
        $I->seeResponseMatchesJsonType(['status' => 'integer']);
        $I->seeResponseContainsJson(['status' => 1]);
    }
    public function logoutUser(\FunctionalTester $I)
    {
        $I = $this->loginUser($I);
        $I->sendPost('/api/logout');
        $I->seeResponseCodeIs(200);
        $I->seeResponseMatchesJsonType(['status' => 'integer']);
        $I->seeResponseContainsJson(['status' => 1]);
    }
}
