<?php

namespace Popuper\Builder\Templates\Bottom;

use Popuper\Builder\Templates\AbstractCloseOnCLickTemplate;
use \Popuper\Model\Templates as TemplatesModel;
use View;

/**
 * Class Right
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Builder\Templates\Bottom
 */
class Right extends AbstractCloseOnCLickTemplate
{
    /**
     * @var int
     */
    protected $_id = TemplatesModel::ID_BOTTOM_RIGHT_CLICK_TO_CLOSE;
}