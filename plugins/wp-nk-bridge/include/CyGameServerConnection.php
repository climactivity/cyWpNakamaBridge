<?php
/**
 * Connects to the game server 
 * currently the connection object is recreated on every call 
 * this could be optimised a lot, but this also ensures that updates
 * to the configuration propagate in a predicatble manner  
 */
class CyGameServerConnection {
    private $api_key;
    private $host;

    /**
     * fetch the current connection parameters from options api on object creation 
     */
    function __construct() {
        $options = get_option( 'cy_settings' );
        $this->api_key = $options['cy_text_field_server_key'];
        $this->host = $options['cy_text_field_hostname'];
    }

    /**
     * tests the current connection parameters
     * @return String latency in ms, error message as defined in WP_Error on failure 
     */
    public function test_connection() {
        $time_pre = microtime(true);
        $result = $this->call_rpc("wp_message",  wp_json_encode( (object) array("message" => "hello")) ); 
        $time_post = microtime(true);
        $exec_time = round(($time_post - $time_pre) * 1000);
        if (!is_wp_error($result)) {
            return "Success, latency: $exec_time ms";
        } else {
            return $result->get_error_message();
        }
    }

    /**
     * Call a remote procedure ¯\_(ツ)_/¯
     * @param rpc_name what endpoint to call 
     *                 because nakama doesn't care which http verb we use just use POST for every interaction 
     *                 this is semantically sound as an rpc api can always be understood to create some sort of
     *                 data on the server. This means we have access to a request body on all rpc calls 
     * @param jsonData the data too to stuff in the request body
     * @return String|WP_Error response data on success, WP_Error on failure
     */
    function call_rpc($rpc_name, $jsonData) {
        $host = $this->host;
        $api_key = $this->api_key;
        $target_url = "$host/v2/rpc/$rpc_name?http_key=$api_key&unwrap";
        $args = array(
            'method' => 'POST',
            'headers' => array(
                "Content-Type" => "application/json",
                "Accept" => "application/json",
            ),
            'body' =>  $jsonData,
        );
        
        return wp_remote_request($target_url, $args);
    }
}

?>