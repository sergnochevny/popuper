<?php

namespace Controller\Actions;

use Arr;
use Controller\EventsManagerInitTrait;
use LeadsSuspend;
use Model_LeadsSuspendReason;
use Popuper\Event\SuitabilityCalculation;
use Popuper\Model\EventType as ModelEventType;
use Popuper\Variables\Dynamic\LinksData;
use Suitability\Manager as SuitabilityManager;

/**
 * Class Kohana_Controller_Actions_Suitability
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 */
class Suitability extends ControllerAbstract {

    use EventsManagerInitTrait;

    /**
     * Function action_suitabilityNotice
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Kohana_Exception
     * @return void
     */
    public function action_suitabilityNotice()
    {
        $this->_suitabilityNotificationProcessing(
            Arr::get($_POST, 'value'),
            null,
            function () {
                return ['success' => false];
            }
        );
    }

    /**
     * Function action_suitabilityLevelAgreement
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Kohana_Exception
     * @return void
     */
    public function action_suitabilityLevelAgreement()
    {
        $this->_suitabilityNotificationProcessing(
            Arr::get($_POST, 'value'),
            function () {
                /** For agreement - Unsuspend this reason */
                (new LeadsSuspend($this->_leadId, false))
                    ->unsuspend(
                        [Model_LeadsSuspendReason::REASON_AUTO_SUSPENDED_UNTIL_THE_SUITABILITY_LEVEL_AGREEMENT,]
                    );

                return [];
            },
            function () {
                return [
                    'redirectTo' => (new LinksData($this->_language))
                        ->getValue(LinksData::ACCOUNT_VERIFICATION_PAGE),
                ];
            }
        );
    }

    /**
     * Function action_increaseLeverage
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Kohana_Exception
     */
    public function action_increaseLeverage()
    {
        $this->_suitabilityNotificationProcessing(
            Arr::get($_POST, 'value'),
            function () {
                SuitabilityManager::factory(
                    SuitabilityManager::LEAD_ACTIVITY_CALCULATION
                )
                    ->setMaxLeverageOfCurrentLevel($this->_leadId)
                    ->sendToPlatformDeferred();

                return [];
            },
            function () {
                return ['success' => false];
            }
        );
    }

    /**
     * Function _suitabilityNotificationProcessing
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     *
     * @param callable|null $approveCallback
     * @param callable|null $declineCallback
     * @param $value
     *
     * @return void
     */
    protected function _suitabilityNotificationProcessing(
        $value,
        callable $approveCallback = null,
        callable $declineCallback = null
    ) {
        $result = ['success' => false];

        /** Invalid request */
        if(
            !$this->_leadId
            || !in_array(
                $value,
                [
                    SuitabilityCalculation::INCREASE_APPROVED,
                    SuitabilityCalculation::INCREASE_NOT_APPROVED,
                ]
            )
        ){
            $result['error'] = 'Invalid request';
            $this->template->result = $result;

            return;
        }

        $result['success'] = true;

        if($value == SuitabilityCalculation::INCREASE_APPROVED && $approveCallback !== null){
            $result += call_user_func($approveCallback);
        } elseif($value == SuitabilityCalculation::INCREASE_NOT_APPROVED && $declineCallback !== null) {
            $result += call_user_func($declineCallback);
        }

        $this->_removeSuitabilityCalculationEvent();
        $this->template->result = $result;

        return;
    }

    /**
     * Function _removeSuitabilityCalculationEvent
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     * @return void
     */
    protected function _removeSuitabilityCalculationEvent()
    {
        $this->_eventsRepository
            ->setTypeId(ModelEventType::EVENT_SUITABILITY_CALCULATION)
            ->removeEvent(
                $this->_eventsManager->getEvent()
            );
    }

}