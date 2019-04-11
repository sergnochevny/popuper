<?php

namespace Popuper\Builder\Templates;

use View;
use \Popuper\Model\Templates as TemplatesModel;

/**
 * Class AbstractClosableTemplate
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Builder\Templates
 */
abstract class AbstractClosableTemplate extends AbstractTemplate
{

    protected $_closeBtnSelector = '#popuper-modal-close-btn';

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

        $callbacks->load();
        $callbacks->addCallback(
            $this->_closeBtnSelector,
            'click',
            $this->_closeJs
        );
        $this->_callbacks = $callbacks;
    }
}
