<?php
require 'aws/aws-autoloader.php';
require 'credentials.php';

$guid = $_REQUEST['guid'];
$material = $_REQUEST['material'];
$html = file_get_contents("email_templates/print_order.html");

$date = new DateTime('now', new DateTimeZone('America/New_York'));

$html = str_replace("[[date]]", $date->format("F jS, Y") , $html);
$html = str_replace("[[guid]]", $guid , $html);
$html = str_replace("[[material]]", $material , $html);

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
            'Data' => $guid
        ),
        // Body is required
        'Body' => array(
            'Text' => array(
                // Data is required
                'Data' => 'Curliceu order ready to print!'
            ),
            'Html' => array(
                // Data is required
                'Data' => $html
            ),
        ),
    ),
    'ReplyToAddresses' => array('admin@curlic.eu'),
    'ReturnPath' => 'admin@curlic.eu',
));

?>
