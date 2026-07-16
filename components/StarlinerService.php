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
        $this->_client = new SoapClient(self::WSDL_URL, [
            'trace' => 1,
            'exceptions' => true,
            'cache_wsdl' => WSDL_CACHE_NONE,
        ]);
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
            // Настройки клиента могут быть расширены (таймауты и т.д.)
            $client = new SoapClient(self::WSDL_URL);

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

            if (!isset($response->return)) {
                Yii::error('Пустой ответ от Сервера.', 'soap_api');
                throw new \Exception('Пустой ответ от Сервера.');
            }

            Yii::info('Успешный ответ от SOAP trainRoute', 'soap_api');

            return $this->parseResponse($response->return);
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
            'number' => (string) $xmlObject->train_description->number,
            'name' => (string) $xmlObject->train_description->name,
        ];

        $routes = [];
        foreach ($xmlObject->route_list as $routeItem) {
            $stops = [];
            foreach ($routeItem->stop_list as $stopItem) {
                $stops[] = [
                    'station' => (string) $stopItem->stop,
                    'arrival_time' => (string) $stopItem->arrival_time,
                    'departure_time' => (string) $stopItem->departure_time,
                    'stop_time' => (int) $stopItem->stop_time,
                ];
            }
            $routes[] = [
                'name' => (string) $routeItem->name,
                'from' => (string) $routeItem->from,
                'to' => (string) $routeItem->to,
                'stops' => $stops,
            ];
        }
        $result['routes'] = $routes;

        return $result;
    }
}
