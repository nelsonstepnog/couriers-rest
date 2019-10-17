<?php

declare( strict_types = 1 );

namespace Delivery\CouriersRest\tests;

use Codeception\Actor;

/**
 * Класс текущего актера-тестера.
 *
 * @method void wantToTest( $text )
 * @method void wantTo( $text )
 * @method void execute( $callable )
 * @method void expectTo( $prediction )
 * @method void expect( $prediction )
 * @method void amGoingTo( $argumentation )
 * @method void am( $role )
 * @method void lookForwardTo( $achieveValue )
 * @method void comment( $description )
 */
class ApiTester extends Actor
{
    use _generated\ApiTesterActions;

    /**
     * Метод логинит пользователя.
     *
     * @param string $username Имя пользователя.
     * @param string $password Пароль пользователя.
     *
     * @return void
     */
    public function login(string $username, string $password): void
    {
        $this->sendPOST('/api/v1/auth', [
            'login'    => $username,
            'password' => $password,
        ]);
        $this->seeResponseCodeIs(200);
    }

    /**
     * Метод на время теста создаёт в базе данных виды топлива согласно переданным параметрам.
     *
     * @param array $fuels Массив с массивами параметров видов топлива.
     *
     * @return array
     * @throws _generated\ModuleException
     */
    public function createManyFuel(array $fuels): array
    {
        $fuelIds = [];
        foreach ($fuels as $fuel) {
            $fuelId                 = $this->addFuelEntity($fuel);
            $fuelIds[$fuel['name']] = $fuelId;
        }
        return $fuelIds;
    }

    /**
     * Метод на время теста создаёт в базе данных типы транспорта согласно переданным параметрам.
     *
     * @param array $vehicleTypes
     *
     * @return array
     * @throws _generated\ModuleException
     */
    public function createManyVehicleType(array $vehicleTypes): array
    {
        $vehicleTypeIds = [];
        foreach ($vehicleTypes as $vehicleType) {
            $vehicleTypeId                        = $this->addVehicleTypeEntity($vehicleType);
            $vehicleTypeIds[$vehicleType['name']] = $vehicleTypeId;
        }
        return $vehicleTypeIds;
    }
}
