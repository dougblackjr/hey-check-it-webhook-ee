<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once __DIR__ . '/vendor/autoload.php';

use HeyCheckItWebhook\Services\ApiService;
use HeyCheckItWebhook\Services\SettingsService;
use HeyCheckItWebhook\Services\NotificationService;

class Hey_check_it_webhook_ext {

	public $version = '1.0.0';

	protected $service;

	public function __construct()
	{
		$this->thirdPartyPath = (ee()->config->item('cartthrob_third_party_path')) ?
		    rtrim(ee()->config->item('cartthrob_third_party_path'), '/') . '/' :
		    PATH_THIRD . 'cartthrob/third_party/';

		$this->paths[] = $this->thirdPartyPath . 'payment_gateways/';
		$this->paths[] = PATH_THIRD . 'cartthrob/cartthrob/plugins/payment_gateways/';

		if (!function_exists('json_decode')) {
		    ee()->load->library('services_json');
		}

		$available_modules = [
		    'subscriptions',
		];

		foreach ($available_modules as $module) {
		    $class = 'Cartthrob_' . $module;
		    $shortName = strtolower($class);

		    if (file_exists(PATH_THIRD . $shortName . '/libraries/' . $class . '.php')) {
		        ee()->load->add_package_path(PATH_THIRD . $shortName . '/');
		        ee()->load->library($shortName);

		        $this->modules[$module] = &ee()->$shortName;

		        ee()->load->remove_package_path(PATH_THIRD . $shortName . '/');
		    } else {
		        $this->modules[$module] = false;
		    }
		}

		// loading these here, because it looks like the package path is lost at some point causing the loading of these later to fail.
		ee()->load->library('logger');

		$this->service = new ApiService;
	}

	public function after_channel_entry_insert($entry, $values)
	{
		// $this->service->send(__FUNCTION__, $values);
		// $message = 'Hey Check It: ' . __FUNCTION__ . ', result: ' . json_encode($this->service->output());
		NotificationService::info($message);
	}

	public function after_channel_entry_update($entry, $values, $modified)
	{
		// $this->service->send(__FUNCTION__, $values);
		// $message = 'Hey Check It: ' . __FUNCTION__ . ', result: ' . json_encode($this->service->output());
		NotificationService::info($message);
	}

	/**
	 * [cartthrob_pre_process description]
	 * @param  array $options [description]
	 * @return [type]          [description]
	 */
	public function cartthrob_pre_process($options)
	{
		// $this->service->send(__FUNCTION__, $options);
		// $message = 'Hey Check It: ' . __FUNCTION__ . ', result: ' . json_encode($this->service->output());
		NotificationService::info($message);
	}

	public function cartthrob_on_decline()
	{
		$cart = $this->makePretty(ee()->cartthrob->cart->toArray(), 'cart_');
		$order = $this->makePretty(ee()->cartthrob->cart->order(), 'order_');
		$data = array_merge(
			$cart,
			$order
		);

		$this->service->send(__FUNCTION__, $data);
		$message = 'Hey Check It: ' . __FUNCTION__ . ', result: ' . json_encode($this->service->output());
		NotificationService::info($message);
	
	}

	public function cartthrob_on_processing()
	{
		$cart = $this->makePretty(ee()->cartthrob->cart->toArray(), 'cart_');
		$order = $this->makePretty(ee()->cartthrob->cart->order(), 'order_');
		$data = array_merge(
			$cart,
			$order
		);

		$this->service->send(__FUNCTION__, $data);
		$message = 'Hey Check It: ' . __FUNCTION__ . ', result: ' . json_encode($this->service->output());
		NotificationService::info($message);
	
	}

	public function cartthrob_on_fail()
	{
		$cart = $this->makePretty(ee()->cartthrob->cart->toArray(), 'cart_');
		$order = $this->makePretty(ee()->cartthrob->cart->order(), 'order_');
		$data = array_merge(
			$cart,
			$order
		);

		$this->service->send(__FUNCTION__, $data);
		$message = 'Hey Check It: ' . __FUNCTION__ . ', result: ' . json_encode($this->service->output());
		NotificationService::info($message);
	
	}

	private function makePretty($data, $prefix = '_')
	{
		$output = [];

		foreach ($data as $key => $value) {
			$output[$prefix . $key] = $value;
		}

		return $output;
	}

}