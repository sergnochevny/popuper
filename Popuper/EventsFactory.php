<?php

namespace Popuper;

use Exception;
use Popuper\Event\AbstractEvent;
use Popuper\Event\AutoVerificationComplete;
use Popuper\Event\DataPolicyConsent;
use Popuper\Event\DepositedNotVerified;
use Popuper\Event\EmailAddressVerification;
use Popuper\Event\HowToTrade;
use Popuper\Event\InvalidCountryDeposit;
use Popuper\Event\NotifyUnauthenticated;
use Popuper\Event\NotifyUnauthenticatedByIP;
use Popuper\Event\QuizResultsComplete;
use Popuper\Event\QuizResultsIncomplete;
use Popuper\Event\QuizResultsMaxAttempts;
use Popuper\Event\ReAcceptTerms;
use Popuper\Event\SiteSurfingNotAllowed;
use Popuper\Event\SuitabilityCalculation;
use Popuper\Event\SuitabilityValuesChanged;
use Popuper\Event\SuitabilityValuesIncrease;
use Popuper\Event\SuitabilityWarning;
use Popuper\Event\SurveyIncomplete;
use Popuper\Event\UnspecifiedOneTime;
use Popuper\Event\UnspecifiedPermanent;
use Popuper\Event\UnsuitableWarning;
use Popuper\Event\WrongRegion;
use Popuper\Event\AutoWithdrawal;
use Popuper\Model\EventType;

/**
 * Class EventsFactory
 * @author  Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
 * @package Popuper
 */
class EventsFactory
{
    /**
     * @throws \Kohana_Cache_Exception
     * @throws \Exception
     *
     * @param null $leadId
     * @param $typeId
     *
     * @return AbstractEvent
     */
    public static function getEvent($typeId, $leadId = null)
    {
        switch ($typeId) {
            case EventType::EVENT_SITE_SURFING_FORBID :
                $event = new SiteSurfingNotAllowed($leadId);
                break;
            case EventType::EVENT_NOTIFY_UNAUTHENTICATED :
                $event = new NotifyUnauthenticated($leadId);
                break;
            case EventType::EVENT_WRONG_REGION :
                $event = new WrongRegion($leadId);
                break;
            case EventType::EVENT_RE_ACCEPT_TERMS :
                $event = new ReAcceptTerms($leadId);
                break;
            case EventType::EVENT_SUITABILITY_VALUES_CHANGED :
                $event = new SuitabilityValuesChanged($leadId);
                break;
            case EventType::EVENT_SUITABILITY_VALUES_INCREASE :
                $event = new SuitabilityValuesIncrease($leadId);
                break;
            case EventType::EVENT_SUITABILITY_WARNING :
                $event = new SuitabilityWarning($leadId);
                break;
            case EventType::EVENT_SURVEY_INCOMPLETE :
                $event = new SurveyIncomplete($leadId);
                break;
            case EventType::EVENT_INVALID_COUNTRY_DEPOSIT :
                $event = new InvalidCountryDeposit($leadId);
                break;
            case EventType::EVENT_HOW_TO_TRADE :
                $event = new HowToTrade($leadId);
                break;
            case EventType::EVENT_AUTOVERIFICATION_COMPLETE :
                $event = new AutoVerificationComplete($leadId);
                break;
            case EventType::EVENT_QUIZ_RESULTS_INCOMPLETE:
                $event = new QuizResultsIncomplete($leadId);
                break;
            case EventType::EVENT_QUIZ_RESULTS_COMPLETE:
                $event = new QuizResultsComplete($leadId);
                break;
            case EventType::EVENT_QUIZ_RESULTS_MAX_ATTEMPTS:
                $event = new QuizResultsMaxAttempts($leadId);
                break;
            case EventType::EVENT_UNSUITABLE_WARNING :
                $event = new UnsuitableWarning($leadId);
                break;
            case EventType::EVENT_DEPOSITED_NOT_VERIFIED :
                $event = new DepositedNotVerified($leadId);
                break;
            case EventType::EVENT_UNSPECIFIED_PERMANENT :
                $event = new UnspecifiedPermanent($leadId);
                break;
            case EventType::EVENT_UNSPECIFIED_ONE_TIME :
                $event = new UnspecifiedOneTime($leadId);
                break;
            case EventType::EVENT_EMAIL_ADDRESS_VERIFICATION :
                $event = new EmailAddressVerification($leadId);
                break;
            case EventType::EVENT_DATA_POLICY_CONSENT :
                $event = new DataPolicyConsent($leadId);
                break;
            case EventType::EVENT_AUTO_WITHDRAWAL :
                $event = new AutoWithdrawal($leadId);
                break;
            case EventType::EVENT_NOTIFY_UNAUTHENTICATED_FROM_UNSUPPORTED_COUNTRIES_BY_IP :
                $event = new NotifyUnauthenticatedByIP($leadId);
                break;
            case EventType::EVENT_SUITABILITY_CALCULATION :
                $event = new SuitabilityCalculation($leadId);
                break;
            default :
                throw new Exception("Undefined popup event type(" . $typeId . ")");
        }

        return $event;
    }
}
