<?php

namespace Popuper\Conditions\Validator;

use Arr;
use ConditionsTree\Validator\ConditionValidator as BaseValidator;
use Popuper\Conditions\Maps\ConditionViewFieldsMap;
use Popuper\Conditions\Providers\PopupConditionsFields;

/**
 * Class ConditionValidator
 * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
 */
class ConditionValidator extends BaseValidator
{
    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param array $rules
     *
     * @return array
     * @throws \Exception
     * 
     * extend rules by checking of fields/operators/values available for eventType
     * in the _data we have an array (incoming data after normalization like below) 
     *
     * [
     *  'eventTypeId' => 1
     *  'fieldId' => 1,
     *  'comparisonOperatorId' => 1,
     *  'logicalOperatorId' => 1,
     *  'value' => ['AL']
     * ]
     *
     */
    protected function _extendValidationRules(array $rules)
    {
        $fieldId = $this->_getFieldIdFromValidationData();
        $eventTypeId = $this->getEventIdFromValidationData();
        
        /** getting all available fields by eventType */
        $restrictions = PopupConditionsFields::getInstance()->getFieldsDataByEventType($eventTypeId);

        /** check incoming fields by available fields for eventType */
        $rules[] = [
            ConditionViewFieldsMap::CONDITION_FIELD_ID,
            'in_array',
            [array_keys($restrictions)]
        ];

        if (!empty($restrictions[$fieldId])) {

            /** check incoming operator by available operators for fields of eventType */
            $rules[] = [
                ConditionViewFieldsMap::CONDITION_COMPARISON_OPERATOR_ID,
                'in_array',
                [$restrictions[$fieldId][ConditionViewFieldsMap::FIELD_OPERATORS]]
            ];

            /** check incoming value of field by available values for fields of eventType */
            if (!empty($restrictions[$fieldId][ConditionViewFieldsMap::CONDITION_VALUES])) {
                $rules[] = [
                    ConditionViewFieldsMap::CONDITION_VALUE,
                    function ($value) use ($restrictions, $fieldId) {
                        $availableValues = Arr::getColumnRecursive(
                            $restrictions[$fieldId][ConditionViewFieldsMap::CONDITION_VALUES],
                            'id'
                        );
                        if (!$availableValues) {
                            $availableValues = array_keys($restrictions[$fieldId][ConditionViewFieldsMap::CONDITION_VALUES]);
                        }

                        if (is_array($value)) {
                            $result = (count(array_intersect($value, $availableValues)) === count($value));
                        } else {
                            $result = (
                                in_array($value, $availableValues) ||
                                in_array($value, $restrictions[$fieldId][ConditionViewFieldsMap::CONDITION_VALUES])
                            );
                        }

                        if (!$result) {
                            $this->setValidateError(ConditionViewFieldsMap::CONDITION_VALUE, 'in_array');

                        }

                        return $result;
                    }
                ];
            }
        }

        return $rules;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return mixed
     */
    protected function _getFieldIdFromValidationData()
    {
        return $this->_data[ConditionViewFieldsMap::CONDITION_FIELD_ID];
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return mixed
     */
    protected function getEventIdFromValidationData()
    {
        return  $this->_data[ConditionViewFieldsMap::VALIDATE_EVENT_TYPE_ID];
    }

}