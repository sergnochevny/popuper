<?php

namespace Popuper\Editor;

use Arr;
use Cache;
use ConditionsTree\Maps\ConditionOperatorsMap;
use Popuper\Builder\Popup;
use Popuper\Conditions\Maps\ConditionViewFieldsMap;
use Popuper\Conditions\Saver as PopupsConditionsSaver;
use Popuper\Model\EventType as EventTypeModel;
use Popuper\Model\Popups as PopupsModel;
use Popuper\Model\PopupsAttributes as PopupsAttributesModel;
use Popuper\Model\PopupsAttributesRevisions as PopupsAttributesRevisionsModel;
use Popuper\Model\PopupsContentsRevisions as PopupsContentsRevisionsModel;
use Popuper\Model\PopupsForEventTypes as PopupsForEventTypesModel;
use Popuper\Model\PopupsForEventTypesRevisions as PopupsForEventTypesRevisionsModel;
use Popuper\Model\PopupsRevisions as PopupsRevisionsModel;
use Popuper\Model\PopupsScripts as PopupsScriptsModel;
use Popuper\Model\PopupsScriptsRevisions as PopupsScriptsRevisionsModel;
use Popuper\Model\PopupsStyles as PopupsStylesModel;
use Popuper\Model\PopupsStylesRevisions as PopupsStylesRevisionsModel;
use User;

/**
 * @author  Andrey Fomov <andrey.fomov@tstechpro.com>
 * @package Popuper\Editor
 */
class Single
{
    /** @var bool */
    protected $_loaded = false;

    /** @var int */
    protected $_popupId;

    /** @var \Popuper\Builder\Popup|null */
    protected $_popup;

    /** @var string */
    protected $_name = '';

    /** @var int */
    protected $_isActive = 0;

    /** @var int */
    protected $_templateId;

    /** @var int */
    protected $_eventTypeId = 0;

    /** @var int */
    protected $_orderForEvent = null;

    /** @var ContentContainer */
    protected $_contentContainer;

    /** @var string */
    protected $_htmlID = '';

    /** @var string */
    protected $_htmlClass = '';

    /** @var array */
    protected $_validationErrors = [];

    /** @var int */
    protected $_defaultEventId = EventTypeModel::EVENT_UNSPECIFIED_ONE_TIME;

    protected $_conditionsData;

    /**
     * Function __construct
     * Single constructor.
     * @throws \Kohana_Cache_Exception
     *
     * @param $popupId
     */
    public function __construct($popupId)
    {
        $this->_popupId = $popupId;
        $this->load();
    }

    /**
     * Function Loaded
     * Get _loaded value
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return bool
     */
    public function isLoaded()
    {
        return !is_null($this->_popup);
    }

    /**
     * Function Name
     * Get _name value
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Function getIsActive
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return int
     */
    public function getIsActive()
    {
        return $this->_isActive;
    }

    /**
     * Function PopupId
     * Get _popupId value
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return int
     */
    public function getPopupId()
    {
        return $this->_popupId;
    }

    /**
     * Function getContentHtml
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return array
     */
    public function getContentByLangs()
    {
        return ($this->isLoaded())
            ? $this->_contentContainer->getContentByLangs()
            : [];
    }

    /**
     * Function getTemplateId
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return int
     */
    public function getTemplateId()
    {
        return $this->_templateId;
    }

    /**
     * Function getGeneralStyles
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return array
     */
    public function getGeneralStyles()
    {
        return ($this->isLoaded())
            ? $this->_popup->getGeneralStyles()
            : [];
    }

    /**
     * Function getCustomStyles
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return array
     */
    public function getCustomStyles()
    {
        return ($this->isLoaded())
            ? $this->_popup->getCustomStyles()
            : [];
    }

    /**
     * Function getGeneralScripts
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return array
     */
    public function getGeneralScripts()
    {
        return ($this->isLoaded())
            ? $this->_popup->getGeneralScripts()
            : [];
    }

    /**
     * Function getCustomScripts
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return array
     */
    public function getCustomScripts()
    {
        return ($this->isLoaded())
            ? $this->_popup->getCustomScripts()
            : [];
    }

    /**
     * Function getHtmlId
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return string
     */
    public function getHtmlId()
    {
        return $this->_htmlID;
    }

    /**
     * Function getHtmlClass
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return string
     */
    public function getHtmlClass()
    {
        return $this->_htmlClass;
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @return int
     */
    public function getEventTypeId()
    {
        return $this->_eventTypeId;
    }

    /**
     * @return mixed
     */
    public function getConditionsData()
    {
        return $this->_conditionsData;
    }

    /**
     * Function setData
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Kohana_Cache_Exception
     *
     * @param array $postData
     *
     * @return \Popuper\Editor\Single
     */
    public function setData(array $postData)
    {
        $data = $this->_filterPostData($postData);

        $data['conditionsData'] = json_decode(Arr::get($data, 'conditionsData', '[]'), true);
        $this->_contentContainer->setContentByLangs(Arr::get($data, 'content'));
        $this->_name = Arr::get($data, 'name', '');
        $this->_templateId = Arr::get($data, 'templateId', '');
        $this->_htmlID = Arr::get($data, 'htmlID', '');
        $this->_htmlClass = Arr::get($data, 'htmlClass', '');
        $this->_conditionsData = Arr::get($data, 'conditionsData', []);

        $newEventId = Arr::get($data, 'eventTypeId');
        if(!$newEventId && !$this->_eventTypeId){
            $newEventId = $this->_defaultEventId;
        }
        if($newEventId){
            $this->_eventTypeId = $newEventId;
        }

        if(!is_null($newOrderForEvent = Arr::get($data, 'orderForEvent'))){
            $this->_orderForEvent = $newOrderForEvent;
        }

        if(is_null($this->_orderForEvent) && $this->_eventTypeId){
            $this->_orderForEvent = (int) (new PopupsForEventTypesModel())
                    ->getMaxOrderForEvent($this->_eventTypeId) + 1;
        }

        $styles = Arr::get($data, 'stylesCustom', []);
        if($styles && is_array($styles)){
            $styles = array_unique($styles);
            $styles = array_combine(range(1, count($styles)), $styles);
        }
        $this->_popup->setCustomStyles($styles);

        $scripts = Arr::get($data, 'scriptsCustom', []);
        if($scripts && is_array($scripts)){
            $scripts = array_unique($scripts);
            $scripts = array_combine(range(1, count($scripts)), $scripts);
        }
        $this->_popup->setCustomScripts($scripts);

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            'content' => $this->getContentByLangs(),
            'name' => $this->getName(),
            'templateId' => $this->getTemplateId(),
            'htmlID' => $this->getHtmlId(),
            'htmlClass' => $this->getHtmlClass(),
            'stylesCustom' => $this->getCustomStyles(),
            'scriptsCustom' => $this->getCustomScripts(),
            'isActive' => $this->getIsActive(),
            'eventTypeId' => $this->getEventTypeId(),
            'conditionsData' => $this->getConditionsData(),
        ];
    }

    /**
     * Function save
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Database_TransactionException
     * @throws \Kohana_Cache_Exception
     * @throws \Kohana_Exception
     * @throws \UserExeption
     * @return bool
     */
    public function savePopup()
    {
        $userId = User::current()
            ->getId();

        $popupData = [
            'name' => $this->_name,
            'templateId' => $this->_templateId,
            'isActive' => $this->_isActive,
        ];
        $savedForId = (new PopupsModel())
            ->set(
                $this->_popupId,
                $popupData
            );
        if($savedForId != $this->_popupId){
            $this->_popupId = $savedForId;
            $this->_contentContainer->reloadForPopupId($this->_popupId);
        }

        $revisionId = (new PopupsRevisionsModel())
            ->set(
                $userId,
                $this->_popupId,
                ($savedForId)
                    ? $popupData
                    : []
            );

        $eventRelationUpdated = (new PopupsForEventTypesModel())
            ->setPopupForEvent(
                $this->_popupId,
                $this->_eventTypeId,
                $this->_orderForEvent
            );

        if($eventRelationUpdated){
            (new PopupsForEventTypesRevisionsModel())
                ->set(
                    $revisionId,
                    $this->_eventTypeId,
                    $this->_orderForEvent
                );
        }

        $attributesData = [
            'id' => $this->_htmlID,
            'class' => $this->_htmlClass,
        ];

        $attributesUpdated = (new PopupsAttributesModel())->save(
            $this->_popupId,
            $attributesData
        );

        if($attributesUpdated){
            (new PopupsAttributesRevisionsModel())
                ->set(
                    $revisionId,
                    $attributesData
                );
        };
        if($contentUpdated = $this->_contentContainer->save()){
            (new PopupsContentsRevisionsModel())
                ->set(
                    $revisionId,
                    $this->_contentContainer
                        ->getContentByLangs()
                );
        }

        $styles = $this->_popup->getCustomStyles();
        $stylesUpdated = (new PopupsStylesModel())
            ->save(
                $this->_popupId,
                $styles
            );

        if($stylesUpdated){
            (new PopupsStylesRevisionsModel())
                ->set(
                    $revisionId,
                    $styles
                );
        }

        $scripts = $this->_popup->getCustomScripts();
        $scriptsUpdated = (new PopupsScriptsModel())
            ->save(
                $this->_popupId,
                $scripts
            );

        if($scriptsUpdated){
            (new PopupsScriptsRevisionsModel())
                ->set(
                    $revisionId,
                    $scripts
                );
        }

        $conditionsUpdated = PopupsConditionsSaver::save(
            $this->_popupId,
            $this->_conditionsData
        );

        $result = $savedForId
            || $attributesUpdated
            || $contentUpdated
            || $eventRelationUpdated
            || $stylesUpdated
            || $scriptsUpdated
            || $conditionsUpdated;

        if($result){
            $this->_clearCache();
            $this->load();
        }

        return $result;
    }

    /**
     * Function load
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Kohana_Cache_Exception
     * @throws \Exception
     * @return void
     */
    public function load()
    {
        $this->_popup = new Popup($this->_popupId, []);
        $this->_name = $this->_popup->getName();
        $this->_isActive = (bool) $this->_popup->isActive();
        $this->_templateId = (int) $this->_popup->getTemplateId();

        $eventRelationData = (new PopupsForEventTypesModel())->getEventTypeByPopup($this->_popupId);
        $this->_eventTypeId = Arr::get($eventRelationData, 'eventTypeId', $this->_defaultEventId);
        $this->_orderForEvent = Arr::get($eventRelationData, 'order');

        $popupAttributes = (new PopupsAttributesModel())->getAll($this->_popupId);
        $this->_htmlID = Arr::get($popupAttributes, 'id', '');
        $this->_htmlClass = Arr::get($popupAttributes, 'class', '');

        $this->_contentContainer = new ContentContainer($this->_popupId);
    }

    /**
     * Function _clearCache
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Kohana_Cache_Exception
     * @return void
     */
    protected function _clearCache()
    {
        $prefix = 'popuper:::';
        $keys = Cache::instance()
            ->getAllKeys($prefix);
        if($keys){
            Cache::instance()
                ->removeAll($prefix);
        }
    }

    /**
     * Function _filterPostData
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param array $postData
     *
     * @return array
     */
    protected function _filterPostData(array $postData)
    {
        $editableFields = [
            'name' => '',
            'templateId' => '',
            'htmlID' => '',
            'htmlClass' => '',
            'content' => [],
            'stylesCustom' => [],
            'scriptsCustom' => [],
            'conditionsData' => '[]',
            'eventTypeId' => '',
        ];

        /** Filter values invalid _POST keys */
        $data = array_replace($editableFields, $postData);

        /** Filter values with invalid types */
        foreach ($data as $key => $datum) {
            if(gettype($datum) != gettype($editableFields[$key])){
                unset($data[$key]);
            }
        }

        return $data;
    }

    /**
     * todo delete
     * translate conditions values to array from string if incoming data
     * is a string with elements separated by ',' and condition operator in (is, is not)
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param array $conditionsData
     *
     * @return array
     */
    protected function _prepareConditionsData(array $conditionsData)
    {
        if(!empty($conditionsData[ConditionViewFieldsMap::RULES])){
            foreach ($conditionsData[ConditionViewFieldsMap::RULES] as &$rule) {
                if(!empty($rule[ConditionViewFieldsMap::ELEMENTS])){
                    foreach ($rule[ConditionViewFieldsMap::ELEMENTS] as &$element) {
                        if(
                            in_array(
                                $element[ConditionViewFieldsMap::CONDITION_COMPARISON_OPERATOR_ID],
                                ConditionOperatorsMap::getMultiOperators()
                            )
                            && !is_array($element[ConditionViewFieldsMap::CONDITION_VALUE])
                        ){
                            $element[ConditionViewFieldsMap::CONDITION_VALUE] = explode(
                                ',',
                                $element[ConditionViewFieldsMap::CONDITION_VALUE]
                            );
                        }
                    }
                }
            }
        }

        return $conditionsData;
    }

}