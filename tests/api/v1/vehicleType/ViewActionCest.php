<?php

declare( strict_types = 1 );

namespace Delivery\CouriersRest\tests;

use Codeception\Util\HttpCode;
use yii;

/**
 * Класс тестирования REST API: просмотр одной сущности "Тип ТС".
 */
class ViewActionCest
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
                'id'              => 'integer',
                'name'            => 'string',
                'load'            => 'float',
                'fuelConsumption' => 'float',
                'fuelId'          => 'integer',
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
     * Передаем: Запрос на получение типа транспорта, созданного в предусловии со сгенерированным случайным именем ТС, грузоподъемность, расход топлива и идентификатор вида топлива.
     * Ожидаем: положительный ответ сервера с содержанием: имени ТС, идентификатора ТС, грузоподъемности, расхода топлива, и идентификатора вида топлива.
     *
     * @param ApiTester $i Объект текущего тестировщика.
     *
     * @return void
     * @throws _generated\ModuleException
     */
    public function positiveViewVehicleTypeCheck(ApiTester $i): void
    {
        $i->wantTo('P: Передаем: Запрос на получение типа транспорта, созданного в предусловии со сгенерированным случайным именем ТС, грузоподъемность = 120.5, расход топлива = 36.4 и идентификатор вида топлива.
                            Ожидаем: положительный ответ сервера с содержанием: имени ТС, идентификатора ТС, грузоподъемности, расхода топлива, и идентификатора вида топлива.');
        $vehicleTypeId = $i->addVehicleTypeEntity([
            'name'            => sqs('VehicleTypeForView'),
            'load'            => 120.5,
            'fuelConsumption' => 36.4,
            'fuelId'          => $this->fuelId,
        ]);
        $i->login(sqs('ApiTester'), '123123');
        $i->haveHttpHeader('X-HTTP-Method-Override', 'GET');
        $i->sendPOST('/api/v1/vehicle-type/' . $vehicleTypeId);
        $i->seeResponseCodeIs(HttpCode::OK);
        $i->seeResponseContainsJson([
            'errors'  => [],
            'notices' => [],
            'data'    => [
                'name'            => sqs('VehicleTypeForView'),
                'id'              => $vehicleTypeId,
                'load'            => 120.5,
                'fuelConsumption' => 36.4,
                'fuelId'          => $this->fuelId,
            ],
        ]);
        $i->seeResponseMatchesJsonType($this->responseTypes['correct']);
    }

    /**
     * Передаем: Передаем: Запрос на получение типа транспорта не авторизовавшись.
     * Ожидаем: ответ сервера "Доступ запрещен".
     *
     * @param ApiTester $i Объект текущего тестировщика.
     *
     * @return void
     * @throws _generated\ModuleException
     */
    public function negativeViewVehicleTypeForbidden(ApiTester $i): void
    {
        $i->wantTo('N: Передаем: Запрос на получение типа транспорта не авторизовавшись.
        Ожидаем: ответ сервера "Доступ запрещен".');
        $vehicleTypeId = $i->addVehicleTypeEntity([
            'name'            => sqs('VehicleTypeForView'),
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
    }
}
