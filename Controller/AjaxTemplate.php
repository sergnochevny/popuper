<?php

namespace Controller;

use Arr;

/**
 * Class AjaxTemplate
 * Controller to process ajax requests.
 * Contains array $this->template->result which will be encoded to JSON and will returned in response
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 */
abstract class AjaxTemplate extends PopuperGeneral
{
    /**
     * @var \View
     */
    public $template = 'popups/template_json';

    /**
     * @var string
     */
    public $contentType = 'application/json';

    /**
     * Function before
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     * @return void
     */
    public function before()
    {
        parent::before();
        $this->template->result = [];
    }

    /**
     * Function after
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return void
     */
    public function after()
    {
        $this->template->content = json_encode($this->template->result);
        parent::after();
    }

    /**
     * Function _getAdditionalRequestParams
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return array
     */
    protected function _getAdditionalRequestParams()
    {
        return array_filter(
            Arr::extract($_POST, $this->_requestAdditionalKeys)
        );
    }
}
