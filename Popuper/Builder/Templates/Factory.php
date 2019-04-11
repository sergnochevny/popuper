<?php

namespace Popuper\Builder\Templates;

use Popuper\Model\Popups as PopupsModel;
use \Popuper\Model\PopupsAttributes as PopupsAttributes;

/**
 * Class Factory
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Builder
 */
class Factory
{
    /** @var int */
    protected $_popupId;

    /** @var int */
    protected $_templateId;

    /** @var string */
    protected $_templateName;

    /** @var array */
    protected $_eventData = [];

    /** @var array */
    protected $_leadData = [];

    /**
     * Function __construct
     * Factory constructor.
     *
     * @param $popupId
     *
     * @throws \Exception
     * @throws \Kohana_Cache_Exception
     */
    public function __construct($popupId)
    {
        $this->_popupId = $popupId;
        $this->_templateId = \Arr::get((new PopupsModel())->getById($this->_popupId), 'templateId');
    }

    public function withLeadData(array $leadData = [])
    {
        $this->_leadData = $leadData;

        return $this;

    }

    public function withEventData(array $eventData = [])
    {
        $this->_eventData = $eventData;

        return $this;
    }

    /**
     * Function load
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return TemplateInterface
     * @throws \Exception
     * @throws \Kohana_Cache_Exception
     * @throws \Popuper\Exceptions\Templates
     */
    public function load()
    {
        /** @var TemplateInterface $template */
        $template = $this->_getTemplate();

        if (!$template) {
            return;
        }

        $template->setPopupAttributes((new PopupsAttributes())->getAll($this->_popupId));

        $template->setCallbacks(
            new \Popuper\Builder\Callbacks($this->_popupId)
        );
        return $template;
    }

    /**
     * Function _getTemplate
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return null|TemplateInterface
     * @throws \Kohana_Cache_Exception
     */
    protected function _getTemplate()
    {
        switch ($this->_templateId) {

            case \Popuper\Model\Templates::ID_CENTER_OVERLAY_NON_CLOSABLE:
                return new \Popuper\Builder\Templates\Center\OverlayNonClosable();
                break;
            case \Popuper\Model\Templates::ID_CENTER_OVERLAY_CLOSABLE:
                return new \Popuper\Builder\Templates\Center\OverlayClosable();
                break;
            case \Popuper\Model\Templates::ID_CENTER_OVERLAY_CLOSE_WITH_BTN_ONLY:
                return new \Popuper\Builder\Templates\Center\OverlayCloseWithBtn();
                break;
            case \Popuper\Model\Templates::ID_TOP_FULL_CLICK_TO_CLOSE:
                return new \Popuper\Builder\Templates\Top\Full();
                break;
            case \Popuper\Model\Templates::ID_TOP_LEFT_CLICK_TO_CLOSE:
                return new \Popuper\Builder\Templates\Top\Left();
                break;
            case \Popuper\Model\Templates::ID_TOP_RIGHT_CLICK_TO_CLOSE:
                return new \Popuper\Builder\Templates\Top\Right();
                break;
            case \Popuper\Model\Templates::ID_BOTTOM_FULL_CLICK_TO_CLOSE:
                return new \Popuper\Builder\Templates\Bottom\Full();
                break;
            case \Popuper\Model\Templates::ID_BOTTOM_LEFT_CLICK_TO_CLOSE:
                return new \Popuper\Builder\Templates\Bottom\Left();
                break;
            case \Popuper\Model\Templates::ID_BOTTOM_RIGHT_CLICK_TO_CLOSE:
                return new \Popuper\Builder\Templates\Bottom\Right();
                break;
        }

        return null;
    }

}