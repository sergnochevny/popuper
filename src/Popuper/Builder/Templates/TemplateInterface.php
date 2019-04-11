<?php

namespace Popuper\Builder\Templates;

/**
 * Interface TemplateInterface
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Builder\Template
 */
interface TemplateInterface
{

    /**
     * Function getSettings
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return array
     */
    public function getSettings();

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
    public function getHtml();

    /**
     * Function getJs
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return string
     * @throws \Exception
     */
    public function getJs();

    /**
     * Function getView
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return \View
     */
    public function getView();

    /**
     * Function Content
     * Set _content value
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param \Popuper\Builder\Content $content
     */
    public function setContent(\Popuper\Builder\Content $content);

    /**
     * Function PopupAttributes
     * Set _popupAttributes value
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param array $popupAttributes
     */
    public function setPopupAttributes(array $popupAttributes);

    /**
     * Function Callbacks
     * Set _callbacks value
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param \Popuper\Builder\Callbacks $callbacks
     */
    public function setCallbacks(\Popuper\Builder\Callbacks $callbacks);

    /**
     * Function Id
     * Get _id value
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @return mixed
     */
    public function getId();

    /**
     * Function PopupAttributes
     * Get _popupAttributes value
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @return array
     */
    public function getPopupAttributes();
}
