<?php

namespace packages\sms\Listeners\Settings;

use packages\userpanel\UserType\Permissions;

class UserType
{
    public function permissions_list()
    {
        $permissions = [
            'sent_list',
            'sent_list_anonymous',
            'get_list',
            'get_list_anonymous',
            'send',
            'settings_gateways_list',
            'settings_gateways_add',
            'settings_gateways_edit',
            'settings_gateways_delete',
            'settings_templates_list',
            'settings_templates_add',
            'settings_templates_edit',
            'settings_templates_delete',
        ];
        foreach ($permissions as $permission) {
            Permissions::add('sms_'.$permission);
        }
    }
}
