<?php

declare( strict_types = 1 );

namespace Delivery\CouriersRest\tests;

use Codeception\Util\HttpCode;
use yii;

/**
 * Класс тестирования REST API: удаление сущности "Тип ТС".
 */
class DeleteActionCest
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
                'success' => 'boolean',
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
        // TODO do it.
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
     * Передаем: Запрос на удаление типа транспорта.
     * Ожидаем: положительный ответ сервера на удаление.
     *
     * @param ApiTester $i Объект текущего тестировщика.
     *
     * @return void
     * @throws _generated\ModuleException
     */
    public function positiveDeleteVehicleTypeCheck(ApiTester $i): void
    {
        $i->wantTo('P: Передаем: Запрос на удаление типа транспорта. 
                            Ожидаем: положительный ответ сервера на удаление.');
        $vehicleTypeId = $i->addVehicleTypeEntity([
            'name'            => 'Vehicle Type',
            'load'            => 120.5,
            'fuelConsumption' => 36.4,
            'fuelId'          => $this->fuelId,
        ]);
        $i->login(sqs('ApiTester'), '123123');
        $i->haveHttpHeader('X-HTTP-Method-Override', 'DELETE');
        $i->sendPOST('/api/v1/vehicle-type/' . $vehicleTypeId);
        $i->seeResponseCodeIs(HttpCode::OK);
        $i->seeResponseContainsJson([
            'errors'  => [],
            'notices' => [],
            'data'    => [
                'success' => true,
            ],
        ]);
        $i->seeResponseMatchesJsonType($this->responseTypes['correct']);
        $i->seeInVehicleTypeTable([
            'id'              => $vehicleTypeId,
            'name'            => 'Vehicle Type',
            'load'            => 120.5,
            'fuelConsumption' => 36.4,
            'fuelId'          => $this->fuelId,
            'isDeleted'       => true,
        ]);
    }

    /**
     * Передаем: Передаем: Запрос на удаление типа транспорта не авторизовавшись.
     * Ожидаем: ответ сервера "Доступ запрещен".
     *
     * @param ApiTester $i Объект текущего тестировщика.
     *
     * @return void
     * @throws _generated\ModuleException
     */
    public function negativeDeleteVehicleTypeForbidden(ApiTester $i): void
    {
        $i->wantTo('N: Передаем: Запрос на удаление типа транспорта не авторизовавшись.
                            Ожидаем: ответ сервера "Доступ запрещен".');
        $vehicleTypeId = $i->addVehicleTypeEntity([
            'name'            => 'Vehicle Type',
            'load'            => 120.5,
            'fuelConsumption' => 36.4,
            'fuelId'          => $this->fuelId,
        ]);
        $i->haveHttpHeader('X-HTTP-Method-Override', 'DELETE');
        $i->sendPOST('/api/v1/vehicle-type/' . $vehicleTypeId);
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
        $i->seeInVehicleTypeTable([
            'id'              => $vehicleTypeId,
            'name'            => 'Vehicle Type',
            'load'            => 120.5,
            'fuelConsumption' => 36.4,
            'fuelId'          => $this->fuelId,
            'isDeleted'       => false,
        ]);
    }
}
