<?php

namespace Popuper\Conditions\Models;

use ConditionsTree\Models\ConditionsModel as BaseModel;

/**
 * Class ConditionsModel
 * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
 */
class ConditionsModel extends BaseModel
{
    /** @var string  */
    const TABLE_NAME = 'popupsConfiguratorConditions';

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return string
     */
    public static function getFieldIdFieldName(){
        return 'conditionFieldId';
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return string
     */
    public static function getComparisonOperatorIdFieldName(){
        return 'conditionOperatorId';
    }

}