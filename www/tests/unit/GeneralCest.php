<?php 

use App\Models\FileModel;

class GeneralCest
{
    /**
     * Проверка расширения и имени файла
     * @param UnitTester $I
     */
    public function ChekExtAndNameFileTest(UnitTester $I)
    {
        $I->assertTrue((new FileModel())->getExtAndNameFile('test.jpg') == ['name' => 'test', 'ext' => 'jpg']);
        $I->assertTrue((new FileModel())->getExtAndNameFile('testt.ewr.jpg') == ['name' => 'testt.ewr', 'ext' => 'jpg']);
        $I->assertFalse((new FileModel())->getExtAndNameFile('testt.ewr.jpg') == ['name' => 'testt.ewr', 'ext' => 'png']);
    }
    /**
     * Проверка на разрешенное расширение файла.
     * @param UnitTester $I
     */
    public function ChekAllowedExtTest(UnitTester $I)
    {
        $I->assertTrue((new FileModel())->checkEXtention('exp') === false);
        $I->assertTrue((new FileModel())->checkEXtention('jpg') === true);
    }
}
