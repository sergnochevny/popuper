<?php

namespace Controller\Actions;

use Arr;
use Controller\EventsManagerInitTrait;
use Kohana;
use Leads;
use Leads\SpecificField\SubscribedNewsletterByEmail;
use leads\SpecificFields;
use Model\Leads\DataPolicy\PrivacyPolicy;
use Model\Leads\DataPolicy\Statuses;
use Model_LeadsTermsAcceptanceStatus;
use Model_LeadsTermsAcceptanceStatuses;
use Popuper\Model\EventType as ModelEventType;
use Popuper\Model\EventVariable;
use Request;
use SimpleCaptcha;
use SystemOptions;

/**
 * Class Kohana_Controller_Actions
 * Controller with actions which called from pop-ups(accept or decline T&C, check captcha, etc)
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 */
class Terms extends ControllerAbstract {

    use EventsManagerInitTrait;

    /**
     * Function action_termsAcceptance
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     */
    public function action_termsAcceptance()
    {
        $this->template->result['success'] = false;
        $this->template->result['messages'] = [];
        if(!$this->_leadId){
            return;
        }
        /** @var bool $newStatus */
        $newStatus = (Arr::get($_POST, 'acceptedConditions'))
            ? Model_LeadsTermsAcceptanceStatuses::AGREED
            : Model_LeadsTermsAcceptanceStatuses::NOT_AGREED;
        if(
            ($enableUsFatcaOptions = SystemOptions::get(
                'Leads/Registration/Enable US FATCA checkbox agreement'
            ))
            && Arr::get(
                $enableUsFatcaOptions, Arr::get(Leads::get_lead_info($this->_leadId, ['country_type']), 'country_type'),
                Arr::get($enableUsFatcaOptions, 'default')
            )
        ){
            if($notUSReportablePerson = Arr::get($_POST, 'notUSReportablePerson')){
                $leadSpecificFields = new SpecificFields($this->_leadId);
                $leadSpecificFields->notUSReportablePerson = $notUSReportablePerson;
                $leadSpecificFields->save();
            } else {
                $this->template->result['messages'] = [Kohana::message('popups', 'notValid.USReportablePerson')];
                $this->template->result['success'] = false;

                return;
            }
        }
        $this->template->result['messages'] = $this->_termsAcceptance($newStatus);
        $this->template->result['success'] = !$this->template->result['messages'];
    }

    /**
     * Function action_termsCaptcha
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     */
    public function action_termsCaptcha()
    {
        $this->template->result['success'] = false;
        $this->template->result['messages'] = [];
        if(!$this->_leadId){
            return;
        }

        /**
         * check captcha
         */
        if(SimpleCaptcha::valid(Arr::get($_POST, 'captchaResponse'))){
            /** @var bool $newStatus */
            $newStatus = Arr::get($_POST, 'acceptedConditions')
                ? Model_LeadsTermsAcceptanceStatuses::AGREED
                : Model_LeadsTermsAcceptanceStatuses::NOT_AGREED;

            $this->template->result['messages'] = $this->_termsAcceptance($newStatus);
        } else {
            $this->template->result['messages']['captchaResponse'] = Kohana::message(
                'popups', 'notValid.captcha'
            );
        }
        $this->template->result['success'] = !$this->template->result['messages'];
    }

    /**
     * Function action_termsAmended
     * Receive AJAX request after "T&C have been amended" popup was clicked
     * and update current event for the lead
     * @author Alexander Shpak <alexander.shpak@tstechpro.com>
     * @throws \Exception
     * @return bool
     */
    public function action_termsAmended()
    {
        $popupId = Arr::get($_POST, 'popupId');

        if(!$this->_leadId || !$popupId){
            return true;
        }

        return $this->_eventsManager
            ->setEventTypeId(ModelEventType::EVENT_UNSPECIFIED_PERMANENT)
            ->saveCurrentEvent(
                $this->_buildEventData(
                    [EventVariable::CUSTOM_POPUP_IDS_LIST => [$popupId]]
                )
            );
    }

    /**
     * Function action_subscribedNewsletter
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     * @return void
     */
    public function action_subscribedNewsletter()
    {
        $data = Arr::get($_POST, 'data', []);
        $popupId = Arr::get($data, 'popupId');

        if(!$this->_leadId || !$data || !$popupId){
            return;
        }

        if($this->_setSubscribedNewsletterStatus($data)){
            $this->_eventsManager
                ->setEventTypeId(ModelEventType::EVENT_UNSPECIFIED_PERMANENT)
                ->saveCurrentEvent(
                    $this->_buildEventData(
                        [EventVariable::CUSTOM_POPUP_IDS_LIST => [$popupId]]
                    )
                );
        }
    }

    /**
     * Function action_gdprRegulation
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     */
    public function action_gdprRegulation()
    {
        $data = json_decode(Arr::get($_POST, 'data'), true);

        if(!$this->_leadId || !$data){
            return;
        }

        if($this->_setSubscribedNewsletterStatus($data)){
            if(Arr::get($data, 'privacyPolicy')){
                (new PrivacyPolicy($this->_leadId))->change(Statuses::AGREED);
            }
            if(Arr::get($data, 'termsConditions')){
                Model_LeadsTermsAcceptanceStatus::setStatusByLead(
                    $this->_leadId,
                    Model_LeadsTermsAcceptanceStatuses::AGREED,
                    $this->ip,
                    Request::$user_agent
                );
            }
        }
    }

    /**
     * Function _termsAcceptance
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     *
     * @param $newStatus
     *
     * @return array
     */
    protected function _termsAcceptance($newStatus)
    {
        $errors = [];
        /** @var array $currentStatus */
        $currentStatus = Arr::get(
            Model_LeadsTermsAcceptanceStatus::getByLead($this->_leadId),
            'statusId',
            Model_LeadsTermsAcceptanceStatuses::NOT_SEEN
        );

        if($newStatus != Model_LeadsTermsAcceptanceStatuses::AGREED){
            $errors[] = Kohana::message('popups', 'notValid.acceptedConditions');
        }

        if(!$errors){
            if($currentStatus != $newStatus){
                /** @var bool $result */
                $result = Model_LeadsTermsAcceptanceStatus::setStatusByLead(
                    $this->_leadId, $newStatus, $this->ip, Request::$user_agent
                );

                if(!$result){
                    $errors[] = Kohana::message('popups', 'undefinedError');
                }
            } else {
                $this->_eventsManager
                    ->setEventTypeId(ModelEventType::EVENT_RE_ACCEPT_TERMS)
                    ->saveCurrentEvent(
                        $this->_buildEventData(
                            [EventVariable::LEAD_TC_STATUS_ID => $newStatus]
                        )
                    );
            }
        }

        return $errors;
    }

    /**
     * Function _setSubscribedNewsletterStatus
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     *
     * @param string $key
     * @param $data
     *
     * @return bool
     */
    protected function _setSubscribedNewsletterStatus($data, $key = 'marketingMaterial')
    {
        if(!array_key_exists($key, $data)){
            return false;
        }

        return (new SubscribedNewsletterByEmail($this->_leadId))
            ->change((bool) $data[$key]);
    }
}