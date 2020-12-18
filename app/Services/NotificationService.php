<?php

namespace HeyCheckItWebhook\Services;

use HeyCheckItWebhook\Services\SettingsService;

class NotificationService {

	public static function info($message, $ship = false, $emails = [], $data = [])
	{
		$notification = self::create('info', $message, $emails, $data);
		if($ship) {
			self::send($notification);
		}
	}

	public static function emergency($message, $ship = false, $emails = [], $data = [])
	{
		$notification = self::create('emergency', $message, $emails, $data);
		if($ship) {
			self::send($notification);
		}
	}

	public static function create($type, $message, $emails = [], $data = [])
	{
		$notification = ee('Model')->make('hey_check_it_webhook:Notification');
		$notification->emails = implode(',', $emails);
		$notification->type = $type;
		$notification->notification = $message;
		$notification->data = json_encode($data);
		$notification->save();
		return $notification;
	}

	public static function send($notification)
	{
		ee()->load->library('email');
		ee()->load->helper('text');

		$settings = new SettingsService;

		ee()->email->wordwrap = true;
		ee()->email->mailtype = 'html';
		ee()->email->from($settings->notificationAdminEmail);
		ee()->email->to($settings->notificationAdminEmail . ($notification->emails ? (',' . $notification->emails) : ''));
		ee()->email->subject('Hey Check It Notification: ' . ucfirst($notification->type));

		$data = json_decode($notification->data);

		$message = "{$notification->notification}\r\n" . print_r($data, true);

		ee()->email->message(entities_to_ascii($message));

		$result = ee()->email->send();

		ee()->email->clear();

		return true;
	}

	public static function markRead($id)
	{
		if(!is_array($id)) {
			$id = [$id];
		}

		foreach ($id as $notificationId) {
			$notification = ee('Model')
								->get('hey_check_it_webhook:Notification')
								->filter('id', $notificationId)
								->first();

			if($notification) {
				$notification->read = true;
				$notification->save();
			}
		}

		return true;
	}
}