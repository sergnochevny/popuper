<?php

namespace Popuper\Conditions\Maps;

use ConditionsTree\Maps\ConditionFieldsMap as BaseMap;
use Popuper\Conditions\Models\ConditionsFieldsModel;
use Popuper\Model\EventVariable;
use Popuper\Variables\Dynamic\DynamicData;
use Popuper\Variables\Dynamic\LeadData;

/**
 * Class ConditionFieldsMap
 * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
 */
class ConditionFieldsMap extends BaseMap
{
    /**
     * @var array
     */
    protected static $_availableFields = [
        ConditionsFieldsModel::FIELD_COUNTRY_BY_IP => DynamicData::COUNTRY_BY_IP,
        ConditionsFieldsModel::FIELD_COUNTRY_TYPE => EventVariable::COUNTRY_TYPE,
        ConditionsFieldsModel::FIELD_TERMS_ACCEPTANCE_STATUS => EventVariable::LEAD_TC_STATUS_ID,
        ConditionsFieldsModel::FIELD_NEWSLETTER_AGREEMENT => LeadData::LEAD_NEWS_LETTER_AGREEMENT_STATUS,
        ConditionsFieldsModel::FIELD_IS_SUITABILITY_LEVEL_FIRST_CALCULATION => EventVariable::IS_FIRST_SUITABILITY_CALCULATION,
        ConditionsFieldsModel::FIELD_DEPOSIT_AMOUNT => LeadData::LEAD_DEPOSIT_AMOUNT,
        ConditionsFieldsModel::FIELD_SUITABILITY_LEVEL => EventVariable::LEAD_SUITABILITY_LVL_ID,
        ConditionsFieldsModel::FIELD_LEAD_BALANCE => EventVariable::LEAD_BALANCE,
        ConditionsFieldsModel::FIELD_EMAIL_VERIFICATION_STATUS => EventVariable::EMAIL_VERIFICATION_STATUS,
        ConditionsFieldsModel::FIELD_NEW_LEVERAGE_DECLINED => EventVariable::PURPOSED_LEVERAGE_WAS_DECLINED,
        ConditionsFieldsModel::FIELD_DATA_POLICY_CONSENT_STATUS => LeadData::LEAD_POLICY_CONSENT_STATUS,
        ConditionsFieldsModel::FIELD_LEAD_COUNTRY => LeadData::LEAD_COUNTRY,
        ConditionsFieldsModel::FIELD_CALLER_PAGE_ID => DynamicData::CALLER_PAGE_ID,
        ConditionsFieldsModel::FIELD_SUSPEND_REASON_ID => LeadData::LEAD_SUSPEND_REASONS,
        ConditionsFieldsModel::FIELD_NEW_LEVERAGE_APPROVED => EventVariable::LEVERAGE_INCREASING_APPROVED,
        ConditionsFieldsModel::FIELD_SUITABILITY_VALUES_CHANGED => EventVariable::SUITABILITY_VALUES_CHANGED,
        ConditionsFieldsModel::FIELD_LEVERAGE_CAN_INCREASE => EventVariable::LEVERAGE_CAN_INCREASE,

    ];

}