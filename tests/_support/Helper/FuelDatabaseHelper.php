<?php

declare(strict_types = 1);

namespace Delivery\CouriersRest\tests\Helper;

use Codeception\Exception\ModuleException;
use Codeception\Module;
use Codeception\Module\Db;
use Delivery\ComponentHelpers\helpers\ArrayHelper;
use Delivery\Couriers\entities\FuelActiveRecord;
use yii;

/**
 * Class FuelDatabaseHelper
 * Класс для работы с предусловиями по видам топлива.
 *
 * @package Delivery\CouriersRest\tests\Helper
 */
class FuelDatabaseHelper extends Module
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
     * Возвращает название таблицы видов топлива.
     *
     * @return string
     */
    protected function getFuelTableName(): string
    {
        return Yii::$app->db->schema->getRawTableName(FuelActiveRecord::tableName());
    }

    /**
     * Добавляет запись вида топлива в базу данных.
     *
     * @param array $params
     *
     * @throws ModuleException
     *
     * @return int
     */
    public function addFuelEntity(array $params = [])
    {
        return $this->getModuleDb()->haveInDatabase($this->getFuelTableName(), [
            'name'      => ArrayHelper::getValue($params, 'name', sqs('DefaultFuelName')),
            'isDeleted' => ArrayHelper::getValue($params, 'isDeleted', false),
        ]);
    }

    /**
     * Обновляет запись вида топлива в базе данных.
     *
     * @param int   $fuelId
     * @param array $params
     *
     * @throws ModuleException
     *
     * @return void
     */
    public function updateFuelEntityById(int $fuelId, array $params = []): void
    {
        $this->getModuleDb()->updateInDatabase($this->getFuelTableName(), [
            'name'      => ArrayHelper::getValue($params, 'name', sqs('DefaultFuelName')),
            'isDeleted' => ArrayHelper::getValue($params, 'isDeleted', false),
        ], ['id' => $fuelId]);
    }

    /**
     * Возвращает значение поля из таблицы видов топлива.
     *
     * @param string $column
     * @param array  $criteria
     *
     * @throws ModuleException
     *
     * @return mixed
     */
    public function grabFromFuelTable(string $column, array $criteria = [])
    {
        return $this->getModuleDb()->grabFromDatabase($this->getFuelTableName(), $column, $criteria);
    }

    /**
     * Проверяет наличие записи в таблице видов топлива.
     *
     * @param array $criteria
     *
     * @throws ModuleException
     *
     * @return void
     */
    public function seeInFuelTable(array $criteria): void
    {
        $this->getModuleDb()->seeInDatabase($this->getFuelTableName(), $criteria);
    }

    /**
     * Проверяет отсутствие записи в таблице видов топлива.
     *
     * @param array $criteria
     *
     * @throws ModuleException
     *
     * @return void
     */
    public function dontSeeInFuelTable(array $criteria): void
    {
        $this->getModuleDb()->dontSeeInDatabase($this->getFuelTableName(), $criteria);
    }
}
