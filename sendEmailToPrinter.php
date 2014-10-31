<?php
require 'aws/aws-autoloader.php';
require 'credentials.php';

$guid = $_REQUEST['guid'];

use Aws\Ses\SesClient;

$client = SesClient::factory(array(
    'key' => $key,
    'secret' => $secret,
    'region'  => 'us-east-1'
));

$result = $client->sendEmail(array(
    // Source is required
    'Source' => 'admin@curlic.eu',
    // Destination is required
    'Destination' => array(
        'ToAddresses' => array('printing@curlic.eu')//add to this list later
    ),
    // Message is required
    'Message' => array(
        // Subject is required
        'Subject' => array(
            // Data is required
            'Data' => 'Curliceu print job ' . $guid
        ),
        // Body is required
        'Body' => array(
            'Text' => array(
                // Data is required
                'Data' => 'Curliceu order ready to print!'
            ),
            'Html' => array(
                // Data is required
                'Data' => '<html><head></head><body><b>HTML</b><i> Email</i> <u>test</u></body></html>'
            ),
        ),
    ),
    'ReplyToAddresses' => array('admin@curlic.eu'),
    'ReturnPath' => 'admin@curlic.eu',
));

?>