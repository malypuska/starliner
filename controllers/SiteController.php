<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use yii\web\Controller;

use app\models\TrainRouteForm;

class SiteController extends Controller
{


    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $trainRouteForm = new TrainRouteForm();
//        $trainRouteForm->testvalue();
        
        return $this->render('index', ['trainRouteForm' => $trainRouteForm]);
    }
}
