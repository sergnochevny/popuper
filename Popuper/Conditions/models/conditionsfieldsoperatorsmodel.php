<?php

namespace Popuper\Conditions\Models;

use ConditionsTree\Models\DBTableModel;

/**
 * Class ConditionsFieldsOperatorsModel
 * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
 */
class ConditionsFieldsOperatorsModel extends DBTableModel
{
    /** @var string  */
    const TABLE_NAME = 'popupsConfiguratorConditionsFieldsOperators';

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return string
     */
    public static function getComparisonOperatorIdFieldName(){
        return 'conditionOperatorId';
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return string
     */
    public static function getFieldIdFieldName(){
        return 'conditionFieldId';
    }

}