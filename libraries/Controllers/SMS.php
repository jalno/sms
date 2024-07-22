<?php

namespace packages\sms\Controllers;

use packages\base\DB;
use packages\base\DB\Parenthesis;
use packages\base\HTTP;
use packages\base\InputValidation;
use packages\base\Utility\Safe;
use packages\base\View\Error;
use packages\base\Views\FormError;
use packages\sms\API;
use packages\sms\Authentication;
use packages\sms\Authorization;
use packages\sms\Controller;
use packages\sms\GateWay;
use packages\sms\GateWay\Number;
use packages\sms\Get;
use packages\sms\Sent;
use packages\sms\View;
use themes\clipone\Views\SMS as Views;
use packages\userpanel;
use packages\userpanel\User;

class SMS extends Controller
{
    protected $authentication = true;

    public function sent()
    {
        Authorization::haveOrFail('sent_list');
        $view = View::byName(Views\Sent\ListView::class);
        $types = Authorization::childrenTypes();
        $sent_list_anonymous = Authorization::is_accessed('sent_list_anonymous');
        $inputsRules = [
            'id' => [
                'type' => 'number',
                'optional' => true,
                'empty' => true,
            ],
            'sender_user' => [
                'type' => 'number',
                'optional' => true,
                'empty' => true,
            ],
            'sender_number' => [
                'type' => 'number',
                'optional' => true,
                'empty' => true,
            ],
            'receiver_user' => [
                'type' => 'number',
                'optional' => true,
                'empty' => true,
            ],
            'receiver_number' => [
                'type' => 'number',
                'optional' => true,
                'empty' => true,
            ],
            'text' => [
                'type' => 'string',
                'optional' => true,
                'empty' => true,
            ],
            'status' => [
                'type' => 'number',
                'optional' => true,
                'empty' => true,
            ],
            'word' => [
                'type' => 'string',
                'optional' => true,
                'empty' => true,
            ],
            'comparison' => [
                'values' => ['equals', 'startswith', 'contains'],
                'default' => 'contains',
                'optional' => true,
            ],
        ];
        $this->response->setStatus(true);
        try {
            $inputs = $this->checkinputs($inputsRules);
            if (isset($inputs['status']) and 0 != $inputs['status']) {
                if (!in_array($inputs['status'], [Sent::queued, Sent::sending, Sent::sent, Sent::failed])) {
                    throw new InputValidation('status');
                }
            }
            foreach (['sender_user', 'receiver_user'] as $field) {
                if (isset($inputs[$field]) and 0 != $inputs[$field]) {
                    $user = User::byId($inputs[$field]);
                    if (!$user) {
                        throw new InputValidation($field);
                    }
                    $inputs[$field] = $user->id;
                }
            }

            foreach (['id', 'sender_user', 'receiver_user', 'sender_number', 'receiver_number', 'text', 'status'] as $item) {
                if (isset($inputs[$item]) and $inputs[$item]) {
                    $comparison = $inputs['comparison'];
                    if (in_array($item, ['id', 'status', 'sender_user', 'receiver_user'])) {
                        $comparison = 'equals';
                    }
                    DB::where('sms_sent.'.$item, $inputs[$item], $comparison);
                }
            }
            if (isset($inputs['word']) and $inputs['word']) {
                $parenthesis = new Parenthesis();
                foreach (['sender_number', 'receiver_number', 'text'] as $item) {
                    if (!isset($inputs[$item]) or !$inputs[$item]) {
                        $parenthesis->where('sms_sent.'.$item, $inputs['word'], $inputs['comparison'], 'OR');
                    }
                }
                DB::where($parenthesis);
            }
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
            DB::orderBy('sms_sent.id', ' DESC');
            DB::pageLimit($this->items_per_page);
            $items = DB::paginate('sms_sent', $this->page, ['sms_sent.*']);
            $view->setPaginate($this->page, DB::totalCount(), $this->items_per_page);
            $sents = [];
            foreach ($items as $item) {
                $sents[] = new Sent($item);
            }
            $view->setDataList($sents);
        } catch (InputValidation $error) {
            $view->setFormError(FormError::fromException($error));
            $this->response->setStatus(false);
        }
        $view->setDataForm($this->inputsvalue($inputs));

        $this->response->setStatus(true);
        $this->response->setView($view);

        return $this->response;
    }

    public function get($name)
    {
        Authorization::haveOrFail('get_list');
        $view = View::byName(Views\Get\ListView::class);
        $types = Authorization::childrenTypes();
        $get_list_anonymous = Authorization::is_accessed('get_list_anonymous');
        $inputsRules = [
            'id' => [
                'type' => 'number',
                'optional' => true,
                'empty' => true,
            ],
            'sender_user' => [
                'type' => 'number',
                'optional' => true,
                'empty' => true,
            ],
            'sender_number' => [
                'type' => 'number',
                'optional' => true,
                'empty' => true,
            ],
            'receiver_number' => [
                'type' => 'number',
                'optional' => true,
                'empty' => true,
            ],
            'text' => [
                'type' => 'string',
                'optional' => true,
                'empty' => true,
            ],
            'status' => [
                'type' => 'number',
                'optional' => true,
                'empty' => true,
            ],
            'word' => [
                'type' => 'string',
                'optional' => true,
                'empty' => true,
            ],
            'comparison' => [
                'values' => ['equals', 'startswith', 'contains'],
                'default' => 'contains',
                'optional' => true,
            ],
        ];
        $this->response->setStatus(true);
        try {
            $inputs = $this->checkinputs($inputsRules);
            if (isset($inputs['status']) and 0 != $inputs['status']) {
                if (!in_array($inputs['status'], [Get::unread, Get::read])) {
                    throw new InputValidation('status');
                }
            }
            if (isset($inputs['sender_user']) and 0 != $inputs['sender_user']) {
                $user = User::byId($inputs['sender_user']);
                if (!$user) {
                    throw new InputValidation('sender_user');
                }
                $inputs['sender_user'] = $user->id;
            }

            foreach (['id', 'sender_user', 'sender_number', 'receiver_number', 'text', 'status'] as $item) {
                if (isset($inputs[$item]) and $inputs[$item]) {
                    $comparison = $inputs['comparison'];
                    if (in_array($item, ['id', 'status', 'sender_user'])) {
                        $comparison = 'equals';
                    }
                    DB::where('sms_get.'.$item, $inputs[$item], $comparison);
                }
            }
            if (isset($inputs['word']) and $inputs['word']) {
                $parenthesis = new Parenthesis();
                foreach (['sender_number', 'receiver_number', 'text'] as $item) {
                    if (!isset($inputs[$item]) or !$inputs[$item]) {
                        $parenthesis->where('sms_get.'.$item, $inputs['word'], $inputs['comparison'], 'OR');
                    }
                }
                DB::where($parenthesis);
            }
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
            DB::orderBy('sms_get.id', ' DESC');
            DB::pageLimit($this->items_per_page);
            $items = DB::paginate('sms_get', $this->page, ['sms_get.*']);
            $view->setPaginate($this->page, DB::totalCount(), $this->items_per_page);
            $gets = [];
            foreach ($items as $item) {
                $gets[] = new Get($item);
            }
            $view->setDataList($gets);
        } catch (InputValidation $error) {
            $view->setFormError(FormError::fromException($error));
            $this->response->setStatus(false);
        }
        $view->setDataForm($this->inputsvalue($inputs));

        $this->response->setStatus(true);
        $this->response->setView($view);

        return $this->response;
    }

    public function send()
    {
        $view = View::byName('\\packages\\sms\\views\\send');
        Authorization::haveOrFail('send');
        DB::join('sms_gateways', 'sms_gateways_numbers.gateway=sms_gateways.id', 'inner');
        DB::where('sms_gateways.status', GateWay::active);
        DB::where('sms_gateways_numbers.status', Number::active);
        $numbersData = DB::get('sms_gateways_numbers', null, 'sms_gateways_numbers.*');
        $numbers = [];
        foreach ($numbersData as $data) {
            $numbers[] = new Number($data);
        }

        $view->setNumbers($numbers);
        if (HTTP::is_post()) {
            $this->response->setStatus(false);
            $inputsRules = [
                'to' => [
                    'type' => 'cellphone',
                ],
                'from' => [
                    'type' => 'number',
                    'optional' => true,
                ],
                'text' => [
                    'type' => 'string',
                    'multiLine' => true,
                ],
            ];
            try {
                $inputs = $this->checkinputs($inputsRules);
                $inputs['text'] = str_replace("\r\n", "\n", $inputs['text']); // this is for save charachters

                if (array_key_exists('from', $inputs)) {
                    if (!$inputs['from'] = Number::byId($inputs['from'])) {
                        throw new InputValidation('from');
                    }
                    if (Number::active != $inputs['from']->status or GateWay::active != $inputs['from']->gateway->status) {
                        throw new InputValidation('from');
                    }
                }
                $sms = new API();
                $sms->to($inputs['to']);
                $sms->fromUser(Authentication::getUser());
                if (array_key_exists('from', $inputs)) {
                    $sms->fromNumber($inputs['from']);
                }
                $sms->now();
                if (Sent::sent != $sms->send($inputs['text'])) {
                    throw new SendException();
                }
                $this->response->setStatus(true);
                $this->response->Go(userpanel\url('sms/sent'));
            } catch (InputValidation $error) {
                $view->setFormError(FormError::fromException($error));
            } catch (SendException $error) {
                $error = new Error();
                $error->setCode('sms.send');
                $view->addError($error);
            }
            $view->setDataForm($this->inputsvalue($inputsRules));
        } else {
            $this->response->setStatus(true);
            if (isset(HTTP::$request['get']['to'])) {
                if (Safe::is_cellphone_ir(HTTP::$request['get']['to'])) {
                    $view->setDataForm(Safe::cellphone_ir(HTTP::$request['get']['to']), 'to');
                }
            }
        }
        $this->response->setView($view);

        return $this->response;
    }
}
