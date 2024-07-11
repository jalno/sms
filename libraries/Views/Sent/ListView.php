<?php

namespace packages\sms\Views\Sent;

use packages\base\Views\Traits\Form as FormTrait;
use packages\sms\Authorization;

class ListView extends \packages\sms\Views\ListView
{
    use FormTrait;

    public bool $canSend;

    protected static $navigation;

    public function __construct()
    {
        $this->canSend = Authorization::is_accessed('send');
    }

    public static function onSourceLoad()
    {
        self::$navigation = Authorization::is_accessed('sent_list');
    }
}
