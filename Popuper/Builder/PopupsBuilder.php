<?php

namespace Popuper\Builder;

use Arr;
use Popuper\Conditions\Identifier as PopupIdentifier;
use Popuper\Model\EventVariable;
use Popuper\Model\Popups as PopupsModel;

/**
 * Class PopupsBuilder
 * @author  Andrey Fomov <andrey.fomov@tstechpro.com>
 * @package Popuper\Builder
 */
class PopupsBuilder
{
    /** @var int */
    protected $_eventTypeId;

    /** @var mixed id of Popup */
    protected $_popup;

    /** @var bool */
    protected $_loaded = false;

    /** @var array */
    protected $_variables;

    /**
     * Function __construct
     * Factory constructor.
     *
     * @param integer $eventTypeId
     * @param $variables
     */
    public function __construct($eventTypeId, $variables)
    {
        $this->_eventTypeId = $eventTypeId;
        $this->_variables = $variables;
    }

    /**
     * Function load
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     * @throws \Kohana_Cache_Exception
     * @return $this
     */
    public function load()
    {
        $this->_popup = null;

        $popupId = $this->_tryToGetCustomPopups();
        if(!$popupId){
            /** @var array $elementsByPopups Get all enabled popups for this event type and prepare them to process */
            $popupId = PopupIdentifier::getPopupByEventType(
                $this->_eventTypeId,
                $this->_variables
            );
        }

        if($popupId){
            $this->_popup = (new Popup($popupId))
                ->setVariables($this->_variables);
        }

        $this->_loaded = true;

        return $this;
    }

    /**
     * Function getPopup
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     * @throws \Kohana_Cache_Exception
     * @return Popup|null
     */
    public function getPopup()
    {
        if(!$this->_loaded){
            $this->load();
        }

        return $this->_popup;
    }

    /**
     * Function _tryToGetCustomPopups
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Kohana_Cache_Exception*@throws \Exception
     * @throws \Exception
     * @return array
     */
    protected function _tryToGetCustomPopups()
    {
        /** get popups for preview */
        $popupsIds = $this->_getPreviewPopupsIds();
        /** Add custom popups*/
        $popupsIds += $this->_getCustomPopupsIds();

        if(!$popupsIds){
            return null;
        }
        $popupsIds = array_unique($popupsIds);

        return array_shift($popupsIds);
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @throws \Kohana_Cache_Exception
     * @throws \Exception
     * @return array
     */
    protected function _getCustomPopupsIds()
    {
        $customPopupsIds = Arr::get(
            $this->_variables,
            EventVariable::CUSTOM_POPUP_IDS_LIST,
            []
        );

        /** Validate custom popups on isActive = 1 */
        return !empty($customPopupsIds)
            ? array_keys(
                (new PopupsModel())
                    ->getByIdsArray($customPopupsIds, true)
            )
            : [];
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @throws \Exception
     * @return array
     */
    protected function _getPreviewPopupsIds()
    {
        $previewPopupsIds = Arr::get(
            $this->_variables,
            EventVariable::PREVIEW_POPUP_IDS_LIST,
            []
        );

        return is_array($previewPopupsIds)
            ? $previewPopupsIds
            : (($previewPopupsIds !== null)
                ? [$previewPopupsIds]
                : []
            );
    }

}