<?php

namespace Popuper\Conditions\Models;

use ConditionsTree\Models\GroupsModel as BaseModel;

/**
 * Class GroupsModel
 * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
 */
class GroupsModel extends BaseModel
{
    /** @var string  */
    const TABLE_NAME = 'popupsConfiguratorGroups';

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return string
     */
    public static function getLogicOperatorIdFieldName(){
        return 'groupOperatorId';
    }

}