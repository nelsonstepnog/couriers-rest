<?php

declare( strict_types = 1 );

namespace Delivery\CouriersRest\tests;

use Codeception\Util\HttpCode;
use yii;

/**
 * Класс тестирования REST API: создание новой сущности "Тип ТС".
 */
class CreateActionCest
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
        $this->fuelId = $i->addFuelEntity();
    }

    /**
     * Передаем: Сгенерированное случайное имя, идентификатор ТС, грузоподъемность, расход топлива и идентификатор вида топлива.
     * Ожидаем: положительный ответ сервера с содержанием имени, идентификатора ТС, грузоподъемности, расхода топлива и идентификатора вида топлива.
     *
     * Метод проверяет позитивный кейс.
     *
     * @param ApiTester $i Объект текущего тестировщика.
     *
     * @return void
     *
     * @throws _generated\ModuleException
     */
    public function positiveCreateVehicleTypeCheck(ApiTester $i): void
    {
        $i->wantTo('P: Передаем: Сгенерированное имя, грузоподъемность, расход топлива и идентификатор вида топлива.
                            Ожидаем: Сгенерированное имя, идентификатор ТС, грузоподъемность, расход топлива и идентификатор вида топлива.');
        $i->login(sqs('ApiTester'), '123123');
        $i->haveHttpHeader('X-HTTP-Method-Override', 'CREATE');
        $i->sendPOST('/api/v1/vehicle-type', [
            'name'            => sqs('Vehicle Type'),
            'load'            => 120.5,
            'fuelConsumption' => 36.4,
            'fuelId'          => $this->fuelId,
        ]);
        $i->seeResponseCodeIs(HttpCode::OK);
        $i->seeInVehicleTypeTable([
            'name'            => sqs('Vehicle Type'),
            'load'            => 120.5,
            'fuelConsumption' => 36.4,
            'fuelId'          => $this->fuelId,
        ]);
        $id = $i->grabFromVehicleTypeTable('id', ['name' => sqs('Vehicle Type')]);
        $i->seeResponseContainsJson([
            'errors'  => [],
            'notices' => [],
            'data'    => [
                'name'            => sqs('Vehicle Type'),
                'id'              => $id,
                'load'            => 120.5,
                'fuelConsumption' => 36.4,
                'fuelId'          => $this->fuelId,
            ],
        ]);
        $i->seeResponseMatchesJsonType($this->responseTypes['correct']);
    }

    /**
     * Передаем: Сгенерированное случайное имя с кириллицей, грузоподъемность, расход топлива и идентификатор вида топлива.
     * Ожидаем: положительный ответ сервера с содержанием имени с кириллицей, идентификатора ТС, грузоподъемности, расхода топлива и идентификатора вида топлива.
     *
     * Метод проверяет позитивный кейс.
     *
     * @param ApiTester $i Объект текущего тестировщика.
     *
     * @return void
     * @throws _generated\ModuleException
     */
    public function positiveCreateVehicleTypeNameCyrillic(ApiTester $i): void
    {
        $i->wantTo('P: Передаем: Сгенерированное имя с кириллицей, грузоподъемность, расход топлива и идентификатор вида топлива.
                            Ожидаем: Сгенерированное имя с кириллицей, идентификатор ТС, грузоподъемность, расход топлива и идентификатор вида топлива.');
        $i->login(sqs('ApiTester'), '123123');
        $i->haveHttpHeader('X-HTTP-Method-Override', 'CREATE');
        $i->sendPOST('/api/v1/vehicle-type', [
            'name'            => sqs('Тип транспорта'),
            'load'            => 120.5,
            'fuelConsumption' => 36.4,
            'fuelId'          => $this->fuelId,
        ]);
        $i->seeResponseCodeIs(HttpCode::OK);
        $i->seeInVehicleTypeTable([
            'name'            => sqs('Тип транспорта'),
            'load'            => 120.5,
            'fuelConsumption' => 36.4,
            'fuelId'          => $this->fuelId,
        ]);
        $id = $i->grabFromVehicleTypeTable('id', ['name' => sqs('Тип транспорта')]);
        $i->seeResponseContainsJson([
            'errors'  => [],
            'notices' => [],
            'data'    => [
                'name'            => sqs('Тип транспорта'),
                'id'              => $id,
                'load'            => 120.5,
                'fuelConsumption' => 36.4,
                'fuelId'          => $this->fuelId,
            ],
        ]);
        $i->seeResponseMatchesJsonType($this->responseTypes['correct']);
    }

    /**
     * Передаем: Сгенерированное случайное имя с символами и пробелом, грузоподъемность, расход топлива и идентификатор вида топлива.
     * Ожидаем: положительный ответ сервера с содержанием имени с символами, идентификатора ТС, грузоподъемности, расхода топлива и идентификатора вида топлива.
     *
     * Метод проверяет позитивный кейс.
     *
     * @param ApiTester $i Объект текущего тестировщика.
     *
     * @return void
     * @throws _generated\ModuleException
     */
    public function positiveCreateVehicleTypeNameSymbol(ApiTester $i): void
    {
        $i->wantTo('P: Передаем: Сгенерированное имя с символами и пробелом, грузоподъемность, расход топлива и идентификатор вида топлива.
                            Ожидаем: Сгенерированное имя с символами, идентификатор ТС, грузоподъемность, расход топлива и идентификатор вида топлива.');
        $i->login(sqs('ApiTester'), '123123');
        $i->haveHttpHeader('X-HTTP-Method-Override', 'CREATE');
        $i->sendPOST('/api/v1/vehicle-type', [
            'name'            => sqs('/g>!u@e#e$%=^&*()-_a '),
            'load'            => 120.5,
            'fuelConsumption' => 36.4,
            'fuelId'          => $this->fuelId,
        ]);
        $i->seeResponseCodeIs(HttpCode::OK);
        $i->seeInVehicleTypeTable([
            'name'            => sqs('/g>!u@e#e$%=^&*()-_a '),
            'load'            => 120.5,
            'fuelConsumption' => 36.4,
            'fuelId'          => $this->fuelId,
        ]);
        $id = $i->grabFromVehicleTypeTable('id', ['name' => sqs('/g>!u@e#e$%=^&*()-_a ')]);
        $i->seeResponseContainsJson([
            'errors'  => [],
            'notices' => [],
            'data'    => [
                'name'            => sqs('/g>!u@e#e$%=^&*()-_a '),
                'id'              => $id,
                'load'            => 120.5,
                'fuelConsumption' => 36.4,
                'fuelId'          => $this->fuelId,
            ],
        ]);
        $i->seeResponseMatchesJsonType($this->responseTypes['correct']);
    }

    /**
     * Передаем: Сгенерированное случайное имя с числом, грузоподъемность, расход топлива и идентификатор вида топлива.
     * Ожидаем: положительный ответ сервера с содержанием имени с числом, идентификатора ТС, грузоподъемности, расхода топлива и идентификатора вида топлива.
     *
     * Метод проверяет позитивный кейс.
     *
     * @param ApiTester $i Объект текущего тестировщика.
     *
     * @return void
     * @throws _generated\ModuleException
     */
    public function positiveCreateVehicleTypeNameNumber(ApiTester $i): void
    {
        $i->wantTo('P: Передаем: Сгенерированное имя с числом, грузоподъемность, расход топлива и идентификатор вида топлива.
                            Ожидаем: Сгенерированное имя с числом, идентификатор ТС, грузоподъемность, расход топлива и идентификатор вида топлива.');
        $i->login(sqs('ApiTester'), '123123');
        $i->haveHttpHeader('X-HTTP-Method-Override', 'CREATE');
        $i->sendPOST('/api/v1/vehicle-type', [
            'name'            => sqs('1234567890'),
            'load'            => 120.5,
            'fuelConsumption' => 36.4,
            'fuelId'          => $this->fuelId,
        ]);
        $i->seeResponseCodeIs(HttpCode::OK);
        $i->seeInVehicleTypeTable([
            'name'            => sqs('1234567890'),
            'load'            => 120.5,
            'fuelConsumption' => 36.4,
            'fuelId'          => $this->fuelId,
        ]);
        $id = $i->grabFromVehicleTypeTable('id', ['name' => sqs('1234567890')]);
        $i->seeResponseContainsJson([
            'errors'  => [],
            'notices' => [],
            'data'    => [
                'name'            => sqs('1234567890'),
                'id'              => $id,
                'load'            => 120.5,
                'fuelConsumption' => 36.4,
                'fuelId'          => $this->fuelId,
            ],
        ]);
        $i->seeResponseMatchesJsonType($this->responseTypes['correct']);
    }

    /**
     * Передаем: Запрос, в котором передаём флаг isDeleted.
     * Ожидаем: Положительный ответ сервера, но в созданной сущности этот флаг проигнорирован.
     *
     * @param ApiTester $i Объект текущего тестировщика.
     *
     * @return void
     * @throws _generated\ModuleException
     */
    public function positiveCreateVehicleTypeWithIsDeleted(ApiTester $i): void
    {
        $i->wantTo('P: Передаем: Запрос, в котором передаём флаг isDeleted. 
                            Ожидаем: Положительный ответ сервера, но в созданной сущности этот флаг проигнорирован.');
        $i->login(sqs('ApiTester'), '123123');
        $i->haveHttpHeader('X-HTTP-Method-Override', 'CREATE');
        $i->sendPOST('/api/v1/vehicle-type', [
            'name'            => sqs('Vehicle Type'),
            'load'            => 120.5,
            'fuelConsumption' => 36.4,
            'fuelId'          => $this->fuelId,
            'isDeleted'       => true,
        ]);
        $i->seeResponseCodeIs(HttpCode::OK);
        $id = $i->grabFromVehicleTypeTable('id', ['name' => sqs('Vehicle Type')]);
        $i->seeResponseContainsJson([
            'errors'  => [],
            'notices' => [],
            'data'    => [
                'name'            => sqs('Vehicle Type'),
                'id'              => $id,
                'load'            => 120.5,
                'fuelConsumption' => 36.4,
                'fuelId'          => $this->fuelId,
            ],
        ]);
        $i->seeResponseMatchesJsonType($this->responseTypes['correct']);
        $i->seeInVehicleTypeTable([
            'id'              => $id,
            'name'            => sqs('Vehicle Type'),
            'load'            => 120.5,
            'fuelConsumption' => 36.4,
            'fuelId'          => $this->fuelId,
            'isDeleted'       => false,
        ]);
    }

    /**
     * Передаем: Строку превышающую 255 символов в поле name, грузоподъемность, расход топлива и идентификатор вида топлива.
     * Ожидаем: отрицательный ответ сервера о том, что поле name может содержать максимум 255 символов.
     *
     * @param ApiTester $i Объект текущего тестировщика.
     *
     * @return void
     */
    public function negativeCreateVehicleTypeOutOfStrName(ApiTester $i): void
    {
        $i->wantTo('N: Передаем: Строку превышающую 255 символов в поле name, грузоподъемность, расход топлива и идентификатор вида топлива. 
                            Ожидаем: отрицательный ответ сервера о том, что поле name может содержать максимум 255 символов.');
        $i->login(sqs('ApiTester'), '123123');
        $i->haveHttpHeader('X-HTTP-Method-Override', 'CREATE');
        $i->sendPOST('/api/v1/vehicle-type', [
            'name'            => str_repeat('a', 256),
            'load'            => 120.5,
            'fuelConsumption' => 36.4,
            'fuelId'          => $this->fuelId,
        ]);
        $i->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $i->seeResponseContainsJson([
            'errors'  => [
                [
                    'code'   => 422,
                    'title'  => '',
                    'detail' => '',
                    'data'   => [
                        'name' => 'Значение «Name» должно содержать максимум 255 символов.',
                    ],
                ],
            ],
            'notices' => [],
            'data'    => [],
        ]);
        $i->seeResponseMatchesJsonType($this->responseTypes['errors']);
    }

    /**
     * Передаем: Пустое значение в обязательное поле name, грузоподъемность, расход топлива и идентификатор вида топлива.
     * Ожидаем: Отрицательный ответ сервера о том, что поле name не заполнено.
     *
     * @param ApiTester $i Объект текущего тестировщика.
     *
     * @return void
     * @throws _generated\ModuleException
     */
    public function negativeCreateVehicleTypeNameEmpty(ApiTester $i): void
    {
        $i->wantTo('N: Передаем: Пустое значение в обязательное поле name, грузоподъемность, расход топлива и идентификатор вида топлива.
                            Ожидаем: отрицательный ответ сервера о том, что поле name не заполнено.');
        $i->login(sqs('ApiTester'), '123123');
        $i->haveHttpHeader('X-HTTP-Method-Override', 'CREATE');
        $i->sendPOST('/api/v1/vehicle-type', [
            'name'            => '',
            'load'            => 120.5,
            'fuelConsumption' => 36.4,
            'fuelId'          => $this->fuelId,
        ]);
        $i->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $i->seeResponseContainsJson([
            'errors'  => [
                [
                    'code'   => 422,
                    'title'  => '',
                    'detail' => '',
                    'data'   => [
                        'name' => 'Необходимо заполнить «Name».',
                    ],
                ],
            ],
            'notices' => [],
            'data'    => [],
        ]);
        $i->seeResponseMatchesJsonType($this->responseTypes['errors']);
        $i->dontSeeInVehicleTypeTable([
            'name' => '',
        ]);
    }

    /**
     * Передаем: Пустое значение в обязательное поле load, расход топлива идентификатор вида топлива и сгенерированное случайное имя в обязательное поле name.
     * Ожидаем: Отрицательный ответ сервера о том, что поле load не заполнено.
     *
     * @param ApiTester $i Объект текущего тестировщика.
     *
     * @return void
     * @throws _generated\ModuleException
     */
    public function negativeCreateVehicleTypeLoadEmpty(ApiTester $i): void
    {
        $i->wantTo('N: Передаем: Пустое значение в обязательное поле load, расход топлива идентификатор вида топлива и сгенерированное случайное имя в обязательное поле name.
                            Ожидаем: отрицательный ответ сервера о том, что поле load не заполнено.');
        $i->login(sqs('ApiTester'), '123123');
        $i->haveHttpHeader('X-HTTP-Method-Override', 'CREATE');
        $i->sendPOST('/api/v1/vehicle-type', [
            'name'            => sqs('Vehicle'),
            'fuelConsumption' => 36.4,
            'load'            => '',
            'fuelId'          => $this->fuelId,
        ]);
        $i->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $i->seeResponseContainsJson([
            'errors'  => [
                [
                    'code'   => 422,
                    'title'  => '',
                    'detail' => '',
                    'data'   => [
                        'load' => 'Необходимо заполнить «Load».',
                    ],
                ],
            ],
            'notices' => [],
            'data'    => [],
        ]);
        $i->seeResponseMatchesJsonType($this->responseTypes['errors']);
    }

    /**
     * Передаем: Пустое значение в обязательное поле fuel consumption, грузоподъёмность, идентификатор вида топлива и сгенерированное случайное имя в обязательное поле name.
     * Ожидаем: Отрицательный ответ сервера о том, что поле load не заполнено.
     *
     * @param ApiTester $i Объект текущего тестировщика.
     *
     * @return void
     * @throws _generated\ModuleException
     */
    public function negativeCreateVehicleTypeFCEmpty(ApiTester $i): void
    {
        $i->wantTo('N: Передаем: Пустое значение в обязательное поле fuel consumption, грузоподъёмность, идентификатор вида топлива и сгенерированное случайное имя в обязательное поле name.
                            Ожидаем: отрицательный ответ сервера о том, что поле load не заполнено.');
        $i->login(sqs('ApiTester'), '123123');
        $i->haveHttpHeader('X-HTTP-Method-Override', 'CREATE');
        $i->sendPOST('/api/v1/vehicle-type', [
            'name'            => sqs('Vehicle'),
            'fuelConsumption' => '',
            'load'            => 120.5,
            'fuelId'          => $this->fuelId,
        ]);
        $i->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $i->seeResponseContainsJson([
            'errors'  => [
                [
                    'code'   => 422,
                    'title'  => '',
                    'detail' => '',
                    'data'   => [
                        'fuelConsumption' => 'Необходимо заполнить «Fuel Consumption».',
                    ],
                ],
            ],
            'notices' => [],
            'data'    => [],
        ]);
        $i->seeResponseMatchesJsonType($this->responseTypes['errors']);
    }

    /**
     * Передаем: Строку с числом превышающим 15 знаков после запятой в поле load, расход топлива, идентификатор вида топлива и сгенерированное случайное имя в обязательное поле name.
     * Ожидаем: Отрицательный ответ сервера о том, что значение в поле load превышено.
     *
     * @param ApiTester $i Объект текущего тестировщика.
     *
     * @return void
     * @throws _generated\ModuleException
     */
    public function negativeCreateVehicleTypeOutOfStrLoad(ApiTester $i): void
    {
        $i->wantTo('N: Передаем: Строку с числом превышающим 15 знаков после запятой в поле load, расход топлива, идентификатор вида топлива и сгенерированное случайное имя в обязательное поле name. 
                            Ожидаем: Отрицательный ответ сервера о том, что значение в поле load превышено.');
        $i->login(sqs('ApiTester'), '123123');
        $i->haveHttpHeader('X-HTTP-Method-Override', 'CREATE');
        $i->sendPOST('/api/v1/vehicle-type', [
            'name'            => sqs('Vehicle'),
            'load'            => 1.1111111111111111111111111111,
            'fuelConsumption' => 36.4,
            'fuelId'          => $this->fuelId,
        ]);
        $i->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $i->seeResponseContainsJson([
            'errors'  => [
                [
                    'code'   => 422,
                    'title'  => '',
                    'detail' => '',
                    'data'   => [
                        'load' => 'Значение «Load» превышено.',
                    ],
                ],
            ],
            'notices' => [],
            'data'    => [],
        ]);
        $i->seeResponseMatchesJsonType($this->responseTypes['errors']);
    }

    /**
     * Передаем: Строку с числом превышающим 15 знаков после запятой в поле fuel consumption, грузоподъёмность, идентификатор вида топлива и сгенерированное случайное имя в обязательное поле name.
     * Ожидаем: Отрицательный ответ сервера о том, что значение в поле fuel consumption превышено.
     *
     * @param ApiTester $i Объект текущего тестировщика.
     *
     * @return void
     * @throws _generated\ModuleException
     */
    public function negativeCreateVehicleTypeOutOfStrFC(ApiTester $i): void
    {
        $i->wantTo('N: Передаем: Строку с числом превышающим 15 знаков после запятой в поле fuel consumption, грузоподъёмность, идентификатор вида топлива и сгенерированное случайное имя в обязательное поле name. 
                            Ожидаем: Отрицательный ответ сервера о том, что значение в поле fuel consumption превышено.');
        $i->login(sqs('ApiTester'), '123123');
        $i->haveHttpHeader('X-HTTP-Method-Override', 'CREATE');
        $i->sendPOST('/api/v1/vehicle-type', [
            'name'            => sqs('Vehicle'),
            'load'            => 120.5,
            'fuelConsumption' => 2.222222222222222222222222222222,
            'fuelId'          => $this->fuelId,
        ]);
        $i->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $i->seeResponseContainsJson([
            'errors'  => [
                [
                    'code'   => 422,
                    'title'  => '',
                    'detail' => '',
                    'data'   => [
                        'fuelConsumption' => 'Значение «Fuel Consumption» превышено.',
                    ],
                ],
            ],
            'notices' => [],
            'data'    => [],
        ]);
        $i->seeResponseMatchesJsonType($this->responseTypes['errors']);
    }

    /**
     * Передаем: Строку в поле load в буквенном виде, расход топлива, идентификатор вида топлива и сгенерированное случайное имя в обязательное поле name.
     * Ожидаем: Отрицательный ответ сервера о том, что значение «Load» должно быть числом.
     *
     * Метод проверяет негативный кейс.
     *
     * @param ApiTester $i Объект текущего тестировщика.
     *
     * @return void
     * @throws _generated\ModuleException
     */
    public function negativeCreateVehicleTypeLoadChar(ApiTester $i): void
    {
        $i->wantTo('N: Передаём: Строку в поле load в буквенном виде, расход топлива, идентификатор вида топлива и сгенерированное случайное имя в обязательное поле name.
                            Ожидаем: Ожидаем: Отрицательный ответ сервера о том, что значение «Load» должно быть числом.');
        $i->login(sqs('ApiTester'), '123123');
        $i->haveHttpHeader('X-HTTP-Method-Override', 'CREATE');
        $i->sendPOST('/api/v1/vehicle-type', [
            'name'            => sqs('Vehicle'),
            'load'            => 'LOAD',
            'fuelConsumption' => 36.4,
            'fuelId'          => $this->fuelId,
        ]);
        $i->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $i->seeResponseContainsJson([
            'errors'  => [
                [
                    'code'   => 422,
                    'title'  => '',
                    'detail' => '',
                    'data'   => [
                        'load' => 'Значение «Load» должно быть числом.',
                    ],
                ],
            ],
            'notices' => [],
            'data'    => [],
        ]);
        $i->seeResponseMatchesJsonType($this->responseTypes['errors']);
    }

    /**
     * Передаем: Строку в поле fuel consumption в буквенном виде, грузоподъёмность, идентификатор вида топлива и сгенерированное случайное имя в обязательное поле name.
     * Ожидаем: Отрицательный ответ сервера о том, что значение «Fuel Consumption» должно быть числом..
     *
     * Метод проверяет негативный кейс.
     *
     * @param ApiTester $i Объект текущего тестировщика.
     *
     * @return void
     * @throws _generated\ModuleException
     */
    public function negativeCreateVehicleTypeFCChar(ApiTester $i): void
    {
        $i->wantTo('N: Передаём: Строку в поле fuel consumption в буквенном виде, грузоподъёмность, идентификатор вида топлива и сгенерированное случайное имя в обязательное поле name.
                            Ожидаем: Отрицательный ответ сервера о том, что значение «Fuel Consumption» должно быть числом.');
        $i->login(sqs('ApiTester'), '123123');
        $i->haveHttpHeader('X-HTTP-Method-Override', 'CREATE');
        $i->sendPOST('/api/v1/vehicle-type', [
            'name'            => sqs('Vehicle'),
            'load'            => 120.5,
            'fuelConsumption' => 'FUEL CONSUMPTION',
            'fuelId'          => $this->fuelId,
        ]);
        $i->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $i->seeResponseContainsJson([
            'errors'  => [
                [
                    'code'   => 422,
                    'title'  => '',
                    'detail' => '',
                    'data'   => [
                        'fuelConsumption' => 'Значение «Fuel Consumption» должно быть числом.',
                    ],
                ],
            ],
            'notices' => [],
            'data'    => [],
        ]);
        $i->seeResponseMatchesJsonType($this->responseTypes['errors']);
    }

    /**
     * Передаем: Строку в поле load в символьном виде, расход топлива, идентификатор вида топлива и сгенерированное случайное имя в обязательное поле name.
     * Ожидаем: Отрицательный ответ сервера о том, что значение «Load» должно быть числом..
     *
     * Метод проверяет негативный кейс.
     *
     * @param ApiTester $i Объект текущего тестировщика.
     *
     * @return void
     * @throws _generated\ModuleException
     */
    public function negativeCreateVehicleTypeLoadSymbol(ApiTester $i): void
    {
        $i->wantTo('N: Передаем: Строку в поле load в символьном виде, расход топлива, идентификатор вида топлива и сгенерированное случайное имя в обязательное поле name.
                            Ожидаем: Отрицательный ответ сервера о том, что значение «Load» должно быть числом.');
        $i->login(sqs('ApiTester'), '123123');
        $i->haveHttpHeader('X-HTTP-Method-Override', 'CREATE');
        $i->sendPOST('/api/v1/vehicle-type', [
            'name'            => sqs('Vehicle'),
            'load'            => '/g>!u@e#e$%=^&*()-_a ',
            'fuelConsumption' => 36.4,
            'fuelId'          => $this->fuelId,
        ]);
        $i->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $i->seeResponseContainsJson([
            'errors'  => [
                [
                    'code'   => 422,
                    'title'  => '',
                    'detail' => '',
                    'data'   => [
                        'load' => 'Значение «Load» должно быть числом.',
                    ],
                ],
            ],
            'notices' => [],
            'data'    => [],
        ]);
        $i->seeResponseMatchesJsonType($this->responseTypes['errors']);
    }

    /**
     * Передаем: Строку в поле fuel consumption в символьном виде, грузоподъёмность, идентификатор вида топлива и сгенерированное случайное имя в обязательное поле name.
     * Ожидаем: Отрицательный ответ сервера о том, что значение «Fuel Consumption» должно быть числом.
     *
     * Метод проверяет негативный кейс.
     *
     * @param ApiTester $i Объект текущего тестировщика.
     *
     * @return void
     * @throws _generated\ModuleException
     */
    public function negativeCreateVehicleTypeFCSymbol(ApiTester $i): void
    {
        $i->wantTo('N: Передаем: Строку в поле fuel consumption в символьном виде, грузоподъёмность, идентификатор вида топлива и сгенерированное случайное имя в обязательное поле name.
                            Ожидаем: Отрицательный ответ сервера о том, что значение «Fuel Consumption» должно быть числом.');
        $i->login(sqs('ApiTester'), '123123');
        $i->haveHttpHeader('X-HTTP-Method-Override', 'CREATE');
        $i->sendPOST('/api/v1/vehicle-type', [
            'name'            => sqs('Vehicle'),
            'load'            => 120.5,
            'fuelConsumption' => '/g>!u@e#e$%=^&*()-_a ',
            'fuelId'          => $this->fuelId,
        ]);
        $i->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $i->seeResponseContainsJson([
            'errors'  => [
                [
                    'code'   => 422,
                    'title'  => '',
                    'detail' => '',
                    'data'   => [
                        'fuelConsumption' => 'Значение «Fuel Consumption» должно быть числом.',
                    ],
                ],
            ],
            'notices' => [],
            'data'    => [],
        ]);
        $i->seeResponseMatchesJsonType($this->responseTypes['errors']);
    }

    /**
     * Передаем: C несуществующим идентификатором вида топлива сгенерированное случайное имя, грузоподъемность, расход топлива.
     * Ожидаем: Отрицательный ответ сервера об отсутствии такого идентификатора вида топлива.
     *
     * Метод негативный кейс.
     *
     * @param ApiTester $i Объект текущего тестировщика.
     *
     * @return void
     *
     * @throws _generated\ModuleException
     */
    public function negativeCreateVehicleTypeWrongFuelID(ApiTester $i): void
    {
        $i->wantTo('N: Передаем: C несуществующим идентификатором вида топлива сгенерированное случайное имя, грузоподъемность, расход топлива.
                            Ожидаем: Отрицательный ответ сервера об отсутствии идентификатора вида топлива.');
        $i->login(sqs('ApiTester'), '123123');
        $i->haveHttpHeader('X-HTTP-Method-Override', 'CREATE');
        $i->sendPOST('/api/v1/vehicle-type', [
            'name'            => sqs('Vehicle'),
            'load'            => 120.5,
            'fuelConsumption' => 36.4,
            'fuelId'          => '999999',
        ]);
        $i->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $i->dontSeeInVehicleTypeTable(['name' => sqs('Vehicle')]);
        $i->seeResponseContainsJson([
            'errors'  => [
                [
                    'code'   => 422,
                    'title'  => '',
                    'detail' => '',
                    'data'   => [
                        'fuelId' => 'Значение «Fuel Id» неверно.',
                    ],
                ],
            ],
            'notices' => [],
            'data'    => [],
        ]);
    }

    /**
     * Передаем: Не авторизовавшись в системе сгенерированное случайное имя, идентификатор вида топлива, грузоподъемность и расход топлива.
     * Ожидаем: ответ сервера "Доступ запрещен".
     *
     * @param ApiTester $i Объект текущего тестировщика.
     *
     * @return void
     * @throws _generated\ModuleException
     */
    public function negativeCreateVehicleTypeForbidden(ApiTester $i): void
    {
        $i->wantTo('N: Передаем: Не авторизовавшись в системе сгенерированное случайное имя, идентификатор вида топлива, грузоподъемность и расход топлива.
                            Ожидаем: ответ сервера "Доступ запрещен".');
        $i->haveHttpHeader('X-HTTP-Method-Override', 'CREATE');
        $i->sendPOST('/api/v1/vehicle-type', [
            'name'            => sqs('Forbidden Vehicle Type'),
            'load'            => 120.5,
            'fuelConsumption' => 36.4,
            'fuelId'          => $this->fuelId,
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
        $i->dontSeeInVehicleTypeTable([
            'name' => sqs('Forbidden Vehicle Type'),
        ]);
    }
}
