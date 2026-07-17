<?php

namespace app\models;

use Yii;
use yii\base\Model;
use Exception;
use app\components\StarlinerService;

class TrainRouteForm extends Model {

    public $train;
    public $start_station;
    public $end_station;
    public $day;
    public $month;
    public $q;
    private $_dataResponce = [
        'success' => false,
        'error' => 'Ошибка на сервере, попробуйте позже',
    ];
    static public $listMonth = [
        '01' => 'Январь',
        '02' => 'Февраль',
        '03' => 'Март',
        '04' => 'Апрель',
        '05' => 'Май',
        '06' => 'Июнь',
        '07' => 'Июль',
        '08' => 'Август',
        '09' => 'Сентябрь',
        '10' => 'Октябрь',
        '11' => 'Ноябрь',
        '12' => 'Декабрь'
    ];

    static public function getListMonth(): array {
        return self::$listMonth;
    }

    static public function getListDay(): array {
        $data = [];
        for ($i = 1; $i <= 31; $i++) {
            $data[$i] = str_pad($i, 2, '0', STR_PAD_LEFT);
        }

        return $data;
    }

    public function rules() {
        return [
            [['train', 'start_station', 'end_station', 'day', 'month'], 'required', 'message' => 'Поле обязательно для заполнения.'],
            [['train'], 'string', 'max' => 10],
            [['train'], 'match', 'pattern' => '/^[0-9A-ZА-Я]+$/u', 'message' => 'Некорректный формат номера поезда.'],
            [['start_station', 'end_station'], 'integer', 'message' => 'Код станции должен быть числом.'],
            [['day'], 'integer', 'min' => 1, 'max' => 31, 'message' => 'День должен быть от 1 до 31.'],
            [['month'], 'integer', 'min' => 1, 'max' => 12, 'message' => 'Месяц должен быть от 1 до 12.'],
            [['q'], 'safe'],
        ];
    }

    public function attributeLabels() {
        return [
            'train' => 'Номер поезда',
            'start_station' => 'Станция отправления',
            'end_station' => 'Станция прибытия',
            'day' => 'День отправления',
            'month' => 'Месяц отправления',
        ];
    }

    public function load($data, $formName = null): bool {
        if (!empty($data['TrainRouteForm'])) {
            foreach ($data['TrainRouteForm'] as $attr => $value) {
                $this->{$attr} = $value;
            }
        }

        return true;
    }

    public function testvalue() {
        $this->train = '016А';
        $this->start_station = 2000000;
        $this->end_station = 2004000;
        $this->day = date('d') + 10;
        $this->month = date('m');
    }

    public function getTrainRoute() {
        try {
            $starlinerService = new StarlinerService();
            $dataResponce = $starlinerService->getTrainRoute($this->attributes);
            if (empty($dataResponce)) {
                $this->setDataResponce([
                    'success' => false,
                    'error' => 'Ошибка на сервере, попробуйте позже',
                ]);
            } else {
                $this->setDataResponce([
                    'success' => true,
                    'html' => Yii::$app->view->renderFile('@app/views/train/_route_result.php', ['data' => $dataResponce]),
                ]);
            }
        } catch (Exception $e) {
            $this->setDataResponce([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getRailStations() {
        try {
            $starlinerService = new StarlinerService();
            $dataResponce = $starlinerService->getRailStations($this->q);
            $this->setDataResponce($dataResponce);
        } catch (Exception $e) {
            $this->setDataResponce([[
                'id' => 0,
                'name' => 'Ошибка на сервере, попробуйте позже',
            ]]);
        }
    }

    public function setDataResponce(array $data) {
        $this->_dataResponce = $data;
    }

    public function getDataResponce(): array {
        return $this->_dataResponce;
    }

    public function getValidateErrors() {
        if ($this->hasErrors()) {

            $data = [];
            foreach ($this->getErrors() as $att => $message) {
                $data[] = $this->getAttributeLabel($att) . ' ' . join(', ', $message);
            }
            $this->setDataResponce([
                'success' => false,
                'error' => join('<br>', $data),
            ]);
        }
    }
}
