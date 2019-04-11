<?php

namespace Popuper\Builder;

use Popuper\Builder\Templates\Factory as TemplatesFactory;
use Popuper\Model\Popups;
use Popuper\Model\PopupsScripts;
use Popuper\Model\PopupsStyles;
use Popuper\Builder\Templates\TemplateInterface;
use SystemOptions;

/**
 * Class Popup
 * @author  Andrey Fomov <andrey.fomov@tstechpro.com>
 * @package Popuper
 */
class Popup
{
    /** @var int */
    protected $_popupId;

    /** @var string */
    protected $_name = '';

    /** @var bool */
    protected $_isActive = false;

    /** @var array */
    protected $_generalScripts = [];

    /** @var array */
    protected $_customScripts = [];

    /** @var array */
    protected $_generalStyles = [];

    /** @var array */
    protected $_customStyles = [];

    /** @var array */
    protected $_validationRules = [];

    /** @var TemplateInterface */
    protected $_template;

    /** @var array */
    protected $_variables = [];

    /**
     * Function __construct
     * Popup constructor.
     * @throws \Exception
     * @throws \Kohana_Cache_Exception
     *
     * @param int $popupId
     * @param array $variables
     */
    public function __construct($popupId, array $variables = [])
    {
        $this->_variables = $variables;

        $this->_loadSelfData($popupId);
        $this->_loadScripts();
        $this->_loadStyles();
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     *
     * @param array $vars
     *
     * @return \Popuper\Builder\Popup
     */
    public function setVariables(array $vars)
    {
        $this->_variables = $vars;

        return $this;
    }

    /**
     * Function getSettings
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     * @throws \Kohana_Cache_Exception
     * @return mixed
     */
    public function getSettings()
    {
        if(!$this->_template){
            $this->_loadTemplate();
        }

        if(!$this->_template){
            return [];
        }

        return $this->_template->getSettings();
    }

    /**
     * Function getHtml
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     * @throws \Kohana_Cache_Exception
     * @return string
     */
    public function getHtml()
    {
        if(!$this->_template){
            $this->_loadTemplate();
        }

        if(!$this->_template){
            return '';
        }

        return $this->_template->getHtml();
    }

    /**
     * Function getAllScripts
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return array
     */
    public function getAllScripts()
    {
        return array_merge($this->_generalScripts, $this->_customScripts);
    }

    /**
     * Function getAllStyles
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return array
     */
    public function getAllStyles()
    {
        return array_merge($this->_generalStyles, $this->_customStyles);
    }

    /**
     * Function GeneralScripts
     * Get _generalScripts value
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return array
     */
    public function getGeneralScripts()
    {
        return $this->_generalScripts;
    }

    /**
     * Function CustomScripts
     * Get _customScripts value
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return array
     */
    public function getCustomScripts()
    {
        return $this->_customScripts;
    }

    /**
     * Function CustomScripts
     * Set _customScripts value
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param array $customScripts
     */
    public function setCustomScripts($customScripts)
    {
        $this->_customScripts = $customScripts;
    }

    /**
     * Function GeneralStyles
     * Get _generalStyles value
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return array
     */
    public function getGeneralStyles()
    {
        return $this->_generalStyles;
    }

    /**
     * Function CustomStyles
     * Get _customStyles value
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return array
     */
    public function getCustomStyles()
    {
        return $this->_customStyles;
    }

    /**
     * Function CustomStyles
     * Set _customStyles value
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param array $customStyles
     *
     * @return \Popuper\Builder\Popup
     */
    public function setCustomStyles($customStyles)
    {
        $this->_customStyles = $customStyles;

        return $this;
    }

    /**
     * Function getJS
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     * @throws \Kohana_Cache_Exception
     * @return string
     */
    public function getJS()
    {
        if(!$this->_template){
            $this->_loadTemplate();
        }

        if(!$this->_template){
            return '';
        }

        return $this->_template->getJs();
    }

    /**
     * Function getTemplateId
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Kohana_Cache_Exception
     * @return mixed
     */
    public function getTemplateId()
    {
        if(!$this->_template){
            $this->_loadTemplate();
        }

        if(!$this->_template){
            return null;
        }

        return $this->_template->getId();
    }

    /**
     * Function Name
     * Get _name value
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return int
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Function isActive
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return bool
     */
    public function isActive()
    {
        return $this->_isActive;
    }

    /**
     * Function exists
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return bool
     */
    public function exists()
    {
        return (bool) $this->_popupId;
    }

    /**
     * Function _loadTemplate
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     * @throws \Kohana_Cache_Exception
     * @return void
     */
    protected function _loadTemplate()
    {
        $this->_template = (new TemplatesFactory($this->_popupId))->load();
        $content = new Content($this->_popupId, $this->_variables);
        $content->load();

        if($this->_template){
            $this->_template
                ->setContent($content);
        }
    }

    /**
     * Function _loadScripts
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     * @throws \Kohana_Cache_Exception
     * @return void
     */
    protected function _loadScripts()
    {
        $this->_generalScripts = SystemOptions::get('Pop-uper//General JS-files')
            ?: [];
        $this->_customScripts = (new PopupsScripts())
            ->getByPopupId($this->_popupId);
    }

    /**
     * Function _loadStyles
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     * @throws \Kohana_Cache_Exception
     * @return void
     */
    protected function _loadStyles()
    {
        $this->_generalStyles = SystemOptions::get('Pop-uper//General CSS-files')
            ?: [];
        $this->_customStyles = (new PopupsStyles())->getByPopupId($this->_popupId);
    }

    /**
     * Function _loadSelfData
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Kohana_Cache_Exception
     *
     * @param int $popupId
     *
     * @return void
     */
    protected function _loadSelfData($popupId)
    {
        $data = (new Popups())->getById($popupId);

        if(!$data){
            return;
        }

        $this->_popupId = $popupId;
        $this->_name = \Arr::get($data, 'name', '');
        $this->_isActive = (bool) \Arr::get($data, 'isActive', false);
    }
}