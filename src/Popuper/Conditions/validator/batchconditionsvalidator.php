<?php

namespace Popuper\Conditions\Validator;

use ConditionsTree\Validator\BatchConditionsValidator as BaseValidator;
use Popuper\Conditions\Maps\ConditionViewFieldsMap;

/**
 * Class BatchConditionsValidator
 * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
 */
class BatchConditionsValidator extends BaseValidator 
{
    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param $condition
     *
     * @return \ConditionsTree\Validator\ConditionValidator
     */
    protected function _getConditionsValidator($condition)
    {
        /** 
         * translate $eventTypeId to conditions' validation 
         */
        $condition[ConditionViewFieldsMap::VALIDATE_EVENT_TYPE_ID] = $this->_data[ConditionViewFieldsMap::VALIDATE_EVENT_TYPE_ID];

        return new ConditionValidator($condition);
    }

}