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
        $contextUser = get_user_by("id", $contextUser);
        $notificationDataTemp = BP_Notifications_Notification::get(array('id' => $text))[0];
        $content = $this->formatNotification( $notificationDataTemp );

        parent::__construct( "Notification", $contextUser, $sendAt );

        $this->persistent = $persistent;
        $this->text = $content;
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
    
    // see https://www.buddyboss.com/resources/reference/functions/bp_notifications_get_notifications_for_user/
    private function formatNotification($notification) {
        $bp = buddypress();
        $component_name = $notification->component_name;
        if ( 'xprofile' == $notification->component_name ) {
            $component_name = 'profile';
        }

        $content = call_user_func(
            $bp->{$component_name}->notification_callback,
            $notification->component_action, 
            $notification->item_id, 
            $notification->secondary_item_id, 
            $notification->total_count, 
            'string', 
            $notification->id
        );
        return $content;

    }

}
?>