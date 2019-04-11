<?php defined('SYSPATH') or die('No direct script access.');

use Popuper\Conditions\Identifier;
use Popuper\Conditions\Providers\PopupConditionsFields;
use Popuper\Conditions\Repositories\Repository as ConditionsRepository;
use Popuper\Editor\Single as PopupEditor;
use Popuper\EventsManager;
use Popuper\EventsRepository;
use Popuper\Model\EventGroups;
use Popuper\Model\EventType;
use Popuper\Model\Popups;
use Popuper\Model\Templates;
use Popuper\Validators\EditPopupValidator;
use Popuper\Validators\TestLeadValidator;
use Popuper\Variables\Repository as EventVariablesRepository;
use Popuper\Variables\RequestData;
use Popuper\Model\EventVariable;

/**
 * Class Controller_Popup_Manager
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 */
class Controller_Popup_Manager extends Controller_Template
{
    /**
     * @var string
     */
    public $template = "layout";

    /** @var array */
    protected $_sortableFields = [
        'id',
        'isActive',
        'name',
        'templateName',
    ];

    /** @var array */
    protected $_sortableDirections = [
        'ASC',
        'DESC',
    ];

    /**
     * Function before
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws Kohana_Exception403
     * @return void
     */
    function before()
    {
        if(!Access::granted('Administer everything')){
            throw new Kohana_Exception403();
        }

        if(F::IsAjaxMode()){
            $this->template = "ajax_layout";
        }

        parent::before();

        if(!F::IsAjaxMode()){
            Model::factory('Template_office')
                ->returnTemp($this->template);
            $this->template->header = View::factory('common/header_Universal')
                ->set('title', 'Back Office -> Pop-up Manager');
        }
    }

    /**
     * Function action_index
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Kohana_Cache_Exception
     * @throws \Kohana_Exception
     * @throws \Exception
     *
     * @param null $eventTypeId
     *
     * @return void
     */
    public function action_event($eventTypeId = null)
    {
        $viewData = [
            'eventGroups' => EventGroups::getAll(),
            'eventTypes' => EventType::getAllForViewPrepared(),
            'fields' => $this->_getAllConditionsFields(),
            'logicalOperators' => $this->_getLogicalOperators(),
            'comparisonOperators' => $this->_getComparisonOperators(),
            'sortablePopups' => EventType::getSortablePopupsToken($eventTypeId),
        ];

        if(
            $eventTypeId
            && is_string($eventTypeId)
            && ctype_digit($eventTypeId)
        ){
            $viewData['popups'] = $this->_getPopupsViewPreparedData($eventTypeId);
            $viewData['eventTypeId'] = $eventTypeId;
        }

        $this->template->content = new View(
            $this->request->controller . '/index',
            $viewData
        );
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @throws \Database_TransactionException
     * @throws \Kohana_Cache_Exception
     * @throws \Kohana_Exception403
     *
     * @param null $eventTypeId
     *
     * @return false|string
     */
    public function action_updatePopups($eventTypeId = null)
    {
        $result = false;

        if(
            !$eventTypeId
            || !F::IsAjaxMode()
            || (
                $eventTypeId
                && is_string($eventTypeId)
                && !ctype_digit($eventTypeId)
            )
        ){
            throw new Kohana_Exception403();
        }

        $popups = Arr::get($_POST, 'popups', null);
        if(!empty($popups)){
            list($popupsData, $petData) = $this->_preparePopupsDataForSaving($popups);
            $result = Popups::savePopupsByEventType($popupsData, $petData);
        }

        $this->template->content = json_encode(['success' => $result]);
    }

    /**
     * Function action_edit
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Database_TransactionException
     * @throws \Kohana_Cache_Exception
     * @throws \Kohana_Exception
     * @throws \Kohana_Exception404
     * @throws \UserExeption
     * @throws \Exception
     *
     * @param null $popupId
     *
     * @return void
     */
    public function action_popup($popupId = null)
    {
        if($popupId && is_string($popupId) && !ctype_digit($popupId)){
            throw new Kohana_Exception404();
        }

        if(!empty($popupId)){
            $popupId = (int) $popupId;
        }

        $popupEditor = new PopupEditor($popupId);

        if($popupId && !$popupEditor->isLoaded()){
            throw new Kohana_Exception404();
        }

        $validationErrors = [];

        if($_POST){
            $popupEditor->setData($_POST);

            $validator = new EditPopupValidator(
                $popupEditor->getEventTypeId(),
                $popupEditor->getPopupId()
            );

            if($validator->validate($popupEditor->getData())){
                if(!($result = $popupEditor->savePopup())){
                    $validationErrors[] = 'Popup not saved!';
                }
            } else {
                $validationErrors = $validator->getValidationErrors();
            }

            if(!empty($result)){
                if(!$popupId){
                    /** add session key with a popup Id as the token to show a label (now only new)*/
                    Session::instance()
                        ->set('changedPopupId', $popupEditor->getPopupId());
                }
                $this->request->redirect(
                    "{$this->request->controller}/event/{$popupEditor->getEventTypeId()}"
                );

                return;
            }
        }

        $data = $popupEditor->getData();
        $eventTypeId = (int) Arr::get($_GET, 'eventTypeId', Arr::get($data, 'eventTypeId'));
        $variablesRepository = (new EventVariablesRepository())
            ->setRequestData(new RequestData())
            ->setEventTypeId($eventTypeId);

        $availableVariables = $variablesRepository
            ->getAllowedListWithDescriptions(EventVariable::TYPE_IS_USER);

        $eventType = EventType::get($eventTypeId);
        $popupConditionsData = Arr::get($data, 'conditionsData');
        if(empty($popupConditionsData) && !empty($popupId)){
            $popupConditionsData = $this->_getPopupConditions($popupId);
        }

        $this->template->content = new View(
            $this->request->controller . '/add_edit',
            [
                'editableData' => $data,
                'id' => $popupId,
                'eventTypeId' => $eventTypeId,
                'validationErrors' => $validationErrors,
                'stylesGeneral' => $popupEditor->getGeneralStyles(),
                'scriptsGeneral' => $popupEditor->getGeneralScripts(),
                'countryFlags' => Model_Language::model()
                    ->getFlagsList(),
                'defaultLang' => Language::instance()
                    ->getDefault(),
                'allTemplates' => array_column((new Templates())->getAll(), 'name', 'id'),
                'assetsDomain' => PopuperAssets::instance()
                    ->getHost(),
                'availableVariables' => $availableVariables,
                'eventType' => $eventType,
                'logicalOperators' => $this->_getLogicalOperators(),
                'comparisonOperators' => $this->_getComparisonOperators(),
                'rules' => $popupConditionsData,
                'fields' => (!empty($popupId))
                    ? $this->_getPopupConditionsFieldsByPopup($popupId)
                    : $this->_getPopupConditionsFieldsByEventType($eventTypeId),
            ]
        );
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @throws \Kohana_Cache_Exception
     * @throws \Kohana_Exception
     * @throws \Kohana_Exception404
     * @throws \ReflectionException
     * @throws \Exception
     *
     * @param null $popupId
     */
    public function action_preview($popupId = null)
    {
        if(!F::IsAjaxMode()){
            throw new Kohana_Exception404();
        }

        $response['success'] = false;

        $leadId = Arr::get($_POST, 'leadId');
        $testLeadValidator = new TestLeadValidator(
            [
                'leadId' => $leadId,
                'popupId' => $popupId,
            ]
        );

        if($testLeadValidator->validate()){
            $popupId = (int) $popupId;

            $eventsRepository = new EventsRepository($leadId);
            $variablesRepository = new EventVariablesRepository();

            $response['success'] = (new EventsManager($eventsRepository, $variablesRepository))
                ->setEventTypeId(EventType::EVENT_UNSPECIFIED_ONE_TIME)
                ->saveCurrentEvent([EventVariable::PREVIEW_POPUP_IDS_LIST => $popupId]);

            if(!$response['success']){
                $response['message'] = 'Something went wrong please try again.';
            } else {
                $response['message'] =
                    '<b>Your pop-up preview has been generated.</b>' .
                    ' Please check it on the ' .
                    '<a target="_blank" href="' .
                    $variablesRepository
                        ->getLinksData()
                        ->getSiteLink() .
                    '">' .
                    'website/platform' .
                    '</a>' . ' side.';
            }
        } else {
            $response['message'] = $testLeadValidator->getValidationErrors();
        }

        $this->template->content = json_encode($response);
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @throws \Kohana_Exception
     *
     * @param $popupId
     *
     * @return mixed
     */
    protected function _getPopupConditions($popupId)
    {
        return Identifier::getPopupConditionsData($popupId);
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @throws \Exception
     *
     * @param $popupId
     *
     * @return array
     */
    protected function _getPopupConditionsFieldsByPopup($popupId)
    {
        return PopupConditionsFields::getInstance()
            ->getFieldsDataByPopup($popupId);
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @throws \Exception
     *
     * @param $eventTypeId
     *
     * @return array
     */
    protected function _getPopupConditionsFieldsByEventType($eventTypeId)
    {
        return PopupConditionsFields::getInstance()
            ->getFieldsDataByEventType($eventTypeId);
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @throws \Exception
     * @return mixed
     */
    protected function _getAllConditionsFields()
    {
        return PopupConditionsFields::getInstance()
            ->getAllFieldsData();
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @throws \Kohana_Cache_Exception
     * @throws \Kohana_Exception
     *
     * @param $eventTypeId
     *
     * @return array
     */
    protected function _getPopupsViewPreparedData($eventTypeId)
    {
        $result = [];
        $changedPopupId = Session::instance()
            ->get('changedPopupId');
        $popups = Popups::getWithTemplateByEventType($eventTypeId);
        foreach ($popups as $popup) {
            $popup['isActive'] = (bool) $popup['isActive'];
            $popup['rules'] = $this->_getPopupConditions($popup['id']);
            $popup['wasChanged'] = false;
            /** mark popup for label */
            if($changedPopupId && ($changedPopupId == $popup['id'])){
                Session::instance()
                    ->delete('changedPopupId');
                $popup['wasChanged'] = true;
            }
            $result[] = $popup;
        }

        return $result;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param $popupsData
     *
     * @return array
     */
    protected function _preparePopupsDataForSaving($popupsData)
    {
        $popupsOrder = 1;

        $popups = [];
        $petData = [];

        $popupsData = json_decode($popupsData, true);
        foreach ($popupsData as $popupData) {
            $popups[$popupData['id']] = [
                'isActive' => (int) $popupData['isActive'],
            ];

            $petData[$popupData['id']] = ['order' => $popupsOrder++];
        }

        return [$popups, $petData];
    }

    /**
     * mock data for front
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return array
     */
    private function _getLogicalOperators()
    {
        return ConditionsRepository::getAllLogicOperators();
    }

    /**
     * mock data for front
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return array
     */
    private function _getComparisonOperators()
    {
        return ConditionsRepository::getAllConditionsOperators();
    }

}