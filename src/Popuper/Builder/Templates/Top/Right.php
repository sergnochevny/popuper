<?php

namespace Popuper\Builder\Templates\Top;

use Popuper\Builder\Templates\AbstractCloseOnCLickTemplate;
use \Popuper\Model\Templates as TemplatesModel;
use View;

/**
 * Class Right
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Builder\Templates\Top
 */
class Right extends AbstractCloseOnCLickTemplate
{
    /**
     * @var int
     */
    protected $_id = TemplatesModel::ID_TOP_RIGHT_CLICK_TO_CLOSE;
}