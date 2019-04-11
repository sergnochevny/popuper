<?php

namespace Popuper\Conditions\Providers;

use Country;
use CountrySiteType;
use DB;
use Kohana;
use Kohana_Model_LeadsTermsAcceptanceStatuses;
use Popuper\Conditions\Maps\ConditionViewFieldsMap;
use Popuper\Conditions\Models\ConditionsFieldsEventTypesModel;
use Popuper\Conditions\Models\ConditionsFieldsModel;
use Popuper\Conditions\Models\ConditionsFieldsOperatorsModel;
use Popuper\Model\PopupsForEventTypes;

/**
 * Class PopupConditionsFields
 * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
 */
class PopupConditionsFields
{
    /** @var \Popuper\Conditions\Providers\PopupConditionsFields */
    protected static $_instance;

    /** @var array */
    private $_countries;

    /** @var mixed */
    private $_preparedData;

    /** @var mixed */
    private $_preparedAllData;

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return \Popuper\Conditions\Providers\PopupConditionsFields
     */
    public static function getInstance()
    {
        if (static::$_instance === null) {
            static::$_instance = new static();
        }

        return static::$_instance;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param $popupId
     *
     * @return array
     * @throws \Exception
     */
    public function getFieldsDataByPopup($popupId = null)
    {
        if ($this->_preparedData === null) {
            $this->_preparedData = $this->_prepareFieldsData(
                $this->_getDataFromDBByPopup($popupId)
            );
        }

        return $this->_preparedData;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return mixed
     * @throws \Exception
     */
    public function getAllFieldsData()
    {
        if ($this->_preparedAllData === null) {
            $this->_preparedAllData = $this->_prepareFieldsData(
                $this->_getAllDataFromDB()
            );
        }

        return $this->_preparedAllData;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param $eventTypeId
     *
     * @return array
     * @throws \Exception
     */
    public function getFieldsDataByEventType($eventTypeId = null)
    {
        if ($this->_preparedData === null) {
            $this->_preparedData = $this->_prepareFieldsData(
                $this->_getDataFromDBByEventType($eventTypeId)
            );
        }

        return $this->_preparedData;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param $fieldsData
     *
     * @return array
     * @throws \Exception
     */
    protected function _prepareFieldsData(array $fieldsData)
    {
        $result = [];
        foreach ($fieldsData as $fieldData) {
            $result[(int)$fieldData[ConditionViewFieldsMap::FIELD_ID]] = $this->_prepareFieldData($fieldData);
        }

        return $result;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param array $fieldData
     *
     * @return array
     * @throws \Exception
     */
    protected function _prepareFieldData(array $fieldData)
    {
        $fieldData[ConditionViewFieldsMap::FIELD_ID] = (int)$fieldData[ConditionViewFieldsMap::FIELD_ID];
        $fieldData[ConditionViewFieldsMap::CONDITION_VALUES] =
            $this->_prepareValuesByField((int)$fieldData[ConditionViewFieldsMap::FIELD_ID]);
        $fieldOperators = explode(',', $fieldData[ConditionViewFieldsMap::FIELD_OPERATORS]);
        $fieldData[ConditionViewFieldsMap::FIELD_OPERATORS] = array_map('intval', $fieldOperators);

        return $fieldData;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param $popupId
     *
     * @return mixed
     */
    protected function _getDataFromDBByPopup($popupId)
    {
        $select = $this->_prepareSelect()
            ->join([PopupsForEventTypes::TABLE_NAME, 'PET'])
            ->on(
                'PET.eventTypeId', '=',
                ConditionsFieldsEventTypesModel::TABLE_NAME . '.' . ConditionsFieldsEventTypesModel::getEventTypeIdFieldName()
            )
            ->where('PET.popupId', '=', $popupId)
            ->execute();

        return $select->as_array();
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return mixed
     */
    protected function _getAllDataFromDB()
    {
        $select = $this->_prepareSelect()->execute();

        return $select->as_array();
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param $eventTypeId
     *
     * @return mixed
     */
    protected function _getDataFromDBByEventType($eventTypeId)
    {
        $select = $this->_prepareSelect()
            ->where(
                ConditionsFieldsEventTypesModel::TABLE_NAME . '.' .
                ConditionsFieldsEventTypesModel::getEventTypeIdFieldName(),
                '=',
                $eventTypeId
            )
            ->execute();

        return $select->as_array();
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param $fieldId
     *
     * @return array
     * @throws \Exception
     */
    protected function _prepareValuesByField($fieldId)
    {
        if ($fieldId === ConditionsFieldsModel::FIELD_COUNTRY_BY_IP) {
            $fieldValues = $this->_getAllCountries();
        } elseif ($fieldId === ConditionsFieldsModel::FIELD_COUNTRY_TYPE) {
            $fieldValues = CountrySiteType::getCountryTypes();
        } elseif ($fieldId === ConditionsFieldsModel::FIELD_TERMS_ACCEPTANCE_STATUS) {
            $fieldValues = Kohana_Model_LeadsTermsAcceptanceStatuses::getAllStatuses();
        } elseif ($fieldId === ConditionsFieldsModel::FIELD_NEWSLETTER_AGREEMENT) {
            $fieldValues = Kohana::config('global/filters_values')->agreed_notAgreed;
        } elseif ($fieldId === ConditionsFieldsModel::FIELD_IS_SUITABILITY_LEVEL_FIRST_CALCULATION) {
            $fieldValues = Kohana::config('global/form')->yesNo;
        } elseif ($fieldId === ConditionsFieldsModel::FIELD_SUITABILITY_LEVEL) {
            $fieldValues = \Suitability\Model\Level::basicQuery()->execute()->as_array('id', 'title');
        } elseif ($fieldId === ConditionsFieldsModel::FIELD_EMAIL_VERIFICATION_STATUS) {
            $fieldValues = \Leads\Email\Verification\Statuses::getAll();
        } elseif ($fieldId === ConditionsFieldsModel::FIELD_NEW_LEVERAGE_DECLINED) {
            $fieldValues = Kohana::config('global/form')->yesNo;
        } elseif ($fieldId === ConditionsFieldsModel::FIELD_DATA_POLICY_CONSENT_STATUS) {
            $fieldValues = \Model\Leads\DataPolicy\Statuses::getAllStatuses();
        } elseif ($fieldId === ConditionsFieldsModel::FIELD_LEAD_COUNTRY) {
            $fieldValues = $this->_getAllCountries();
        } elseif ($fieldId === ConditionsFieldsModel::FIELD_CALLER_PAGE_ID) {
            $fieldValues = Kohana::config('global/gdpr_enabled_pages')->pages;
        } elseif ($fieldId === ConditionsFieldsModel::FIELD_SUSPEND_REASON_ID) {
           $fieldValues = Kohana::config('global/suspendreasons')->reasons;
        } elseif ($fieldId === ConditionsFieldsModel::FIELD_NEW_LEVERAGE_APPROVED) {
            $fieldValues = Kohana::config('global/form')->yesNo;
        } elseif ($fieldId === ConditionsFieldsModel::FIELD_SUITABILITY_VALUES_CHANGED) {
            $fieldValues = Kohana::config('global/form')->yesNo;
        } elseif ($fieldId === ConditionsFieldsModel::FIELD_LEVERAGE_CAN_INCREASE) {
            $fieldValues = Kohana::config('global/form')->yesNo;
        } else {
            $fieldValues = [];
        }

        return $fieldValues;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return array
     */
    protected function _getAllCountries()
    {
        if ($this->_countries === null) {
            $this->_countries = Country::getCountries('iso', 'printable_name');
        }

        return $this->_countries;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return \Database_Query_Builder_Select
     */
    protected function _prepareSelect()
    {
        return DB::select(
            [
                ConditionsFieldsModel::TABLE_NAME . '.' . ConditionsFieldsModel::getIdFieldName(),
                ConditionViewFieldsMap::FIELD_ID
            ],
            [
                ConditionsFieldsModel::TABLE_NAME . '.' . ConditionsFieldsModel::getTitleFieldName(),
                ConditionViewFieldsMap::FIELD_TITLE
            ],
            [
                DB::expr('GROUP_CONCAT( DISTINCT ' .
                    ConditionsFieldsOperatorsModel::TABLE_NAME . '.' .
                    ConditionsFieldsOperatorsModel::getComparisonOperatorIdFieldName() .
                    ' ORDER BY ' .
                    ConditionsFieldsOperatorsModel::TABLE_NAME . '.' .
                    ConditionsFieldsOperatorsModel::getComparisonOperatorIdFieldName() .
                    ' SEPARATOR ",")'
                ),
                ConditionViewFieldsMap::FIELD_OPERATORS
            ]
        )
            ->from(ConditionsFieldsModel::TABLE_NAME)
            ->join(ConditionsFieldsEventTypesModel::TABLE_NAME)
            ->on(
                ConditionsFieldsEventTypesModel::TABLE_NAME . '.' . ConditionsFieldsEventTypesModel::getFieldIdFieldName(),
                '=',
                ConditionsFieldsModel::TABLE_NAME . '.' . ConditionsFieldsModel::getIdFieldName()
            )
            ->join(ConditionsFieldsOperatorsModel::TABLE_NAME)
            ->on(
                ConditionsFieldsModel::TABLE_NAME . '.' . ConditionsFieldsModel::getIdFieldName(),
                '=',
                ConditionsFieldsOperatorsModel::TABLE_NAME . '.' . ConditionsFieldsOperatorsModel::getFieldIdFieldName()
            )
            ->group_by(ConditionsFieldsModel::TABLE_NAME . '.' . ConditionsFieldsModel::getIdFieldName());
    }

}
