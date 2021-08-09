<?php

// type WpMessageEnvelope = {
//     contextUser? : string
//     sendAt? : Date
//     messageId?: string  
//     message? : string
// }

abstract class CyMessageEnvelope {
    private $contextUser;
    private $sendAt;
    private $messageType;  

    function __construct($messageType = null, $contextUser = null, $sendAt = null) {
        if ($sendAt == null) {
            $this->sendAt = date_create('now')->format('Y-m-d H:i:s');
        }
        $this->messageType = $messageType; 
        $this->contextUser = $contextUser;
        $this->sendAt = $sendAt;
    }

    abstract function toJson(); 
}

class CyNotification extends CyMessageEnvelope {
    private $persistent; 
    private $text;
    private $linkTo;

    function __construct($text, $linkTo = null, $persistent = true, $contextUser, $sendAt = null) {
        parent::__construct( "Notification", $contextUser, $sendAt );

        $this->persistent = $persistent;
        $this->text = $text;
        $this->linkTo = $linkTo;

    }

    function toJson() {
        //return wp_json_encode(  $this);
        return "{'message_type': 'notification', 'notification_id': $this->text, 'user_id': $this->contextUser }";
    }
}


?>