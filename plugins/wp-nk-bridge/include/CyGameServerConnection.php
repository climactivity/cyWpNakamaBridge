<?php

class CyGameServerConnection {
    private $api_key;
    private $host;

    function __construct() {
        $options = get_option( 'cy_settings' );
        $this->api_key = $options['cy_text_field_server_key'];
        $this->host = $options['cy_text_field_hostname'];
    }

    public function test_connection() {
        $time_pre = microtime(true);
        $result = $this->call_rpc("wp_message", array("message" => "hello")); 
        $time_post = microtime(true);
        $exec_time = round(($time_post - $time_pre) * 1000);
        if (!is_wp_error($result)) {
            return "Success, latency: $exec_time ms";
        } else {
            return implode( "\t", $result);
        }
    }

    function call_rpc($rpc_name, $vars) {
        $host = $this->host;
        $api_key = $this->api_key;
        $target_url = "$host/v2/rpc/$rpc_name?http_key=$api_key&unwrap";
        //return $target_url;
        $payload = wp_json_encode( (object) $vars); 
        // if (!payload) {//
        //     return new WP_Error( "0", "Failed to encode payload");
        // }
        $args = array(
            'method' => 'POST',
            'headers' => array(
                "Content-Type" => "application/json",
                "Accept" => "application/json",
            ),
            'body' =>  $payload,
        );
        
        return wp_remote_request($target_url, $args);
    }
}

?>