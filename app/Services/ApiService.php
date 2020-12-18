<?php

namespace HeyCheckItWebhook\Services;

require_once __DIR__ . '/../../vendor/autoload.php';

use Buzz\Browser;
use Buzz\Client\FileGetContents;
use HeyCheckItWebhook\Exceptions\HeyCheckItWebhookException;
use HeyCheckItWebhook\Services\SettingsService;
use Nyholm\Psr7\Factory\Psr17Factory;

class ApiService {

	protected $baseUrl;
	protected $version = '1.0';
	protected $lastRequest;
	protected $statusCode;
	protected $method;
	protected $data = [];
	protected $output = [];
	protected $success;
	protected $settings;

	protected $params = [
		'User-Agent'	=> 'HeyCheckItWebhook',
		'Content-Type'	=> 'application/json',
		// 'Accept'		=> '*/*',
	];

	private $client;
	private $browser;

	public function __construct()
	{
		$this->settings = new SettingsService;
		$this->baseUrl = $this->settings->baseUrl;
		$this->client = new FileGetContents(new Psr17Factory());
		$this->browser = new Browser($this->client, new Psr17Factory());
	}

	public function send($type, $data)
	{

		if(!$this->baseUrl) {
			return;
		}

		$sendData = array_merge(
			['type'	=> $type],
			$data
		);

		$response = $this->browser->post(
			$this->baseUrl,
			$this->params,
			json_encode($sendData)
		);
		$result = $response->getBody()->getContents();
		$body = json_decode($result, true);
		$this->output = $result;
		$this->success = $response->getStatusCode() === 200;
		return $this->success;
	}

	public function output()
	{
		return $this->output;
	}

}