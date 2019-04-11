<?php

namespace Controller\Actions;

use Arr;
use Assessment\Status\Lead;
use Assessment\Status\Model\Statuses;
use Kohana;
use Leads;
use leads\SpecificFields;
use LeadsSuspend;
use Model_LeadsSuspendReason;
use Operations;
use OperationsTypes;
use Popuper\EventsManager;
use Popuper\EventsRepository;
use Popuper\Model\EventType as ModelEventType;
use Popuper\Model\EventType;
use Popuper\Model\EventVariable;
use Popuper\Variables\Repository as EventVariablesRepository;
use Regulation;
use SystemOptions;

/**
 * Class Controller_Actions_Mixed
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 */
class Mixed extends ControllerAbstract
{
    /**
     * Function action_howToTrade
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     * @return void
     */
    public function action_howToTrade()
    {
        $answer = Arr::get($_POST, 'answer');
        if(null == $answer){
            return;
        }
        if(!$this->_leadId){
            $this->template->result['success'] = false;
            $this->template->result['url'] = '/';

            return;
        }
        $this->template->result['success'] = true;
        $leadSpecificFields = new SpecificFields($this->_leadId);
        $leadSpecificFields->knowHowToTrade = $answer;
        $leadSpecificFields->save();
        if($leadSpecificFields->knowHowToTrade){
            (new LeadsSuspend($this->_leadId, false))->unsuspend(
                [
                    Model_LeadsSuspendReason::REASON_QUIZ_IS_NOT_COMPLETE,
                ]
            );
            (new Lead($this->_leadId))->setStatusId(
                Statuses::ID_NOT_APPLICABLE
            )
                ->save();
            $platformUrl = '//' . ltrim(
                    SystemOptions::get('System/Outer Services/Platform Domain'),
                    ' /'
                );
            $this->template->result['url'] = Regulation::instance()
                ->parseUrlBasedOnCurrentDomain($platformUrl);
            $comment = 'Defined as experienced trader';
        } else {
            $leadInfo = Leads::get_lead_info($this->_leadId, ['lang', 'country_type']);
            $sitesTypes = Kohana::config('global/sites-types')->sites;
            $domain = Regulation::instance()
                ->parseTextForCountryType(
                    $leadInfo['country_type'],
                    $sitesTypes[$this->_language]
                );
            $this->template->result['url'] = '//' . trim($domain, '/') . '/' . ltrim(
                    SystemOptions::get('System/Website/Quiz page URI'),
                    '/'
                );
            $comment = 'Defined as not experienced trader';
        }

        Operations::save(
            OperationsTypes::OTHER,
            'lead',
            $this->_leadId,
            '',
            $comment
        );

        (new EventsManager(
            new EventsRepository($this->_leadId),
            new EventVariablesRepository()
        ))
            ->setEventTypeId(ModelEventType::EVENT_HOW_TO_TRADE)
            ->removeCurrentEvent();
    }
}