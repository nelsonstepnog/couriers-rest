<?php

declare( strict_types = 1 );

namespace Delivery\CouriersRest\tests;

use Codeception\Util\HttpCode;
use yii;

/**
 * Class VehicleTypeListActionCest
 * Класс тестирования REST API: получение списка сущностей "Типы ТС".
 *
 * @package Delivery\CouriersRest\tests
 */
class ListActionCest
{
    /**
     * Массив с типами ключей в ответах.
     *
     * @var array
     */
    protected $responseTypes = [
        'correct' => [
            'errors'  => 'array',
            'notices' => 'array',
            'data'    => [
                'list' => 'array',
                'more' => 'boolean',
            ],
        ],
        'errors'  => [
            'errors'  => [
                [
                    'code'   => 'integer',
                    'title'  => 'string',
                    'detail' => 'string',
                    'data'   => 'array',
                ],
            ],
            'notices' => 'array',
            'data'    => 'array',
        ],
    ];
    /**
     * Идентификатор вида топлива.
     *
     * @var integer|null
     */
    protected $fuelId;

    /**
     * Метод для послетестовых де-инициализаций.
     *
     * @param ApiTester $i Объект текущего тестировщика.
     *
     * @return void
     */
    public function _after(ApiTester $i): void
    {

    }

    /**
     * Метод для предварительных инициализаций перед тестами.
     *
     * @param ApiTester $i Объект текущего тестировщика.
     *
     * @return void
     * @throws _generated\Exception
     * @throws _generated\ModuleException
     */
    public function _before(ApiTester $i): void
    {
        Yii::$app->cache->flush();
        $i->createNewUser(sqs('ApiTester'), [
            'password' => 123123,
            'roleId'   => 1,
        ]);
        $this->fuelId = $i->addFuelEntity();
    }

    /**
     * Передаем: Запрос на получение списка типов транспорта.
     * Ожидаем: Получаем ответ со всеми существующими типами транспорта.
     *
     * @param ApiTester $i Объект текущего тестировщика.
     *
     * @return void
     * @throws _generated\ModuleException
     */
    public function positiveGetVehicleTypeListCheck(ApiTester $i): void
    {
        $i->wantTo('P: Передаем: Запрос на получение списка типов транспорта. 
                            Ожидаем: Получаем ответ со всеми существующими типами транспорта.');
        $vehicleTypeIds = $i->createManyVehicleType([
            [
                'name'            => sqs('firstVehicleType'),
                'load'            => 120.5,
                'fuelConsumption' => 36.4,
                'fuelId'          => $this->fuelId,
            ],
            [
                'name'            => sqs('secondVehicleType'),
                'load'            => 120.5,
                'fuelConsumption' => 36.4,
                'fuelId'          => $this->fuelId,
            ],
            [
                'name'            => sqs('thirdVehicleType'),
                'load'            => 120.5,
                'fuelConsumption' => 36.4,
                'fuelId'          => $this->fuelId,
            ],
        ]);
        $i->login(sqs('ApiTester'), '123123');
        $i->haveHttpHeader('X-HTTP-Method-Override', 'GET');
        $i->sendPOST('/api/v1/vehicle-type');
        $i->seeResponseCodeIs(HttpCode::OK);
        $i->seeResponseContainsJson([
            'errors'  => [],
            'notices' => [],
            'data'    => [
                'list' => [
                    [
                        'id'              => $vehicleTypeIds[sqs('firstVehicleType')],
                        'name'            => sqs('firstVehicleType'),
                        'load'            => 120.5,
                        'fuelConsumption' => 36.4,
                        'fuelId'          => $this->fuelId,
                    ],
                    [
                        'id'              => $vehicleTypeIds[sqs('secondVehicleType')],
                        'name'            => sqs('secondVehicleType'),
                        'load'            => 120.5,
                        'fuelConsumption' => 36.4,
                        'fuelId'          => $this->fuelId,
                    ],
                    [
                        'id'              => $vehicleTypeIds[sqs('thirdVehicleType')],
                        'name'            => sqs('thirdVehicleType'),
                        'load'            => 120.5,
                        'fuelConsumption' => 36.4,
                        'fuelId'          => $this->fuelId,
                    ],
                ],
                'more' => false,
            ],
        ]);
        $i->seeResponseMatchesJsonType($this->responseTypes['correct']);
    }

    /**
     * Передаем: Запрос на получение списка типов транспорта с фильтром по имени.
     * Ожидаем: ответ со всеми существующими типами транспорта подпадающими под критерии фильтра.
     *
     * @param ApiTester $i Объект текущего тестировщика.
     *
     * @return void
     * @throws _generated\ModuleException
     */
    public function positiveGetVehicleTypeListWithFilterByName(ApiTester $i): void
    {
        $i->wantTo('P: Передаем: Запрос на получение списка типов транспорта с фильтром по имени.
                            Ожидаем: ответ со всеми существующими типами транспорта подпадающими под критерии фильтра.');
        $vehicleTypeIds = $i->createManyVehicleType([
            [
                'name'            => sqs('firstVehicleType'),
                'load'            => 110.2,
                'fuelConsumption' => 25.8,
                'fuelId'          => $this->fuelId,
            ],
            [
                'name'            => sqs('secondVehicleType'),
                'load'            => 120.5,
                'fuelConsumption' => 36.4,
                'fuelId'          => $this->fuelId,
            ],
            [
                'name'            => sqs('thirdVehicleType'),
                'load'            => 120.5,
                'fuelConsumption' => 36.4,
                'fuelId'          => $this->fuelId,
            ],
        ]);
        $i->login(sqs('ApiTester'), '123123');
        $i->haveHttpHeader('X-HTTP-Method-Override', 'GET');
        $i->sendPOST('/api/v1/vehicle-type', [
            'filter' => ['name' => sqs('firstVehicleType')],
        ]);
        $i->seeResponseCodeIs(HttpCode::OK);
        $i->seeResponseContainsJson([
            'errors'  => [],
            'notices' => [],
            'data'    => [
                'list' => [
                    [
                        'id'              => $vehicleTypeIds[sqs('firstVehicleType')],
                        'name'            => sqs('firstVehicleType'),
                        'load'            => 110.2,
                        'fuelConsumption' => 25.8,
                        'fuelId'          => $this->fuelId,
                    ],
                ],
                'more' => false,
            ],
        ]);
        $i->dontSeeResponseJsonMatchesJsonPath('$.data.list[1]');
        $i->seeResponseMatchesJsonType($this->responseTypes['correct']);
    }

    /**
     * Передаем: Запрос на получение списка типов транспорта без авторизации.
     * Ожидаем: ответ сервера "Доступ запрещен".
     *
     * @param ApiTester $i Объект текущего тестировщика.
     *
     * @return void
     */
    public function negativeGetVehicleTypeListForbidden(ApiTester $i): void
    {
        $i->wantTo('N: Передаем: Запрос на получение списка типов транспорта без авторизации. 
                            Ожидаем: ответ сервера "Доступ запрещен".');
        $i->haveHttpHeader('X-HTTP-Method-Override', 'GET');
        $i->sendPOST('/api/v1/vehicle-type');
        $i->seeResponseCodeIs(HttpCode::FORBIDDEN);
        $i->seeResponseContainsJson([
            'errors'  => [
                [
                    'code'   => 403,
                    'title'  => 'Доступ запрещен',
                    'detail' => '',
                    'data'   => [],
                ],
            ],
            'notices' => [],
            'data'    => [],
        ]);
        $i->seeResponseMatchesJsonType($this->responseTypes['errors']);
    }
}
