<?php

namespace Popuper\Builder;

/**
 * Class Popup Repository
 * @package Popuper
 */
class Repository
{
    /** @var \Popuper\Event\InterfaceEvent */
    protected $_event;

    /** @var array */
    protected $_eventVariables;

    /** @var \Popuper\Builder\Popup */
    protected $_popup;

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @throws \Kohana_Cache_Exception
     * @return \Popuper\Builder\Popup|null
     */
    public function getActualPopup()
    {
        if(
            !$this->_event
        ){
            return null;
        }

        return (new PopupsBuilder(
            $this->_event->getTypeId(),
            $this->_eventVariables
        ))
            ->getPopup();
    }

    /**
     * Function hasValidPopup
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Kohana_Cache_Exception
     * @return bool
     */
    public function hasValidPopup()
    {
        if(
            !$this->_event
        ){
            return false;
        }

        if(!$this->_popup){
            $this->_popup = $this->getActualPopup();
        }

        return (bool) $this->_popup;
    }

    /**
     * Function getPopupData
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     * @throws \Kohana_Cache_Exception
     * @return array
     */
    public function getPopupData()
    {
        $result = [];
        if($this->hasValidPopup()){
            $popup = $this->_getCurrentPopup();
            $result['settings'] = $popup->getSettings();
            $result['html'] = $popup->getHtml();
            $result['js'] = $popup->getJS();
            $result['scripts'] = $popup->getAllScripts();
            $result['styles'] = $popup->getAllStyles();
            $result['typeId'] = $this->_event->getTypeId();
        }

        return $result;
    }

    /**
     * @param \Popuper\Event\InterfaceEvent $event
     *
     * @return Repository
     */
    public function setEvent($event)
    {
        $this->_event = $event;

        return $this;
    }

    /**
     * @param array $eventVariables
     *
     * @return Repository
     */
    public function setEventVariables(array $eventVariables)
    {
        $this->_eventVariables = $eventVariables;

        return $this;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return \Popuper\Builder\Popup
     */
    protected function _getCurrentPopup()
    {
        return $this->_popup;
    }

}