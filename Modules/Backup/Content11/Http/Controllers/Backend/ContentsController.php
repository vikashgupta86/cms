<?php

namespace Modules\Content\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Backend\BackendBaseController;

class ContentsController extends BackendBaseController
{
    use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'Contents';

        // module name
        $this->module_name = 'contents';

        // directory path of the module
        $this->module_path = 'content::backend';

        // module icon
        $this->module_icon = 'fa-regular fa-sun';

        // module model name, path
        $this->module_model = "Modules\Content\Models\Content";
    }

}
