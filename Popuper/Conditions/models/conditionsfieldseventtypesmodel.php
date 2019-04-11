<?php

namespace Popuper\Conditions\Models;

use ConditionsTree\Models\DBTableModel;

/**
 * Class ConditionsFieldsEventTypesModel
 * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
 */
class ConditionsFieldsEventTypesModel extends DBTableModel
{
    /** @var string  */
    const TABLE_NAME = 'popupsConfiguratorConditionsFieldsEventTypes';

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
    public static function getEventTypeIdFieldName(){
        return 'eventTypeId';
    }
}