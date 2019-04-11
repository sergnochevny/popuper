<?php

namespace Popuper\Builder\Templates\Center;

use View;
use Popuper\Builder\Templates\AbstractClosableTemplate;
use \Popuper\Model\Templates as TemplatesModel;

/**
 * Class OverlayClosable
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper
 */
class OverlayClosable extends AbstractClosableTemplate
{
    /**
     * @var int
     */
    protected $_id = TemplatesModel::ID_CENTER_OVERLAY_CLOSABLE;

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

        return View::factory('popups/templates/with_close_btn');
    }
}
