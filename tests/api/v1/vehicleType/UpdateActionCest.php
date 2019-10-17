<?php

declare( strict_types = 1 );

namespace Delivery\CouriersRest\tests;

use Codeception\Util\HttpCode;
use yii;

/**
 * Класс тестирования REST API: обновление сущности "Тип ТС".
 */
class UpdateActionCest
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
     * Идентификатор нового вида топлива.
     *
     * @var integer|null
     */
    protected $updateFuelId;

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
     *
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
        $this->fuelId       = $i->addFuelEntity();
        $this->updateFuelId = $i->addFuelEntity();
    }

    /**
     * Передаем: Запрос на изменение имени типа транспорта, созданного в предусловии, с новыми значениями: грузоподъемность, расход топлива и идентификатор вида топлива.
     * Ожидаем: положительный ответ сервера с содержанием: имени ТС, идентификатора ТС, грузоподъемность, расход топлива и идентификатор вида топлива.
     *
     * @param ApiTester $i
     *
     * @return void
     * @throws _generated\ModuleException
     */
    public function positiveUpdateVehicleTypeCheck(ApiTester $i): void
    {
        $i->wantTo('P: Передаем: Запрос на изменение имени типа транспорта, созданного в предусловии, с новыми значениями: грузоподъемность, расход топлива и идентификатор вида топлива.
                            Ожидаем: положительный ответ сервера с содержанием: имени ТС, идентификатора ТС, грузоподъемность, расход топлива и идентификатор вида топлива.');
        $vehicleTypeId = $i->addVehicleTypeEntity([
            'name'            => 'VehicleType',
            'load'            => 120.5,
            'fuelConsumption' => 36.4,
            'fuelId'          => $this->fuelId,
        ]);
        $i->login(sqs('ApiTester'), '123123');
        $i->haveHttpHeader('X-HTTP-Method-Override', 'PUT');
        $i->sendPOST('/api/v1/vehicle-type/' . $vehicleTypeId, [
            'name'            => 'VehicleType updated',
            'load'            => 22.2,
            'fuelConsumption' => 55.5,
            'fuelId'          => $this->updateFuelId,
        ]);
        $i->seeResponseCodeIs(HttpCode::OK);
        $i->seeResponseContainsJson([
            'errors'  => [],
            'notices' => [],
            'data'    => [
                'id'              => $vehicleTypeId,
                'name'            => 'VehicleType updated',
                'load'            => 22.2,
                'fuelConsumption' => 55.5,
                'fuelId'          => $this->updateFuelId,
            ],
        ]);
        $i->seeResponseMatchesJsonType($this->responseTypes['correct']);
        $i->seeInVehicleTypeTable([
            'id'              => $vehicleTypeId,
            'name'            => 'VehicleType updated',
            'load'            => 22.2,
            'fuelConsumption' => 55.5,
            'fuelId'          => $this->updateFuelId,
            'isDeleted'       => false,
        ]);
    }

    /**
     * Передаем: Запрос на изменение имени типа транспорта, созданного в предусловии с кирилицей, с новыми значениями: грузоподъемность, расход топлива и идентификатор вида топлива.
     * Ожидаем: положительный ответ сервера с содержанием: имени ТС с кирилицей, идентификатора ТС, грузоподъемность, расход топлива и идентификатор вида топлива.
     *
     * @param ApiTester $i
     *
     * @return void
     * @throws _generated\ModuleException
     */
    public function positiveUpdateVehicleTypeNameCyrillic(ApiTester $i): void
    {
        $i->wantTo('P: Передаем: Запрос на изменение имени типа транспорта, созданного в предусловии с кирилицей, с новыми значениями: грузоподъемность, расход топлива и идентификатор вида топлива.
                            Ожидаем: положительный ответ сервера с содержанием: имени ТС с кирилицей, идентификатора ТС, грузоподъемность, расход топлива и идентификатор вида топлива.');
        $vehicleTypeId = $i->addVehicleTypeEntity([
            'name'            => 'VehicleType',
            'load'            => 120.5,
            'fuelConsumption' => 36.4,
            'fuelId'          => $this->fuelId,
        ]);
        $i->login(sqs('ApiTester'), '123123');
        $i->haveHttpHeader('X-HTTP-Method-Override', 'PUT');
        $i->sendPOST('/api/v1/vehicle-type/' . $vehicleTypeId, [
            'name'            => 'Тип транспорта обновленный',
            'load'            => 22.2,
            'fuelConsumption' => 55.5,
            'fuelId'          => $this->updateFuelId,
        ]);
        $i->seeResponseCodeIs(HttpCode::OK);
        $i->seeResponseContainsJson([
            'errors'  => [],
            'notices' => [],
            'data'    => [
                'id'              => $vehicleTypeId,
                'name'            => 'Тип транспорта обновленный',
                'load'            => 22.2,
                'fuelConsumption' => 55.5,
                'fuelId'          => $this->updateFuelId,
            ],
        ]);
        $i->seeResponseMatchesJsonType($this->responseTypes['correct']);
        $i->seeInVehicleTypeTable([
            'id'              => $vehicleTypeId,
            'name'            => 'Тип транспорта обновленный',
            'load'            => 22.2,
            'fuelConsumption' => 55.5,
            'fuelId'          => $this->updateFuelId,
            'isDeleted'       => false,
        ]);
    }

    /**
     * Передаем: Запрос на изменение имени типа транспорта, созданного в предусловии с символами и пробелом, с новыми значениями: грузоподъемность, расход топлива и идентификатор вида топлива.
     * Ожидаем: положительный ответ сервера с содержанием: имени ТС с кирилицей, идентификатора ТС, грузоподъемность, расход топлива и идентификатор вида топлива.
     *
     * @param ApiTester $i
     *
     * @return void
     * @throws _generated\ModuleException
     */
    public function positiveUpdateVehicleTypeNameSymbol(ApiTester $i): void
    {
        $i->wantTo('P: Передаем: Запрос на изменение имени типа транспорта, созданного в предусловии с символами и пробелом, с новыми значениями: грузоподъемность, расход топлива и идентификатор вида топлива.
                            Ожидаем: положительный ответ сервера с содержанием: имени ТС с кирилицей, идентификатора ТС, грузоподъемность, расход топлива и идентификатор вида топлива.');
        $vehicleTypeId = $i->addVehicleTypeEntity([
            'name'            => 'VehicleType',
            'load'            => 120.5,
            'fuelConsumption' => 36.4,
            'fuelId'          => $this->fuelId,
        ]);
        $i->login(sqs('ApiTester'), '123123');
        $i->haveHttpHeader('X-HTTP-Method-Override', 'PUT');
        $i->sendPOST('/api/v1/vehicle-type/' . $vehicleTypeId, [
            'name'            => '/g>!u@e#e$%=^&*()-_a ',
            'load'            => 22.2,
            'fuelConsumption' => 55.5,
            'fuelId'          => $this->updateFuelId,
        ]);
        $i->seeResponseCodeIs(HttpCode::OK);
        $i->seeResponseContainsJson([
            'errors'  => [],
            'notices' => [],
            'data'    => [
                'id'              => $vehicleTypeId,
                'name'            => '/g>!u@e#e$%=^&*()-_a ',
                'load'            => 22.2,
                'fuelConsumption' => 55.5,
                'fuelId'          => $this->updateFuelId,
            ],
        ]);
        $i->seeResponseMatchesJsonType($this->responseTypes['correct']);
        $i->seeInVehicleTypeTable([
            'id'              => $vehicleTypeId,
            'name'            => '/g>!u@e#e$%=^&*()-_a ',
            'load'            => 22.2,
            'fuelConsumption' => 55.5,
            'fuelId'          => $this->updateFuelId,
            'isDeleted'       => false,
        ]);
    }

    /**
     * Передаем: Запрос на изменение имени типа транспорта, созданного в предусловии с числами, с новыми значениями: грузоподъемность, расход топлива и идентификатор вида топлива.
     * Ожидаем: положительный ответ сервера с содержанием: имени ТС с числами, идентификатора ТС, грузоподъемность, расход топлива и идентификатор вида топлива.
     *
     * @param ApiTester $i
     *
     * @return void
     * @throws _generated\ModuleException
     */
    public function positiveUpdateVehicleTypeNameNumber(ApiTester $i): void
    {
        $i->wantTo('P: Передаем: Запрос на изменение имени типа транспорта, созданного в предусловии с числами, с новыми значениями: грузоподъемность, расход топлива и идентификатор вида топлива.
                            Ожидаем: положительный ответ сервера с содержанием: имени ТС с числами, идентификатора ТС, грузоподъемность, расход топлива и идентификатор вида топлива.');
        $vehicleTypeId = $i->addVehicleTypeEntity([
            'name'            => 'VehicleType',
            'load'            => 120.5,
            'fuelConsumption' => 36.4,
            'fuelId'          => $this->fuelId,
        ]);
        $i->login(sqs('ApiTester'), '123123');
        $i->haveHttpHeader('X-HTTP-Method-Override', 'PUT');
        $i->sendPOST('/api/v1/vehicle-type/' . $vehicleTypeId, [
            'name'            => '1234567890',
            'load'            => 22.2,
            'fuelConsumption' => 55.5,
            'fuelId'          => $this->updateFuelId,
        ]);
        $i->seeResponseCodeIs(HttpCode::OK);
        $i->seeResponseContainsJson([
            'errors'  => [],
            'notices' => [],
            'data'    => [
                'id'              => $vehicleTypeId,
                'name'            => '1234567890',
                'load'            => 22.2,
                'fuelConsumption' => 55.5,
                'fuelId'          => $this->updateFuelId,
            ],
        ]);
        $i->seeResponseMatchesJsonType($this->responseTypes['correct']);
        $i->seeInVehicleTypeTable([
            'id'              => $vehicleTypeId,
            'name'            => '1234567890',
            'load'            => 22.2,
            'fuelConsumption' => 55.5,
            'fuelId'          => $this->updateFuelId,
            'isDeleted'       => false,
        ]);
    }

    /**
     * Передаем: Запрос на изменение типа транспорта не авторизовавшись в системе сгенерированное случайное имя, идентификатор вида топлива, грузоподъемность и расход топлива.
     * Ожидаем: ответ сервера "Доступ запрещен".
     *
     * @param ApiTester $i
     *
     * @return void
     * @throws _generated\ModuleException
     */
    public function negativeUpdateVehicleTypeForbidden(ApiTester $i): void
    {
        $i->wantTo('N: Передаем: Запрос на изменение типа транспорта не авторизовавшись в системе сгенерированное случайное имя, идентификатор вида топлива, грузоподъемность и расход топлива.
                            Ожидаем: ответ сервера "Доступ запрещен".');
        $vehicleTypeId = $i->addVehicleTypeEntity([
            'name'            => 'VehicleType for update',
            'load'            => 120.5,
            'fuelConsumption' => 36.4,
            'fuelId'          => $this->fuelId,
        ]);
        $i->haveHttpHeader('X-HTTP-Method-Override', 'PUT');
        $i->sendPOST('/api/v1/vehicle-type/' . $vehicleTypeId, [
            'name'            => 'VehicleType new',
            'load'            => 22.2,
            'fuelConsumption' => 55.5,
            'fuelId'          => $this->updateFuelId,
        ]);
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
            'name'            => 'VehicleType for update',
            'load'            => 120.5,
            'fuelConsumption' => 36.4,
            'fuelId'          => $this->fuelId,
            'isDeleted'       => false,
        ]);
    }
}
