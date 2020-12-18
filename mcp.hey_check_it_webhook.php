<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once __DIR__ . '/vendor/autoload.php';

use EllisLab\ExpressionEngine\Library\CP\Table;
use HeyCheckItWebhook\Services\NotificationService;
use HeyCheckItWebhook\Services\SettingsService;

class hey_check_it_webhook_mcp {

    public function index()
    {
        $table = $this->getNotificationTable();
        // $this->getSyncTable()

        $vars['table'] = $table;
        $vars['massRemoveUrl'] = ee('CP/URL', 'addons/settings/hey_check_it_webhook/notification_read_bulk');

        $this->getNav();

        return [
            'heading'   => 'Notifications',
            'body'      => ee('View')->make('hey_check_it_webhook:index')->render($vars),
        ];
    }

    public function settings()
    {

        $this->getNav();

        $service = new SettingsService;

        $settingsFields = [];
        $vars = [];

        foreach ($service->baseSettings as $setting) {
            $settingsFields[] = [
                'title' => lang('hey_check_it_webhook_' . $setting),
                'fields' => [
                    $setting => [
                        'type' => 'text',
                        'value' => isset($service->{$setting}) ? $service->{$setting} : '',
                        'required' => true
                    ],
                ],
            ];
        }

        $vars['sections'] = [
            $settingsFields
        ];

        // Final view variables we need to render the form
        $vars += [
            'base_url'              => ee('CP/URL', 'addons/settings/hey_check_it_webhook/save'),
            'cp_page_title'         => lang('hey_check_it_webhook_settings'),
            'save_btn_text'         => 'btn_save_settings',
            'save_btn_text_working' => 'btn_saving',
            'breadcrumb' => [
                ee('CP/URL', 'addons/settings/hey_check_it_webhook')->compile() => lang('hey_check_it_webhook_module_name'),
                ee('CP/URL', 'addons/settings/hey_check_it_webhook/settings')->compile() => lang('hey_check_it_webhook_settings')
            ]
        ];

        return ee('View')->make('ee:_shared/form')->render($vars);

    }

    public function save()
    {

        $service = new SettingsService;
        foreach ($service->baseSettings as $setting) {

            $result = ee()->input->get_post($setting);

            if($result) {
                $service->set($setting, $result);
            }

        }

        ee('CP/Alert')->makeInline('fortune-cookie-form')
            ->asIssue()
            ->withTitle(lang('hey_check_it_webhook_settings_saved'))
            ->now();

        ee()->functions->redirect(ee('CP/URL')->make('addons/settings/hey_check_it_webhook/settings'));

    }

    private function getNav()
    {
        $sidebar = ee('CP/Sidebar')->make();
        $header = $sidebar->addHeader('Hey, Check It');
        $list = $header->addBasicList();
        $navItems = [
            'hey_check_it_webhook_notifications'    => '',
            'hey_check_it_webhook_settings'         => '/settings',
        ];

        foreach ($navItems as $key => $item) {
            $list->addItem(
                lang($key), ee('CP/URL', 'addons/settings/hey_check_it_webhook' . $item));
        }

        ee()->cp->set_right_nav($navItems);
    }

    private function getActionTable()
    {
        // Use default options
        $table = ee(
            'CP/Table',
            [
                'autosort' => true,
                'autosearch' => false,
            ]
        );

        $table->setColumns(
            [
                'Action',
                'Description',
                'URL',
            ]
        );

        // Get all actions
        $data = [];
        $actions = ee('Model')->get('Action')
                            ->filter('class', 'hey_check_it_webhook')
                            ->all();

        $url = rtrim(ee()->config->item('site_url'), '/') . '/';

        foreach ($actions as $action) {
            $data[] = [
                lang('hey_check_it_webhook_action_' . $action->method),
                lang('hey_check_it_webhook_action_description_' . $action->method),
                $url . '?ACT=' . $action->action_id,
            ];
        }

        $table->setData($data);

        return $table->viewData();
    }

    private function getNotificationTable()
    {
        // Use default options
        $table = ee(
            'CP/Table',
            [
                'autosort' => true,
                'autosearch' => false,
            ]
        );

        $table->setNoResultsText(lang('hey_check_it_webhook_no_notifications'));
        $table->setColumns(
            [
                'Type',
                'Notification',
                'Date',
                'Data',
                'manage' => [
                    'type'  => Table::COL_TOOLBAR
                ],
                [
                    'type'  => Table::COL_CHECKBOX
                ]
            ]
        );

        // Add the confirm_remove JS script
        ee()->cp->add_js_script(array(
            'file' => array('cp/confirm_remove'),
        ));

        // Get all forms
        $data = [];
        $notifications = ee('Model')
                            ->get('hey_check_it_webhook:Notification')
                            ->filter('read', false)
                            ->all()
                            ->reverse();

        foreach ($notifications as $notification) {

            $markReadUrl = ee('CP/URL', 'addons/settings/hey_check_it_webhook/notification_read/' . $notification->getId());

            $data[] = [
                $notification->type,
                $notification->notification,
                $notification->created_at,
                $notification->data,
                [
                    'toolbar_items' => [
                        'edit' => [
                            'href' => $markReadUrl,
                            'title' => lang('hey_check_it_webhook_mark_read')
                        ],
                    ]
                ],
                [
                    'name' => 'notifications[]',
                    'value' => $notification->getId(),
                    'data'  => [
                        'confirm' => 'Notification: <b>' . htmlentities($notification->id, ENT_QUOTES) . '</b>'
                    ]
                ]
            ];
        }

        $table->setData($data);

        return $table->viewData();
    }

    public function notification_read($id)
    {
        NotificationService::markRead($id);
        ee()->functions->redirect(ee('CP/URL')->make('addons/settings/hey_check_it_webhook'));
    }

    private function getSyncTable()
    {
        // Use default options
        $table = ee(
            'CP/Table',
            [
                'autosort' => true,
                'autosearch' => false,
            ]
        );

        $table->setNoResultsText(lang('hey_check_it_webhook_no_notifications'));
        $table->setColumns(
            [
                'Type',
                'Ran At',
                'Data',
            ]
        );

        // Add the confirm_remove JS script
        ee()->cp->add_js_script(array(
            'file' => array('cp/confirm_remove'),
        ));

        // Get all forms
        $data = [];
        $syncs = ee('Model')
                            ->get('hey_check_it_webhook:Sync')
                            ->all()
                            ->reverse();

        foreach ($syncs as $sync) {
            $data[] = [
                $sync->type,
                $sync->ran_at,
                $sync->data,
            ];
        }

        $table->setData($data);

        return $table->viewData();
    }

}