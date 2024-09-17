<?php

namespace packages\sms\Listeners;

use packages\base\DB;
use packages\base\DB\Parenthesis;
use packages\base\Translator;
use packages\sms\Authentication;
use packages\sms\Authorization;
use packages\sms\Get;
use packages\sms\Sent;
use packages\userpanel;
use packages\userpanel\Date;
use packages\userpanel\Events\Search as Event;
use packages\userpanel\Search as SearchHandler;
use packages\userpanel\Search\Link;

class Search
{
    public function find(Event $e)
    {
        if (Authorization::is_accessed('sent_list')) {
            $this->sent($e->word);
        }
        if (Authorization::is_accessed('get_list')) {
            $this->get($e->word);
        }
    }

    public function get($word)
    {
        $types = Authorization::childrenTypes();
        $get_list_anonymous = Authorization::is_accessed('get_list_anonymous');
        $parenthesis = new Parenthesis();
        foreach (['sender_number', 'receiver_number', 'text'] as $item) {
            $parenthesis->where('sms_get.'.$item, $word, 'contains', 'OR');
        }
        DB::where($parenthesis);
        if ($get_list_anonymous) {
            DB::join('userpanel_users', 'userpanel_users.id=sms_get.sender_user', 'left');
            $parenthesis = new Parenthesis();
            $parenthesis->where('userpanel_users.type', $types, 'in');
            $parenthesis->where('sms_get.sender_user', null, 'is', 'or');
            DB::where($parenthesis);
        } else {
            DB::join('userpanel_users', 'userpanel_users.id=sms_get.sender_user', 'inner');
            if ($types) {
                DB::where('userpanel_users.type', $types, 'in');
            } else {
                DB::where('userpanel_users.id', Authentication::getID());
            }
        }
        DB::orderBy('sms_get.id', 'DESC');
        $items = DB::get('sms_get', null, ['sms_get.*']);
        $gets = [];
        foreach ($items as $item) {
            $gets[] = new Get($item);
        }
        foreach ($gets as $get) {
            $result = new Link();
            $result->setLink(userpanel\url('sms/get', ['id' => $get->id]));
            $result->setTitle(t('sms.get.bySenderNumber', [
                'senderNumber' => $get->sender_number,
            ]));
            $result->setDescription(t('sms.get.description', [
                'receive_at' => Date::format('Y/m/d H:i:s', $get->receive_at),
                'text' => mb_substr($get->text, 0, 70),
            ]));
            SearchHandler::addResult($result);
        }
    }

    public function sent($word)
    {
        $types = Authorization::childrenTypes();
        $sent_list_anonymous = Authorization::is_accessed('sent_list_anonymous');
        $parenthesis = new Parenthesis();
        foreach (['sender_number', 'receiver_number', 'text'] as $item) {
            $parenthesis->where('sms_sent.'.$item, $word, 'contains', 'OR');
        }
        DB::where($parenthesis);
        if ($sent_list_anonymous) {
            DB::join('userpanel_users', 'userpanel_users.id=sms_sent.receiver_user', 'left');
            $parenthesis = new Parenthesis();
            $parenthesis->where('userpanel_users.type', $types, 'in');
            $parenthesis->where('sms_sent.receiver_user', null, 'is', 'or');
            DB::where($parenthesis);
        } else {
            DB::join('userpanel_users', 'userpanel_users.id=sms_sent.receiver_user', 'inner');
            if ($types) {
                DB::where('userpanel_users.type', $types, 'in');
            } else {
                DB::where('userpanel_users.id', Authentication::getID());
            }
        }
        DB::orderBy('sms_sent.id', 'DESC');
        $items = DB::get('sms_sent', null, ['sms_sent.*']);
        $sents = [];
        foreach ($items as $item) {
            $sents[] = new Sent($item);
        }
        foreach ($sents as $sent) {
            $result = new Link();
            $result->setLink(userpanel\url('sms/sent', ['id' => $sent->id]));
            $result->setTitle(t('sms.sent.byReceiverNumber', [
                'receiverNumber' => $sent->receiver_number,
            ]));
            $result->setDescription(t('sms.sent.description', [
                'send_at' => Date::format('Y/m/d H:i:s', $sent->send_at),
                'text' => mb_substr($sent->text, 0, 70),
            ]));
            SearchHandler::addResult($result);
        }
    }
}
