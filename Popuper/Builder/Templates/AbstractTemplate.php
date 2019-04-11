<?php

namespace Popuper\Builder\Templates;

use View;
use \Popuper\Model\Templates as TemplatesModel;

/**
 * Class Template
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper
 */
abstract class AbstractTemplate implements TemplateInterface
{

    protected $_id;

    protected $_data;

    /** @var \Popuper\Builder\Content */
    protected $_content;

    /** @var \Popuper\Builder\Callbacks */
    protected $_callbacks;

    protected $_popupAttributes = [];

    protected $_html = '';

    protected $_js = '';

    protected $_rendered = false;

    /**
     * Function __construct
     * AbstractTemplate constructor.
     *
     * @throws \Kohana_Cache_Exception
     */
    public function __construct()
    {
        $this->_loadData();
    }

    /**
     * Function Content
     * Set _content value
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param \Popuper\Builder\Content $content
     */
    public function setContent(\Popuper\Builder\Content $content)
    {
        $this->_content = $content;
    }

    /**
     * Function PopupAttributes
     * Set _popupAttributes value
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param array $popupAttributes
     */
    public function setPopupAttributes(array $popupAttributes)
    {

        $this->_popupAttributes = $popupAttributes;
    }

    /**
     * Function Callbacks
     * Set _callbacks value
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param \Popuper\Builder\Callbacks $callbacks
     */
    public function setCallbacks(\Popuper\Builder\Callbacks $callbacks)
    {

        $this->_callbacks = $callbacks;
    }

    /**
     * Function getSettings
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return array
     */
    public function getSettings()
    {
        return array_filter(\Arr::extract($this->_data, ['class', 'overlay']));
    }

    /**
     * Function getHtml
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return string
     * @throws \Exception
     */
    public function getHtml()
    {

        if (!$this->_rendered) {
            $this->_render();
        }

        return $this->_html;
    }

    /**
     * Function getJs
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return mixed|string
     * @throws \Exception
     */
    public function getJs()
    {

        if (!$this->_rendered) {
            $this->_render();
        }

        return ($this->_js) ? str_replace(
            [],
            '',
            View::factory('popups/executableJs/layout', ['contentJS' => $this->_js])
        ) : '';
    }

    /**
     * Function Id
     * Get _id value
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @return mixed
     */
    public function getId()
    {

        return $this->_id;
    }

    /**
     * Function PopupAttributes
     * Get _popupAttributes value
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @return array
     */
    public function getPopupAttributes()
    {

        return $this->_popupAttributes;
    }

    /**
     * Function _loadData
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return void
     * @throws \Exception
     * @throws \Kohana_Cache_Exception
     */
    protected function _loadData()
    {
        $this->_data = (new TemplatesModel)->getWithOverlay($this->_id);

    }

    /**
     * Function _render
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return void
     * @throws \Exception
     */
    protected function _render()
    {
        $view = $this->getView();
        $view->set('popupAttributes', $this->_popupAttributes);
        $view->set('content', $this->_content->getBody());
        $this->_html = (string) $view;
        $this->_js = (string) $this->_callbacks->getRendered();
        $this->_rendered = true;

    }
}
