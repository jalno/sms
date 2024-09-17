<?php

namespace packages\sms\Views\Settings\Templates;

use packages\base\Views\Traits\Form as FormTrait;
use packages\sms\Authorization;

class ListView extends \packages\userpanel\Views\ListView
{
    use FormTrait;
    protected $canAdd;
    protected $canEdit;
    protected $canDel;

    public function __construct()
    {
        $this->canAdd = Authorization::is_accessed('settings_templates_add');
        $this->canEdit = Authorization::is_accessed('settings_templates_edit');
        $this->canDel = Authorization::is_accessed('settings_templates_delete');
    }
}
