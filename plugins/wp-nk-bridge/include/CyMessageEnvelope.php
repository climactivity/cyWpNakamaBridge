<?php

require_once('CyGameServerConnection.php');

/**
 * Wrap data to ensure required metadata is available on nk side
 */
abstract class CyMessageEnvelope implements JsonSerializable{
    protected $contextUser;
    protected $sendAt;
    protected $messageType;  
    
    /**
     * @param messageType owo what is $this. Should be set by implementing class
     * @param contextUser who is the message for, leave blank to assign it to the global namespace
     * @param sendAt automatically set to now, override if the message types semantics require something else 
     */
    function __construct($messageType = null, $contextUser = "00000000-0000-0000-0000-000000000000", $sendAt = null) {
        if ($sendAt == null) {
            $this->sendAt = date_create('now')->format('Y-m-d H:i:s');
        }
        $this->messageType = $messageType; 
        $this->contextUser = $contextUser;
        $this->sendAt = $sendAt;
    }

    function toJson() {
        return wp_json_encode($this);
    }
    
    // jsonSerialize makes the object compatible with wp_json_encode
    abstract function jsonSerialize();  

    /**
     * Send the message to nakama
     * @return (string|WP_Error) response if successful WP_Error if connection failed 
     */
    public function sendToNk() {
        $connection = new CyGameServerConnection();
        return $connection->call_rpc($this->getRpcTarget(), wp_json_encode($this)); 
    }
    
    /**
     * @return RpcTarget the name of the endpoint on the game server
     */
    abstract function getRpcTarget(); 
     
}

?>