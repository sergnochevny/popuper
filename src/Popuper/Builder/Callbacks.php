<?php
namespace Popuper\Builder;

/**
 * Class Callbacks
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Builder
 */
class Callbacks
{
    /**
     * @var
     */
    protected $_popupId;

    /**
     * @var bool
     */
    protected $_loaded = false;

    /**
     * @var array
     */
    protected $_data = [];

    /**
     * @var string
     */
    protected $_view = 'popups/executableJs/jquery';

    /**
     * Function __construct
     * Callbacks constructor.
     *
     * @param $popupId
     */
    public function __construct($popupId)
    {
        $this->_popupId = $popupId;
    }

    /**
     * Function get
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return array
     */
    public function getRaw()
    {

        if (!$this->_loaded) {
            $this->load();
        }

        return $this->_data;

    }

    /**
     * Function addCallback
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $selector
     * @param $trigger
     * @param $callbackBody
     *
     * @return void
     */
    public function addCallback($selector, $trigger, $callbackBody)
    {
        $this->_data[$selector][$trigger][] = $callbackBody;
    }


    /**
     * Function getRendered
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return string
     */
    public function getRendered()
    {

        if (!$this->_loaded) {
            $this->load();
        }

        $output = '';

        foreach ($this->_data as $selector => $elementCallbacks) {
            foreach ($elementCallbacks as $trigger => $callbacks) {
                $calbacksStr = trim(implode(' ', $callbacks));
                if (!$calbacksStr) {
                    continue;
                }
                $output .= \View::factory(
                    $this->_view,
                    [
                        'selector' => $selector,
                        'trigger'  => $trigger,
                        'callback' => $calbacksStr,
                    ]
                );
            }
        }

        return $output;

    }

    /**
     * Function load
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return void
     */
    public function load()
    {
        $allCallbacks = (new \Popuper\Model\Callbacks())->getByPopupId($this->_popupId);

        foreach ($allCallbacks as $callback) {
            $this->addCallback($callback['selector'], $callback['action'], $callback['value']);
        }
    }

    /**
     * Function reset
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return void
     */
    public function reset()
    {
        $this->_data = [];
    }


}