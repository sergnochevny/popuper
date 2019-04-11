<?php

namespace Popuper\Conditions\Models;

use ConditionsTree\Models\DBTableModel;

/**
 * Class ConditionsFieldsModel
 * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
 */
class ConditionsFieldsModel extends DBTableModel
{
    /** @var string */
    const TABLE_NAME = 'popupsConfiguratorConditionsFields';
    
    const FIELD_COUNTRY_BY_IP = 1;
    const FIELD_COUNTRY_TYPE = 2;
    const FIELD_TERMS_ACCEPTANCE_STATUS = 3;
    const FIELD_NEWSLETTER_AGREEMENT = 4;
    const FIELD_IS_SUITABILITY_LEVEL_FIRST_CALCULATION = 5;
    const FIELD_DEPOSIT_AMOUNT = 6;
    const FIELD_SUITABILITY_LEVEL = 7;
    const FIELD_LEAD_BALANCE = 8;
    const FIELD_EMAIL_VERIFICATION_STATUS = 9;
    const FIELD_NEW_LEVERAGE_DECLINED = 10;
    const FIELD_DATA_POLICY_CONSENT_STATUS = 11;
    const FIELD_LEAD_COUNTRY = 12;
    const FIELD_CALLER_PAGE_ID = 13;
    const FIELD_SUSPEND_REASON_ID = 14;
    const FIELD_NEW_LEVERAGE_APPROVED = 15;
    const FIELD_SUITABILITY_VALUES_CHANGED = 16;
    const FIELD_LEVERAGE_CAN_INCREASE = 17;

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return string
     */
    public static function getTitleFieldName()
    {
        return 'name';
    }

}