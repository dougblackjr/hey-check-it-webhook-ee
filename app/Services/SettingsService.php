<?php

namespace HeyCheckItWebhook\Services;

class SettingsService {

	public $baseSettings = [
		'baseUrl',
		'notificationAdminEmail',
	];

	public function __construct()
	{
		$settings = ee('Model')->get('hey_check_it_webhook:Setting')->all();

		foreach ($settings as $setting) {
			$this->{$setting->key} = $setting->value;
		}
	}

	public function getRaw()
	{
		return ee('Model')->get('hey_check_it_webhook:Setting')->all();
	}

	public function set($key, $val)
	{

		$setting = ee('Model')
						->get('hey_check_it_webhook:Setting')
						->filter('key', $key)
						->first();

		if($setting) {
			$setting->value = $val;
			$setting->save();
		} else {
			$setting = ee('Model')->make(
				'hey_check_it_webhook:Setting',
				[
					'key'	=> $key,
					'value' => $val,
				]
			);
			$setting->save();
		}

		return true;
	}

}