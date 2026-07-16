<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\components\StarlinerService;
use app\models\TrainRouteForm;

class TrainController extends Controller {
    public function actionSearch() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $trainRouteForm = new TrainRouteForm();
        if (Yii::$app->request->isAjax) {
            if ($trainRouteForm->load(Yii::$app->request->post()) && $trainRouteForm->validate()) {
                $trainRouteForm->getTrainRoute();
            } else {
                $trainRouteForm->getValidateErrors();
            }
        }
        
        return $trainRouteForm->getDataResponce();
    }
    
    public function actionSearchStation() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $trainRouteForm = new TrainRouteForm();
        if (Yii::$app->request->isAjax) {
            if ($trainRouteForm->load(Yii::$app->request->get())) {
                $trainRouteForm->getRailStations();
            }
        }    
        
        return $trainRouteForm->getDataResponce();
    }
}
