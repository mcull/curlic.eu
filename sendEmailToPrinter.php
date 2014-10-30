<?php

use Aws\Ses\SesClient;

$client = SesClient::factory(array(
    'profile' => '~/.aws/credentials',
    'region'  => 'us-east-1'
));

$result = $client->sendEmail(array(
    // Source is required
    'Source' => 'admin@curlic.eu',
    // Destination is required
    'Destination' => array(
        'ToAddresses' => array('sales@curlic.eu')//add to this list later
    ),
    // Message is required
    'Message' => array(
        // Subject is required
        'Subject' => array(
            // Data is required
            'Data' => 'New Curliceu Order!'
        ),
        // Body is required
        'Body' => array(
            'Text' => array(
                // Data is required
                'Data' => 'Testing sending an email from Curlic.eu'
            ),
            'Html' => array(
                // Data is required
                'Data' => '<html><head></head><body><b>HTML</b><i> Email</i> <u>test</u></body></html>'
            ),
        ),
    ),
    'ReplyToAddresses' => array('admin@curlic.eu'),
    'ReturnPath' => 'marc.cull@gmail.com',
));

?>