<?php

namespace Popuper\Variables\Dynamic;

use Arr;
use Leads;
use Leads\SpecificField\SubscribedNewsletterByEmail;
use Leads_Balance;
use Model\Leads\DataPolicy\Status as LeadPolicyStatus;
use Model\Leads\DataPolicy\Statuses as LeadPolicyStatuses;
use Model\Leads\LeadsSuspendStatuses;
use Model_Leads_Transactions;
use Model_LeadsTermsAcceptanceStatus;
use Model_LeadsTermsAcceptanceStatuses;
use Popuper\Variables\RequestData;

/**
 * Class LeadData
 * @author  Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
 * @package Popuper\Variables\Dynamic
 */
class LeadData extends AbstractVariables
{
    const COUNTRY_TYPE = 'COUNTRY_TYPE';
    const LEAD_EMAIL = 'LEAD_EMAIL';
    const LEAD_TC_STATUS_ID = 'LEAD_TC_STATUS_ID';
    const LEAD_BALANCE = 'LEAD_BALANCE';
    const LEAD_CAN_ACCEPT_TC = 'LEAD_CAN_ACCEPT_TC';
    const LEAD_COUNTRY = 'LEAD_COUNTRY';
    const LEAD_POLICY_CONSENT_STATUS = 'LEAD_POLICY_CONSENT_STATUS';
    const LEAD_NEWS_LETTER_AGREEMENT_STATUS = 'NEWS_LETTER_AGREEMENT_STATUS';
    const LEAD_DEPOSIT_AMOUNT = 'LEAD_DEPOSIT_AMOUNT';
    const LEAD_SUSPEND_REASONS = 'LEAD_SUSPEND_REASONS';

    /** @var array */
    protected $_leadInfo;

    /** @var RequestData */
    protected $_requestData;

    protected $_termsAcceptanceStatus;

    /**
     * LeadData constructor.
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @throws \Exception
     *
     * @param RequestData $requestData
     */
    public function __construct(RequestData $requestData)
    {
        $this->_requestData = $requestData;

        $this->_loadValues();
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @return array
     */
    public function getLeadInfo()
    {
        if(!$this->_leadInfo){
            $this->_leadInfo = Leads::get_lead_info($this->_requestData->leadId);
        }

        return $this->_leadInfo;
    }

    /**
     * Variables with descriptions.
     * Format:
     * [
     *     'VARIABLE_NAME' => 'Description of the variable',
     *     ...
     * ]
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @return array
     */
    public function getSystemVars()
    {
        return [
            self::LEAD_TC_STATUS_ID => 'Lead\'s Terms Acceptance Status',
            self::LEAD_CAN_ACCEPT_TC => 'Can accept T&C',
            self::LEAD_BALANCE => 'Lead\'s Balance',
            self::LEAD_COUNTRY => 'Lead\'s Country',
            self::LEAD_POLICY_CONSENT_STATUS => 'Lead\'s Policy Consent Status',
            self::LEAD_NEWS_LETTER_AGREEMENT_STATUS => 'Lead\'s News Letter Agreement Status',
            self::LEAD_DEPOSIT_AMOUNT => 'Lead\'s All Deposits Total Sum',
            self::LEAD_SUSPEND_REASONS => 'Lead\'s suspend reasons',
        ];
    }

    /**
     * Variables with descriptions.
     * Format:
     * [
     *     'VARIABLE_NAME' => 'Description of the variable',
     *     ...
     * ]
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @return array
     */
    public function getUsersVars()
    {
        return [
            self::COUNTRY_TYPE => 'Lead\'s Country type',
            self::LEAD_EMAIL => 'Lead\'s Email address',
        ];
    }

    /**
     * Method loads default values for variables
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @throws \Exception
     */
    protected function _loadValues()
    {
        $leadInfo = $this->getLeadInfo();
        if($leadInfo){
            $this->_variables[self::COUNTRY_TYPE] = $leadInfo['country_type'];
            $this->_variables[self::LEAD_EMAIL] = $leadInfo['email'];
            $this->_variables[self::LEAD_TC_STATUS_ID] = Arr::get(
                Model_LeadsTermsAcceptanceStatus::getByLead($leadInfo['id']),
                'statusId',
                Model_LeadsTermsAcceptanceStatuses::NOT_SEEN
            );
            $this->_variables[self::LEAD_CAN_ACCEPT_TC] = 1; // @deprecated
            $this->_variables[self::LEAD_BALANCE] = Leads_Balance::getFromExternalSystem(
                $leadInfo['id'],
                Leads_Balance::BALANCE_WITHDRAWAL
            );
            $this->_variables[self::LEAD_COUNTRY] = $leadInfo['country'];
            $this->_variables[self::LEAD_NEWS_LETTER_AGREEMENT_STATUS] =
                (new SubscribedNewsletterByEmail($leadInfo['id']))->get();
            $this->_variables[self::LEAD_POLICY_CONSENT_STATUS] = (int) Arr::get(
                LeadPolicyStatus::model()
                    ->getByLead($leadInfo['id']),
                'statusId',
                LeadPolicyStatuses::NOT_SEEN
            );
            $this->_variables[self::LEAD_DEPOSIT_AMOUNT] =
                Model_Leads_Transactions::getLeadDepositsSum($leadInfo['id']);
            $this->_variables[self::LEAD_SUSPEND_REASONS] = array_keys(
                (new LeadsSuspendStatuses($leadInfo['id']))
                    ->getStatuses()
            );
        } else {
            $this->_variables[self::COUNTRY_TYPE] = $this->_requestData->getCountryType();
        }
    }

}
