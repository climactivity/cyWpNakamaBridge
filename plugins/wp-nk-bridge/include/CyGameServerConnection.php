<?php

class CyGameServerConnection {
    private $api_key;
    private $host;
    private $instance; 

    function __construct() {
        $options = get_option( 'cy_settings' );
        $api_key = $options['cy_text_field_server_key'];
        $host = $options['cy_text_field_hostname'];
    }

     static function get_instance() {
         if ($instance == null) {
             $instance = new CyGameServerConnection();
         }
         return $instance;
     }
}

?>