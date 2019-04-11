<?php

namespace Popuper\Builder\Templates;

use View;
use \Popuper\Model\Templates as TemplatesModel;

/**
 * Class AbstractCloseOnCLickTemplate
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Builder\Templates
 */
abstract class AbstractCloseOnCLickTemplate extends AbstractTemplate
{

    protected $_closeJs = 'popuper.hideByUser();';

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
        $selector = (\Arr::get($this->_popupAttributes, 'id')) ? : '.popuper-content.popuper-block .popuper-content';

        $callbacks->load();
        $callbacks->addCallback(
            $selector,
            'click',
            $this->_closeJs
        );
        $this->_callbacks = $callbacks;
    }

    /**
     * Function getView
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return \View
     */
    public function getView()
    {

        return View::factory('popups/templates/default');
    }
}
