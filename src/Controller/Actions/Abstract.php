<?php

namespace Controller\Actions;

use Arr;
use Controller\AjaxTemplate;

/**
 * Class ControllerAbstract
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 */
abstract class ControllerAbstract extends AjaxTemplate
{
    /**
     * Function after
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return void
     */
    public function after()
    {
        $scripts = ($this->template->result && is_array($this->template->result))
            ? Arr::get($this->template->result, 'scripts', [])
            : [];

        if ($scripts) {
            $this->template->result['scripts'] = $this->_checkListOfAdditionalFiles(
                $scripts
            );
        }

        $styles = ($this->template->result && is_array($this->template->result))
            ? Arr::get($this->template->result, 'styles', [])
            : [];

        if($styles){
            $this->template->result['styles'] = $this->_checkListOfAdditionalFiles(
                $styles
            );
        }

        parent::after();
    }
}