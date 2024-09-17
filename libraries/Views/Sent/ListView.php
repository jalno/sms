<?php

namespace packages\sms\Views\Sent;

use packages\base\Views\Traits\Form as FormTrait;
use packages\sms\Authorization;

class ListView extends \packages\sms\Views\ListView
{
    use FormTrait;

    public bool $canSend;

    public function __construct()
    {
        $this->canSend = Authorization::is_accessed('send');
    }
}
