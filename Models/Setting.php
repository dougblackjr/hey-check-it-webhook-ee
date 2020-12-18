<?php

namespace HeyCheckItWebhook\Models;

use EllisLab\ExpressionEngine\Service\Model\Model;

class Setting extends Model {

    // Primary Key: This is entry_id in our model.
    protected static $_primary_key = 'id';

    // Table Name: Our database table where our data resides
    protected static $_table_name = 'hci_settings';

    protected $id;
    protected $key;
    protected $value;

}