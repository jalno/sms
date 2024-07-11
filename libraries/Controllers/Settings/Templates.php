<?php

namespace packages\sms\Controllers\Settings;

use packages\base\DB\DuplicateRecord;
use packages\base\DB\Parenthesis;
use packages\base\HTTP;
use packages\base\InputValidation;
use packages\base\NotFound;
use packages\base\Translator;
use packages\base\Views\FormError;
use packages\sms\Authorization;
use packages\sms\Controller;
use packages\sms\Events\Templates as TemplatesEvent;
use packages\sms\Template;
use packages\sms\View;
use packages\userpanel;

class Templates extends Controller
{
    protected $authentication = true;

    public function listtemplates()
    {
        Authorization::haveOrFail('settings_templates_list');
        $view = View::byName('\\packages\\sms\\views\\settings\\templates\\listview');
        $template = new Template();
        $inputsRules = [
            'id' => [
                'type' => 'number',
                'optional' => true,
                'empty' => true,
            ],
            'name' => [
                'type' => 'string',
                'optional' => true,
                'empty' => true,
            ],
            'lang' => [
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
                if (!in_array($inputs['status'], [Template::active, Template::deactive])) {
                    throw new InputValidation('status');
                }
            }

            foreach (['id', 'name', 'lang', 'status'] as $item) {
                if (isset($inputs[$item]) and $inputs[$item]) {
                    $comparison = $inputs['comparison'];
                    if (in_array($item, ['id', 'lang', 'status'])) {
                        $comparison = 'equals';
                    }
                    $template->where($item, $inputs[$item], $comparison);
                }
            }
            if (isset($inputs['word']) and $inputs['word']) {
                $parenthesis = new Parenthesis();
                foreach (['name', 'text'] as $item) {
                    if (!isset($inputs[$item]) or !$inputs[$item]) {
                        $parenthesis->where('sms_templates.'.$item, $inputs['word'], $inputs['comparison'], 'OR');
                    }
                }
                $template->where($parenthesis);
            }
        } catch (InputValidation $error) {
            $view->setFormError(FormError::fromException($error));
            $this->response->setStatus(false);
        }
        $view->setDataForm($this->inputsvalue($inputsRules));
        $template->orderBy('id', 'ASC');
        $template->pageLimit = $this->items_per_page;
        $items = $template->paginate($this->page);
        $view->setPaginate($this->page, $template->totalCount, $this->items_per_page);
        $view->setDataList($items);
        $this->response->setView($view);

        return $this->response;
    }

    public function add()
    {
        Authorization::haveOrFail('settings_templates_add');
        $view = View::byName(\packages\sms\Views\Settings\Templates\Add::class);
        $templates = new TemplatesEvent();
        $view->setTemplates($templates->get());
        if (HTTP::is_post()) {
            $inputsRules = [
                'name' => [
                    'type' => 'string',
                ],
                'text' => [],
                'lang' => [
                    'type' => 'string',
                    'values' => Translator::$allowlangs,
                ],
                'status' => [
                    'type' => 'number',
                    'values' => [Template::active, Template::deactive],
                ],
            ];
            $this->response->setStatus(true);
            try {
                $inputs = $this->checkinputs($inputsRules);
                if (Template::where('name', $inputs['name'])->where('lang', $inputs['lang'])->has()) {
                    throw new DuplicateRecord('name');
                }
                $template = $templates->getByName($inputs['name']);
                $templateObj = new Template();
                $templateObj->name = $inputs['name'];
                $templateObj->status = $inputs['status'];
                $templateObj->lang = $inputs['lang'];
                $templateObj->text = $inputs['text'];
                if ($template) {
                    $templateObj->variables = $template->variables;
                    $templateObj->event = $template->event;
                    $templateObj->render = $template->render;
                }
                $templateObj->save();
                $this->response->setStatus(true);
                $this->response->Go(userpanel\url('settings/sms/templates/edit/'.$templateObj->id));
            } catch (InputValidation $error) {
                $view->setFormError(FormError::fromException($error));
                $this->response->setStatus(false);
            } catch (DuplicateRecord $error) {
                $view->setFormError(FormError::fromException($error));
                $this->response->setStatus(false);
            }
            $view->setDataForm($this->inputsvalue($inputsRules));
        } else {
            $this->response->setStatus(true);
        }
        $this->response->setView($view);

        return $this->response;
    }

    public function delete($data)
    {
        Authorization::haveOrFail('settings_templates_delete');
        $template = (new Template())->byID($data['template']);
        if (!$template) {
            throw new NotFound();
        }
        $view = View::byName(\packages\sms\Views\Settings\Templates\Delete::class);
        $view->setTemplate($template);
        if (HTTP::is_post()) {
            $template->delete();

            $this->response->setStatus(true);
            $this->response->Go(userpanel\url('settings/sms/templates'));
        } else {
            $this->response->setStatus(true);
        }
        $this->response->setView($view);

        return $this->response;
    }

    public function edit($data)
    {
        Authorization::haveOrFail('settings_templates_edit');
        $templateObj = (new Template())->byID($data['template']);
        if (!$templateObj) {
            throw new NotFound();
        }
        $view = View::byName(\packages\sms\Views\Settings\Templates\Edit::class);
        $view->setTemplate($templateObj);
        $templates = new TemplatesEvent();
        $view->setTemplates($templates->get());
        if (HTTP::is_post()) {
            $inputsRules = [
                'name' => [
                    'type' => 'string',
                    'optional' => true,
                ],
                'text' => [
                    'optional' => true,
                ],
                'lang' => [
                    'type' => 'string',
                    'values' => Translator::$allowlangs,
                    'optional' => true,
                ],
                'status' => [
                    'type' => 'number',
                    'values' => [Template::active, Template::deactive],
                    'optional' => true,
                ],
            ];
            $this->response->setStatus(true);
            try {
                $inputs = $this->checkinputs($inputsRules);
                if (isset($inputs['name']) and $inputs['name'] != $templateObj->name) {
                    $templateExsits = Template::where('name', $inputs['name']);
                    if (isset($inputs['lang'])) {
                        $templateExsits->where('lang', $inputs['lang']);
                    } else {
                        $templateExsits->where('lang', $templateObj->lang);
                    }
                    if ($templateExsits->has()) {
                        throw new DuplicateRecord('name');
                    }
                    unset($templateExsits);
                    $template = $templates->getByName($inputs['name']);
                    $templateObj->name = $inputs['name'];
                    if ($template) {
                        $templateObj->variables = $template->variables;
                        $templateObj->event = $template->event;
                        $templateObj->render = $template->render;
                    } else {
                        $templateObj->variables = null;
                        $templateObj->event = null;
                        $templateObj->render = null;
                    }
                } elseif (isset($inputs['lang']) and $inputs['lang'] != $templateObj->lang) {
                    if (Template::where('name', $templateObj->lang)->where('lang', $inputs['lang'])->has()) {
                        throw new DuplicateRecord('lang');
                    }
                }

                foreach (['lang', 'text', 'status'] as $key) {
                    if (isset($inputs[$key])) {
                        $templateObj->$key = $inputs[$key];
                    }
                }
                $templateObj->save();
                $this->response->setStatus(true);
            } catch (InputValidation $error) {
                $view->setFormError(FormError::fromException($error));
                $this->response->setStatus(false);
            } catch (DuplicateRecord $error) {
                $view->setFormError(FormError::fromException($error));
                $this->response->setStatus(false);
            }
            $view->setDataForm($this->inputsvalue($inputsRules));
        } else {
            $this->response->setStatus(true);
        }
        $this->response->setView($view);

        return $this->response;
    }
}
