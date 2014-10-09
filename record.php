<?php
 
require "Services/Twilio.php";
include "messages.php";
 

    //output TwiML to record the message
    $response = new Services_Twilio_Twiml();
    $response->say('Record your message at the beep.');
    $response->record(
        array(
            'action' => "handle_message.php",
            'maxLength' => '120')
    );
 
    // record will post to this url if it receives a message
    // otherwise it falls through to the next verb
    $response->gather()
        ->say("A message was not received, press any key to try again");
 
    print $response;

 
?>