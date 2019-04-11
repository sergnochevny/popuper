<?php

namespace Popuper\Conditions\Models;

use ConditionsTree\Models\ElementsModel as BaseModel;
use DB;

/**
 * Class ElementsModel
 * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
 */
class ElementsModel extends BaseModel
{
    /** @var string */
    const TABLE_NAME = 'popupsConfiguratorElements';

    /** @var array */
    protected static $_depends;

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return string
     */
    public static function getMainIdFName()
    {
        return 'popupId';
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param $data
     *
     * @return \ConditionsTree\Models\ElementsLogModel
     */
    protected static function _getElementsLogModel($data)
    {
        return new ElementsLogModel($data);
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param array $queryConditions
     *
     * @throws \Database_Exception
     */
    protected static function _deleteDependsData(array $queryConditions = [])
    {
        $depends = static::_getDependsData($queryConditions);

        foreach ($depends as $item) {
            if ($item[static::getEntityTypeIdFName()] == EntityTypesModel::ENTITY_TYPE_GROUP) {
                GroupsModel::deleteAll([GroupsModel::getIdFieldName() => $item[static::getEntityIdFName()]]);
            } elseif ($item[static::getEntityTypeIdFName()] == EntityTypesModel::ENTITY_TYPE_CONDITION) {
                ConditionsModel::deleteAll([ConditionsModel::getIdFieldName() => $item[static::getEntityIdFName()]]);
            }
        }

        static::$_depends = null;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param array $queryConditions
     *
     * @return mixed
     */
    protected static function _combineLogData(array $queryConditions = [])
    {
        $logData = null;

        $depends = self::_getDependsData($queryConditions);
        foreach ($depends as $item) {
            if ($item[static::getEntityTypeIdFName()] == EntityTypesModel::ENTITY_TYPE_GROUP) {
                $logData[GroupsModel::getTableName()][] = GroupsModel::findAll(
                    [GroupsModel::getIdFieldName() => $item[static::getEntityIdFName()]]
                )->as_array();
            } elseif ($item[static::getEntityTypeIdFName()] == EntityTypesModel::ENTITY_TYPE_CONDITION) {
                $logData[GroupsModel::getTableName()][] = ConditionsModel::findAll(
                    [ConditionsModel::getIdFieldName() => $item[static::getEntityIdFName()]]
                )->as_array();
            }
        }
        $logData[static::getTableName()] = static::findAll($queryConditions)->as_array();

        return $logData;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param array $queryConditions
     *
     * @return array
     */
    protected static function _getDependsData(array $queryConditions = [])
    {
        if (static::$_depends === null) {
            $dependsQuery = DB::select()->from(static::getTableName());
            static::_appendEqualWhereByConditions($dependsQuery, $queryConditions);
            static::$_depends = $dependsQuery->execute()->as_array();
        }

        return static::$_depends;
    }

}