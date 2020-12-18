<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once __DIR__ . '/vendor/autoload.php';

class Hey_check_it_webhook_upd {

    public $version = '1.0.0';

    public function install()
    {

        $data = array(
            'module_name'           => 'Hey_check_it_webhook',
            'module_version'        => $this->version,
            'has_cp_backend'        => 'n',
            'has_publish_fields'    => 'n'
        );

        ee()->db->insert('modules', $data);

        // Create model tables
        $this->createSettingsTable();
        $this->createNotificationTable();

        // Add Extension calls
        $this->addExtensionCalls();

        return true;

    }

    public function update($current = '')
    {
        return true;
    }

    public function uninstall()
    {
        ee()->load->dbforge();
        
        ee()->db->where('module_name', 'Hey_check_it_webhook');
        ee()->db->delete('modules');

        ee()->db->where('class', 'Hey_check_it_webhook_ext');
        ee()->db->delete('extensions');

        // Drop tables
        if(ee()->db->table_exists('hci_notifications')) ee()->dbforge->drop_table('hci_notifications');
        if(ee()->db->table_exists('hci_settings')) ee()->dbforge->drop_table('hci_settings');
        return true;
    }

    private function createNotificationTable()
    {
        // Notifications model
        ee()->load->dbforge();
        if(!ee()->db->table_exists('hci_notifications'))
        {

            ee()->dbforge->add_field(
                [
                    // id, our primary key
                    'id'           => [
                        'type'              => 'int',
                        'constraint'        => 6,
                        'unsigned'          => true,
                        'auto_increment'    => true,
                    ],
                    'type'           => [
                        'type'              => 'VARCHAR',
                        'constraint'        => 20,
                    ],
                    'emails'           => [
                        'type'              => 'VARCHAR',
                        'constraint'        => 250,
                    ],
                    'notification'           => [
                        'type'              => 'TEXT',
                    ],
                    'data'           => [
                        'type'              => 'TEXT',
                        'null'              => true,
                    ],
                    'read'           => [
                        'type'              => 'TINYINT',
                        'default'           => 0,
                    ],
                    'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
                ]
            );

            ee()->dbforge->add_key('id', true);

            ee()->dbforge->create_table('hci_notifications');

        }
    }

    private function createSettingsTable()
    {
        ee()->load->dbforge();
        // Notifications model
        if(!ee()->db->table_exists('hci_settings'))
        {

            ee()->dbforge->add_field(
                [
                    // id, our primary key
                    'id'           => [
                        'type'              => 'int',
                        'constraint'        => 6,
                        'unsigned'          => true,
                        'auto_increment'    => true,
                    ],
                    'key'           => [
                        'type'              => 'VARCHAR',
                        'constraint'        => 50,
                    ],
                    'value'           => [
                        'type'              => 'TEXT',
                    ],
                ]
            );

            ee()->dbforge->add_key('id', true);

            ee()->dbforge->create_table('hci_settings');

        }
    }

    private function addExtensionCalls()
    {

        $extensions = [
            'cartthrob_save_customer_info_end',
            'after_channel_entry_insert',
            'after_channel_entry_update',
            'cartthrob_pre_process',
            'cartthrob_on_authorize',
            'cartthrob_on_decline',
            'cartthrob_on_processing',
            'cartthrob_on_fail',
        ];

        foreach ($extensions as $hook) {
            $data = [
                'class'     => 'Hey_check_it_webhook_ext',
                'method'    => $hook,
                'hook'      => $hook,
                'settings'  => serialize([]),
                'priority'  => 10,
                'version'   => $this->version,
                'enabled'   => 'y'
            ];
            ee()->db->insert('extensions', $data);
        }

    }

}