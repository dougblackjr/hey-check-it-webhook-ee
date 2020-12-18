<?php

return [
    'author'            => 'Doug Black Jr',
    'author_url'        => 'https://triplenerdscore.net',
    'name'              => 'Hey, Check It Webhook',
    'description'       => 'Connect to Hey Check It',
    'version'           => '1.0.0',
    'namespace'         => 'HeyCheckItWebhook',
    'settings_exist'    => true,
    // Advanced settings
    'models'            => [
        'Notification'  => 'Models\Notification',
        'Setting'       => 'Models\Setting',
	],
];