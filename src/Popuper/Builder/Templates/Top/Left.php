<?php

namespace Popuper\Builder\Templates\Top;

use Popuper\Builder\Templates\AbstractCloseOnCLickTemplate;
use \Popuper\Model\Templates as TemplatesModel;
use View;

/**
 * Class Left
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Builder\Templates\Top
 */
class Left extends AbstractCloseOnCLickTemplate
{
    /**
     * @var int
     */
    protected $_id = TemplatesModel::ID_TOP_LEFT_CLICK_TO_CLOSE;
}