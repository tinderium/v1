<?php

namespace common\tests;

use common\models\Adr;

class AlapanyagTest extends \Codeception\Test\Unit {

    /**
     * @var \common\tests\UnitTester
     */
    protected $tester;
    private $model;

    protected function _before() {
        $this->model = new \common\modules\alapanyag\models\AlapanyagEdit();
    }

    protected function _after() {
        
    }

    public function testSavebeszallitoifmustNonExistingBeszállito() {
        $beszallitoNev = 'szerdareggel';
        $this->model->beszallitoName = $beszallitoNev;
        $this->model->SaveBeszallítoIfMust();
        expect("Létrehoz és beszúr egy $beszallitoNev nevű beszállító");

        $this->assertNotNull($this->model->getBeszallitoFromName());
    }

    public function testSavebeszallitoifmustExistingBeszállito() {
        $beszallitoNev = 'Barry Seal';
        $this->model->beszallitoName = $beszallitoNev;
        $id1 = $this->model->SaveBeszallítoIfMust();
        $this->model = new \common\modules\alapanyag\models\AlapanyagEdit();
        $this->model->beszallitoName = $beszallitoNev;
        $id2 = $this->model->SaveBeszallítoIfMust();

        expect("Visszatér ugyanazzal az id-vel");

        $this->assertTrue($id1 == $id2);
    }

    public function testSavebeszallitoifmustEmptyBeszallitoName() {
        $beszallitoNev = '';
        $this->model->beszallitoName = $beszallitoNev;
        $returnValue = $this->model->SaveBeszallítoIfMust();
        expect("Null-al tér vissza");

        $this->assertNull($returnValue);
    }

    public function testSavebeszallitoifmustNullBeszallitoName() {
        $this->model->beszallitoName = null;
        $returnValue = $this->model->SaveBeszallítoIfMust();
        expect("Null-al tér vissza");

        $this->assertNull($returnValue);
    }

    public function testSavebeszallitoifmustArrayBeszallitoName() {
        $this->model->beszallitoName = ['fagyi', 'csoki'];
        $returnValue = $this->model->SaveBeszallítoIfMust();
        expect("Null-al tér vissza");

        $this->assertNull($returnValue);
    }

    public function testSavealapanyagadrconnectionaruosztalyOneRow() {
        $this->model->id = 256;
        $this->model->aruOsztaly = "abc";
        $result = $this->model->saveAlapanyagAdrConnectionAruosztaly();

        expect("Beszúr egy rekordot a kapcsolótáblába");
        $query = $this->getNumberOfAdrForAlapanyag();
        $this->AssertTrue(count($query) == 1 && $result);
    }

    public function testSavealapanyagadrconnectionaruosztalyValid() {
        $this->model->id = 256;
        $this->model->aruOsztaly = ["abc", "bcd", "cde"];
        $result = $this->model->saveAlapanyagAdrConnectionAruosztaly();

        expect("Beszúr három rekordot a kapcsolótáblába");
        $query = $this->getNumberOfAdrForAlapanyag();
        $this->AssertTrue(count($query) == 3 && $result);
    }

    public function testSavealapanyagadrconnectionaruosztalyReinsert() {
        $this->model->id = 256;
        $this->model->aruOsztaly = ["abc", "bcd", "cde"];
        $result = $this->model->saveAlapanyagAdrConnectionAruosztaly();
        $this->model->removeAlapanyagAdrConnection();
        $this->model->aruOsztaly = ["abc"];
        $result = $this->model->saveAlapanyagAdrConnectionAruosztaly();
        expect("Beszúr három rekordot a kapcsolótáblába, törli őket, majd beszúr egyet");
        $query = $this->getNumberOfAdrForAlapanyag();
        $this->AssertTrue(count($query) == 1 && $result);
    }

    public function testSavealapanyagadrconnectionaruosztalyEmptyArray() {
        $this->model->id = 256;
        $this->model->aruOsztaly = [];
        $result = $this->model->saveAlapanyagAdrConnectionAruosztaly();

        expect("Hiba: return false");
        $this->AssertFalse($result);
    }

    public function testSavealapanyagadrconnectionaruosztalyNullArray() {
        $this->model->id = 256;
        $this->model->aruOsztaly = null;
        $result = $this->model->saveAlapanyagAdrConnectionAruosztaly();

        expect("Hiba: return false");
        $this->AssertFalse($result);
    }

    public function testSavealapanyagadrconnectionaruosztalyEmptyString() {
        $this->model->id = 256;
        $this->model->aruOsztaly = "";
        $result = $this->model->saveAlapanyagAdrConnectionAruosztaly();

        expect("return false");
        $this->AssertFalse($result);
    }

    public function testSavealapanyagadrconnectionaruosztalyAruosztalyArrayWithEmptyString() {
        $this->model->id = 256;
        $this->model->aruOsztaly = ["abc", "", "cde"];
        $result = $this->model->saveAlapanyagAdrConnectionAruosztaly();

        $query = $this->getNumberOfAdrForAlapanyag();
        expect("Beszúr két rekordot");
        $this->AssertTrue(count($query) == 2);
    }

    public function testSaveveszelyValid() {
        $this->model->id = 256;
        $this->model->veszely = [5, 6];
        $this->model->saveAlapanyagVeszely();

        $count = $this->getNumberOfVeszelyForAlapanyag();
        expect("Beszúr két veszélyt");
        $this->AssertTrue(count($count) == 2);
    }

    public function testSaveveszelyEmpty() {
        $this->model->id = 256;
        $this->model->veszely = [];
        $this->model->saveAlapanyagVeszely();

        $count = $this->getNumberOfVeszelyForAlapanyag();
        expect("Nem szúr be semmit");
        $this->AssertTrue(count($count) == null);
    }

    public function testSaveveszelyNull() {
        $this->model->id = 256;
        $this->model->veszely = null;
        $this->model->saveAlapanyagVeszely();

        $count = $this->getNumberOfVeszelyForAlapanyag();
        expect("Beszúr két veszélyt");
        $this->AssertTrue(count($count) == null);
    }

    public function testSaveveszelyReinsert() {
        $this->model->id = 256;
        $this->model->veszely = 5;
        $this->model->saveAlapanyagVeszely();
        $this->model->veszely = [5, 6, 7];
        $this->model->saveAlapanyagVeszely();
        $count = $this->getNumberOfVeszelyForAlapanyag();
        expect("Beszúr két veszélyt");
        $this->AssertTrue(count($count) == 3);
    }

    public function getNumberOfAdrForAlapanyag() {
        return \common\modules\alapanyag\models\AlapanyagAdr::find()->where(['alapanyag_id' => $this->model->id])->all();
    }

    public function getNumberOfVeszelyForAlapanyag() {
        return \common\modules\alapanyag\models\AlapanyagVeszely::find()->where(['alapanyag_id' => $this->model->id])->all();
    }

}
