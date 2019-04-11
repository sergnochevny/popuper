<?php

namespace Popuper\Builder\Templates\Center;

use View;
use Popuper\Builder\Templates\AbstractTemplate;
use \Popuper\Model\Templates as TemplatesModel;

/**
 * Class OverlayNonClosable
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper
 */
class OverlayNonClosable extends AbstractTemplate
{
    /**
     * @var int
     */
    protected $_id = TemplatesModel::ID_CENTER_OVERLAY_NON_CLOSABLE;

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
