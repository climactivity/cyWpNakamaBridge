<?php

require_once('CyMessageEnvelope.php');

/**
 * Forwards notifications to the game server
 */
class CyNotification extends CyMessageEnvelope {
    private $persistent; 
    private $text;
    private $linkTo;

    /** 
     * @param text the notification contents 
     * @param linkTo link to open when the notification is selected in the app
     * @param persistent send to client on next reconnect if offline, else discarded. 
     *                   in next release persistent messages may be send as push notifications
     */
    function __construct($text, $linkTo = null, $persistent = true, $contextUser, $sendAt = null) {
        parent::__construct( "Notification", $contextUser, $sendAt );

        $this->persistent = $persistent;
        $this->text = $text;
        $this->linkTo = $linkTo;

    }

    /** 
     * This probably needs to be copied to all implementations, idk 
     * Someone who is good at php might be able to fix it
     */
    public function jsonSerialize() {
        $vars = get_object_vars($this); 
        return $vars;
    }

    public function getRpcTarget() {
        return "wp_notification"; 
    }
    function toJson() {
        return wp_json_encode($this);
    }
}

?>