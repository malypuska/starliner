<?php

namespace app\components;

use SoapClient;
use SoapFault;
use Yii;

class StarlinerService {

    private const WSDL_URL = 'https://test-api.starliner.ru/Api/connect/Soap/Train/1.1.7?wsdl';

    private $authData = [
        'login' => 'test',
        'psw' => 'bYKoDO2it',
        'terminal' => 'htk_test',
        'represent_id' => 22400,
        'access_token' => '',
        'language' => 'RU',
        'currency' => 'RUB',
    ];
    private $_client;

    public function __construct() {
        $this->createSoapClient();
    }

    public function createSoapClient() {
        try {
            $this->_client = new SoapClient(self::WSDL_URL, [
                'trace' => 1,
                'exceptions' => true,
                'cache_wsdl' => WSDL_CACHE_NONE,
            ]);
        } catch (SoapFault $e) {
            Yii::error("Ошибка SOAP: " . $e->getMessage(), 'soap_api');
            return false;
        } catch (\Exception $e) {
            Yii::error("Ошибка ({$e->getCode()}): " . $e->getMessage(), 'soap_api');
            return false;
        }
    }

    /**
     * Получает станции через SOAP API.
     *
     * @param string $name Валидированные данные из формы.
     * @return array|false Массив с данными маршрута или false при ошибке.
     */
    public function getRailStations(string $name) {
        try {
            Yii::info('Отправка запроса SOAP getRailStations', 'soap_api');
            Yii::info('Параметры запроса: ' . $name, 'soap_api');

            $response = $this->_client->getRailStations($this->authData, $name);

            if (!isset($response->list)) {
                Yii::error('Пустой ответ от Сервера.', 'soap_api');
                throw new \Exception('Пустой ответ от Сервера.');
            }

            Yii::info('Успешный ответ от SOAP getRailStations', 'soap_api');

            return $this->_parseResponseRailStations($response);
        } catch (SoapFault $e) {
            Yii::error("Ошибка SOAP-запроса: " . $e->getMessage(), 'soap_api');
            return false;
        } catch (\Exception $e) {
            Yii::error("Ошибка ({$e->getCode()}): " . $e->getMessage(), 'soap_api');
            return false;
        }
    }

    /**
     * Получает маршрут поезда через SOAP API.
     *
     * @param array $data Валидированные данные из формы.
     * @return array|false Массив с данными маршрута или false при ошибке.
     */
    public function getTrainRoute(array $data) {
        try {
            $train = $data['train'];
            $requestParams = [
                'from' => $data['start_station'],
                'to' => $data['end_station'],
                'day' => $data['day'],
                'month' => $data['month'],
            ];

            Yii::info('Отправка запроса SOAP trainRoute', 'soap_api');
            Yii::info('Параметры запроса: ' . print_r($requestParams, true), 'soap_api');
            
            $response = $this->_client->trainRoute($this->authData, $train, $requestParams);
            
            return $this->parseResponse($response);
        } catch (SoapFault $e) {
            Yii::error("Ошибка SOAP-запроса: " . $e->getMessage(), 'soap_api');
            return false;
        } catch (\Exception $e) {
            Yii::error("Ошибка ({$e->getCode()}): " . $e->getMessage(), 'soap_api');
            return false;
        }
    }

    /**
     * Парсит сложный XML-ответ от SOAP в удобный массив.
     */
    private function _parseResponseRailStations($xmlObject): array {
        $result = [];
        foreach ($xmlObject->list as $item) {
            $result[] = [
                'id' => $item->id,
                'name' => $item->name,
            ];
        }

        return $result;
    }

    private function parseResponse($xmlObject): array {
        $result = [];

        $result['train_description'] = [
            'number' => $xmlObject->train_description->number,
            'from' => $xmlObject->train_description->from,
            'to' => $xmlObject->train_description->to,
        ];

        $stops = [];
        if (!empty($xmlObject->route_list->stop_list)) {
            foreach ($xmlObject->route_list->stop_list as $stopItem) {
                $stops[] = [
                    'station' => $stopItem->stop,
                    'arrival_time' => $stopItem->arrival_time,
                    'departure_time' => $stopItem->departure_time,
                    'stop_time' => $stopItem->stop_time,
                ];
            }
        }
        
        $route = [
            'name' => $xmlObject->route_list->name,
            'from' => $xmlObject->route_list->from,
            'to' => $xmlObject->route_list->to,
            'stops' => $stops,
        ];

        $result['route'] = $route;

        return $result;
    }
}
