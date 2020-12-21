<?php
namespace packages\sms\listeners\userpanel\users;

use packages\base\{View\Error};
use packages\sms\{Authorization, Get, Sent};
use packages\userpanel\events as UserpanelEvents;
use function packages\userpanel\url;

class BeforeDelete {
	public function check(UserpanelEvents\Users\BeforeDelete $event): void {
		$this->checkSentSMSesSender($event);
		$this->checkSentSMSesReceiver($event);
		$this->checkGetSMSesReceiver($event);
	}
	private function checkSentSMSesSender(UserpanelEvents\Users\BeforeDelete $event): void {
		$user = $event->getUser();
		$hasSentSMSes = (new Sent)->where("sender_user", $user->id)->has();
		if (!$hasSentSMSes) {
			return;
		}
		$message = t("error.packages.sms.error.smses.sent.sender_user.delete_user_warn.message");
		$error = new Error("packages.sms.error.smses.sent.sender_user.delete_user_warn");
		$error->setType(Error::WARNING);
		if (Authorization::is_accessed("sent_list")) {
			$message .= "<br> " . t("packages.sms.error.smses.sent.sender_user.delete_user_warn.view_smses") . " ";
			$error->setData(array(
				array(
					"txt" => '<i class="fa fa-search"></i> ' . t("packages.sms.error.smses.sent.sender_user.delete_user_warn.view_smses_btn"),
					"type" => "btn-warning",
					"link" => url("sms/sent", array(
						"sender_user" => $user->id,
					)),
				),
			), "btns");
		} else {
			$message .= "<br> " . t("packages.sms.error.smses.sent.sender_user.delete_user_warn.view_smses.sent.tell_someone");
		}
		$error->setMessage($message);

		$event->addError($error);
	}
	private function checkSentSMSesReceiver(UserpanelEvents\Users\BeforeDelete $event): void {
		$user = $event->getUser();
		$hasSentSMSes = (new Sent)->where("receiver_user", $user->id)->has();
		if (!$hasSentSMSes) {
			return;
		}
		$message = t("error.packages.sms.error.smses.sent.receiver_user.delete_user_warn.message");
		$error = new Error("packages.sms.error.smses.sent.receiver_user.delete_user_warn");
		$error->setType(Error::WARNING);
		if (Authorization::is_accessed("sent_list")) {
			$message .= "<br> " . t("packages.sms.error.smses.sent.receiver_user.delete_user_warn.view_smses") . " ";
			$error->setData(array(
				array(
					"txt" => '<i class="fa fa-search"></i> ' . t("packages.sms.error.smses.sent.receiver_user.delete_user_warn.view_smses_btn"),
					"type" => "btn-warning",
					"link" => url("sms/sent", array(
						"receiver_user" => $user->id,
					)),
				),
			), "btns");
		} else {
			$message .= "<br> " . t("packages.sms.error.smses.sent.receiver_user.delete_user_warn.view_smses.tell_someone");
		}
		$error->setMessage($message);

		$event->addError($error);
	}
	private function checkGetSMSesReceiver(UserpanelEvents\Users\BeforeDelete $event): void {
		$user = $event->getUser();
		$hasGetSMSes = (new Get)->where("sender_user", $user->id)->has();
		if (!$hasGetSMSes) {
			return;
		}
		$message = t("error.packages.sms.error.smses.get.sender_user.delete_user_warn.message");
		$error = new Error("packages.sms.error.smses.get.sender_user.delete_user_warn");
		$error->setType(Error::WARNING);
		if (Authorization::is_accessed("get_list")) {
			$message .= "<br> " . t("packages.sms.error.smses.get.sender_user.delete_user_warn.view_smses") . " ";
			$error->setData(array(
				array(
					"txt" => '<i class="fa fa-search"></i> ' . t("packages.sms.error.smses.get.sender_user.delete_user_warn.view_smses_btn"),
					"type" => "btn-warning",
					"link" => url("sms/sent", array(
						"sender_user" => $user->id,
					)),
				),
			), "btns");
		} else {
			$message .= "<br> " . t("packages.sms.error.smses.get.sender_user.delete_user_warn.view_smses.tell_someone");
		}
		$error->setMessage($message);

		$event->addError($error);
	}
}
