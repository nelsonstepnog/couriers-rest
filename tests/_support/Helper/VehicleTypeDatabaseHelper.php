<?php

declare( strict_types = 1 );

namespace Delivery\CouriersRest\tests\Helper;

use Codeception\Exception\ModuleException;
use Codeception\Module;
use Codeception\Module\Db;
use Delivery\ComponentHelpers\helpers\ArrayHelper;
use Delivery\Couriers\entities\VehicleTypeActiveRecord;
use yii;

/**
 * Class VehicleTypeDatabaseHelper
 * Класс для работы с предусловиями по типам транспортных средств.
 *
 * @package Delivery\CouriersRest\tests\Helper
 */
class VehicleTypeDatabaseHelper extends Module
{
    /**
     * Возвращает модуль кодесепшена ДБ.
     *
     * @throws ModuleException
     *
     * @return Module|Db
     */
    protected function getModuleDb(): Db
    {
        return $this->getModule('Db');
    }

    /**
     * Возвращает название таблицы типов транспортных средств.
     *
     * @return string
     */
    protected function getVehicleTypeTableName(): string
    {
        return Yii::$app->db->schema->getRawTableName(VehicleTypeActiveRecord::tableName());
    }

    /**
     * Добавляет запись типов транспортных средств в базу данных.
     *
     * @param array $params
     *
     * @throws ModuleException
     *
     * @return int
     */
    public function addVehicleTypeEntity(array $params = []): int
    {
        return $this->getModuleDb()->haveInDatabase($this->getVehicleTypeTableName(), [
            'name'            => ArrayHelper::getValue($params, 'name', sqs('DefaultVehicleTypeName')),
            'load'            => ArrayHelper::getValue($params, 'load'),
            'fuelConsumption' => ArrayHelper::getValue($params, 'fuelConsumption'),
            'fuelId'          => $params['fuelId'],
            'isDeleted'       => ArrayHelper::getValue($params, 'isDeleted', false),
        ]);
    }

    /**
     * Обновляет запись типов транспортных средств в базе данных.
     *
     * @param int   $vehicleTypeId
     * @param array $params
     *
     * @return void
     *
     * @throws ModuleException
     */
    public function updateVehicleTypeEntityById(int $vehicleTypeId, array $params = []): void
    {
        $this->getModuleDb()->updateInDatabase($this->getVehicleTypeTableName(), [
            'name'            => ArrayHelper::getValue($params, 'name', sqs('DefaultVehicleTypeName')),
            'load'            => ArrayHelper::getValue($params, 'load'),
            'fuelConsumption' => ArrayHelper::getValue($params, 'fuelConsumption'),
            'fuelId'          => $params['fuelId'],
            'isDeleted'       => ArrayHelper::getValue($params, 'isDeleted', false),
        ], ['id' => $vehicleTypeId]);
    }

    /**
     * Возвращает значение поля из таблицы типов транспортных средств.
     *
     * @param string $column
     * @param array  $criteria
     *
     * @throws ModuleException
     *
     * @return mixed
     */
    public function grabFromVehicleTypeTable(string $column, array $criteria = [])
    {
        return $this->getModuleDb()->grabFromDatabase($this->getVehicleTypeTableName(), $column, $criteria);
    }

    /**
     * Проверяет наличие записи в таблице типов транспортных средств.
     *
     * @param array $criteria
     *
     * @throws ModuleException
     *
     * @return void
     */
    public function seeInVehicleTypeTable(array $criteria): void
    {
        $this->getModuleDb()->seeInDatabase($this->getVehicleTypeTableName(), $criteria);
    }

    /**
     * Проверяет отсутствие записи в таблице типов транспортных средств.
     *
     * @param array $criteria
     *
     * @throws ModuleException
     *
     * @return void
     */
    public function dontSeeInVehicleTypeTable(array $criteria): void
    {
        $this->getModuleDb()->dontSeeInDatabase($this->getVehicleTypeTableName(), $criteria);
    }
}
