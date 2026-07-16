<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */
declare(strict_types=1);

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\components\StarlinerService;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ServiceController extends Controller {

    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionIndex(): int {
        
    }

    public function actionRailStations() {
        $starlinerService = new StarlinerService();

        $data = $starlinerService->getRailStations('Москва');

        if (!empty($data)) {
            print_r($data);
        }
    }

    public function actionRoute() {
        $starlinerService = new StarlinerService();

        $requestParams = [
            'train' => '016А',
            'start_station' => '2000000',
            'end_station' => '2004000',
            'day' => 20,
            'month' => 7,
        ];

        $data = $starlinerService->getTrainRoute($requestParams);

        if (!empty($data)) {
            print_r($data);
        }
    }
}
