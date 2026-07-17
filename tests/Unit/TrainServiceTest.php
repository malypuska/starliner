<?php

namespace app\tests\Unit;

use app\tests\Support\UnitTester;
use SoapClient;
use SoapFault;

use app\components\StarlinerService;

class TrainServiceTest extends \Codeception\Test\Unit {

    protected UnitTester $tester;

    public function testGetRouteSuccess() {
        
        $starlinerService = new StarlinerService();

        $this->expectException(\yii\base\ErrorException::class);

        $requestParams = [
            'train' => '016А',
            'start_station' => '2000000',
            'end_station' => '2004000',
            'day' => 20,
            'month' => 7,
        ];        
        
        $result = $starlinerService->getTrainRoute($requestParams);  
        
        $this->assertIsArray($result);
        $this->assertEquals('016А', $result['description']['number']);
    }   
}
