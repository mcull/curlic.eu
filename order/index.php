<?php

require_once('./Stripe.php');
require '../aws/aws-autoloader.php';
require '../credentials.php';
use Aws\Ses\SesClient;
use Aws\DynamoDb\DynamoDbClient;

Stripe::setApiKey("UmSEWvgAWiTGgQnWnfFMTdVRCEh8mSma");

// Get the credit card details submitted by the form
$token = $_POST['stripeToken'];
$price = $_POST['price'];
$email = $_POST['billingEmail'];

$charge = null;
$error = null;



$fonts = json_decode(file_get_contents('../fonts.json'),true);
$ttfFont = getFontByName($_POST["font"]);

$graphicUrl = "http://www.curlic.eu/namesvg/?text=" 
			. $_POST["text"] 
			. "&font=" 
			. $ttfFont["ttfName"] 
			. "&stroke=" 
			. ((ISSET($ttfFont["stroke"]) && strlen($ttfFont["stroke"]) > 0) ? $ttfFont["stroke"] : "0")
			. "&spacing=" 
			. ((ISSET($ttfFont["spacing"]) && strlen($ttfFont["spacing"]) > 0) ? $ttfFont["spacing"] : "0");

date_default_timezone_set("America/New_York");


// Create a Customer
$customer = Stripe_Customer::create(array(
  "card" => $token,
  "description" => $email)
);

try {
	$charge = Stripe_Charge::create(array(
		"amount" => $price,
		"currency" => "usd",
		"card" => $token, 
		"description" => "Charge for Curliceu"
	));


	sendEmailToPrinter($charge);
	persistOrder($charge);

} catch(Stripe_CardError $e) {
  // The card has been declined
	$error =  $e;
}

function getFontByName($name) {
	global $fonts;
	foreach ($fonts as $font) {
		if (str_replace("-"," ",$name) == $font["name"])  {
			return $font;
		}
	}
}

function persistOrder($charge) {
	global $key,$secret;
	$client = DynamoDbClient::factory(array(
    	'key' => $key,
    	'secret'=>$secret,
    	'region'  => 'us-east-1'
	));

	$result = $client->putItem(array(
    'TableName' => 'curliceu_orders',
    'Item' => array(
        'id'      			=> array('S' => $charge["id"]),
        'order_date'    			=> array('N' => time()),
        'amount'			=> array('N' => $charge["amount"]/100),
        'text'				=> array('S' => $_POST['text']),
        'font'				=>  array('S' => $_POST['font']),
        'material'			=> array('S' => $_POST['material']),
        'cardBrand'			=> array('S' => $charge['card']['brand']),
        'last4'				=> array('S' => $charge['card']['last4']),
        'billName'			=> array('S' => $_POST['billName']),
        'billAddress1'		=> array('S' => $_POST['billAddress1']),
        'billAddress2'		=> array('S' => (ISSET($_POST['billAddress2']) && strlen($_POST['billAddress2'] > 0) ? $_POST['billAddress2'] : "none")),
        'billCity'			=> array('S' => $_POST['billCity']),
        'billState'			=> array('S' => $_POST['billState']),
        'billZip'			=> array('S' => $_POST['billZip']),
        'shippingMethod'	=> array('S' => $_POST['shippingMethod']),
        'shipName'			=> array('S' => $_POST['shipName']),
        'shipAddress1'		=> array('S' => $_POST['shipAddress1']),
        'shipAddress2'		=> array('S' => (ISSET($_POST['shipAddress2']) && strlen($_POST['shipAddress2'] > 0) ? $_POST['shipAddress2'] : "none")),
        'shipCity'			=> array('S' => $_POST['shipCity']),
        'shipState'			=> array('S' => $_POST['shipState']),
        'shipZip'			=> array('S' => $_POST['shipZip']),
        'customer_email'	=> array('S' => $_POST['billEmail']),
		'customer_sms'		=> array('S' => (ISSET($_POST['billSMS']) && strlen($_POST['billSMS'] > 0) ? $_POST['billSMS'] : "none"))
		)
	));
}

function sendEmailToPrinter($charge) {
	global $key, $secret, $graphicUrl;

	$material = $_REQUEST['material'];
	$html = file_get_contents("../email_templates/signaturePrintOrderEmail.html");

	$date = new DateTime('now', new DateTimeZone('America/New_York'));

	$html = str_replace("{{date}}", $date->format("F jS, Y") , $html);
	$html = str_replace("{{order}}", $charge["id"] , $html);
	$html = str_replace("{{material}}", $_POST['material'], $html);
	$html = str_replace("{{text}}", $_POST['text'], $html);
	$html = str_replace("{{font}}", $_POST['font'], $html);
	$html = str_replace("{{imgUrl}}", $graphicUrl, $html);
	$html = str_replace("{{shipMethod}}", $_POST['shippingMethod'], $html);
	$html = str_replace("{{shipName}}", $_POST['shipName'], $html);
	$html = str_replace("{{shipAddress1}}", $_POST['shipAddress1'], $html);
	$html = str_replace("{{shipAddress2}}",
			((ISSET($_POST["shipAddress2"]) && strlen($_POST["shipAddress2"]) > 0)
				?  $_POST['shipAddress2']
				: ""
				), $html);
	$html = str_replace("{{shipCity}}", $_POST['shipCity'], $html);
	$html = str_replace("{{shipState}}", $_POST['shipState'], $html);
	$html = str_replace("{{shipZip}}", $_POST['shipZip'], $html);

	$html = str_replace("{{email}}", $_POST['billEmail'], $html);
	$html = str_replace("{{sms}}", $_POST['billSMS'], $html);

	$textOnly  = $date->format("F jS, Y") . "\n";
	$textOnly .= "Order Id: " . $charge["id"] . "\n";
	$textOnly .= "Material : " .$_POST['material'] . "\n";
	$textOnly .= "Text: " .$_POST['text'] . "\n";
	$textOnly .= "Font: " .$_POST['font'] . "\n";
	$textOnly .= "Graphic Url: " .$graphicUrl . "\n";
	$textOnly .= "Shipping Method: " .$_POST['shippingMethod'] . "\n";
	$textOnly .= "Ship Name: " .$_POST['shipName'] . "\n";
	$textOnly .= "Ship Address 1: " .$_POST['shipAddress1'] . "\n";
	if (ISSET($_POST["shipAddress2"]) && strlen($_POST["shipAddress2"]) > 0) {
		$textOnly .= "ship Address 2: " . $_POST['shipAddress2'] . "\n";
	}
	$textOnly .= "Ship City: " .$_POST['shipCity'] . "\n";
	$textOnly .= "Ship State: " .$_POST['shipState'] . "\n";
	$textOnly .= "Ship Zip: " .$_POST['shipZip'] . "\n";

	$textOnly .= "Customer Email: " .$_POST['billEmail'] . "\n";
	$textOnly .= "Customer SMS: " .$_POST['billSMS'] . "\n";



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
	            'Data' => 'Order ' . $charge["id"]
	        ),
	        // Body is required
	        'Body' => array(
	            'Text' => array(
	                // Data is required
	                'Data' => $textOnly
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
}

?>
<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Curliceu Order <?php echo $charge["id"]; ?></title>
    <link rel="stylesheet" href="../css/foundation.css" />
    <link rel="stylesheet" href="../css/app.css" />
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='//fonts.googleapis.com/css?family=Rochester|Noto+Sans|Source+Code+Pro|Nixie+One|Quicksand:300,400,700|Montserrat:400,700' rel='stylesheet' type='text/css'>
    <script src="js/vendor/modernizr.js"></script>
    <style>
    	.receiptRow {
    		border-bottom: solid 1px #cccccc;
    		padding-bottom:.6rem;
    		padding-top:.3rem;
    	}
    </style>
  </head>
  <body style="font-family:'Quicksand', sans-serif;font-size:.75rem;width:80%;margin:0px auto;">
  	<?php
  	 	if ($error) {
  	?>

  	 <div class="row">
  		<div class="large-12 columns" style="padding-top:1rem;padding-bottom:2rem;">
  			Sorry!!  We could not process your payment:
  			<span style="color:red;font-weight:bold">
  			 <?php 	$body = $e->getJsonBody();
  					$err  = $body['error'];
  					echo $err['message'];
 			?></span>  
 			<p>Please go back to
  			try your order again.  

  			<p>For questions, 
  			contact us anytime at <a href="mailto:help@curlic.eu">help@curlic.eu</a>.
  		</div>
  	</div>

  	<?php 
  	 	} else {
	?>
  	<div class="row">
  		<div class="large-12 columns" style="text-align:center;font-size:2rem;font-family:Rochester;padding-top:2rem;">
  			Curliceu<span style="top:.4rem;position:relative;font-size:1rem"><small>&trade;</small></span>
  		</div>
  	</div>
  	 <div class="row">
  		<div class="large-12 columns" style="text-align:center;font-size:1.5em;padding-top:1rem">
  			Reciept
  		</div>
  	</div>
  	 <div class="row">
  		<div class="large-12 columns" style="padding-top:1rem;padding-bottom:2rem;">
  			We've received payment for your <b>Signature Necklace</b>.  
  			You can keep this receipt for your records.  For questions, 
  			contact us anytime at <a href="mailto:help@curlic.eu">help@curlic.eu</a>.
  		</div>
  	</div>
  	<div class="row receiptRow">
  		<div class="large-4 medium-4 small-4 columns">Date</div>
  		<div class="large-8 medium-8 small-8 columns"><b><?php echo date("F jS, Y g:sa T") ?></b></div>
  	</div>
  	<div class="row receiptRow">
  		<div class="large-4 medium-4 small-4 columns">Billed To</div>
  		<div class="large-8 medium-8 small-8 columns">
  			<b><?php echo $_POST['billName']; ?><br>
  				<?php echo ucwords($_POST['billAddress1']); ?><br>
  				<?php echo (ISSET($_POST['Address2']) && str_len($_POST['Address2']) > 0) ? $_POST['Address2'] . "<br>": ""; ?>
  				<?php echo ucwords($_POST['billCity']) . ", " . $_POST['billState'] . " " . $_POST['billZip']; ?>
  			</b>
  		</div>
  	</div>
  	<div class="row receiptRow">
  		<div class="large-4 medium-4 small-4 columns">Ship To</div>
  		<div class="large-8 medium-8 small-8 columns">
  			 <b><?php echo $_POST['billName']; ?><br>
  				<?php echo ucwords($_POST['billAddress1']); ?><br>
  				<?php echo (ISSET($_POST['Address2']) && str_len($_POST['Address2']) > 0) ? $_POST['Address2'] . "<br>": ""; ?>
  				<?php echo ucwords($_POST['billCity']) . ", " . $_POST['billState'] . " " . $_POST['billZip']; ?>
  			</b>
  		</div>
  	</div>
  	<div class="row receiptRow">
  		<div class="large-4 medium-4 small-4 columns">Amount Charged</div>
  		<div class="large-8 medium-8 small-8 columns"><b>USD $<?php echo $charge['amount']/100 ?></b></div>
  	</div>
	<div class="row receiptRow">
  		<div class="large-4 medium-4 small-4 columns">Charged To</div>
  		<div class="large-8 medium-8 small-8 columns"><b><?php echo $charge['card']['brand'] . " (***" .$charge['card']['last4'] . ")"; ?></b></div>
  	</div>
	<div class="row receiptRow">
  		<div class="large-4 medium-4 small-4 columns">Transaction ID</div>
  		<div class="large-8 medium-8 small-8 columns"><b><?php echo $charge['id'] ?></b></div>
  	</div>

	<div class="row">
  		<div class="large-12 columns" style="margin-top:3rem;font-size:3rem;opacity:.5;font-family:Rochester;height:3rem;">Thank you</div>
  	</div>

	<div class="row" style="font-size:.6rem;line-height:.75rem;top:-1rem;position:relative;">
  		<div class="large-4 medium-4 small-4 columns">
  			Curlic.eu<br>
  			11501 Valley View Road<br>
  			Anchorage, KY 4023<br>
  			United States
  		</div>
  		<div class="large-8 medium-8 small-8 columns">
  			help@curlic.eu<br>
  			https://curlic.eu/contact

  		</div>
  	</div>
  	<?php } ?>
  	<div class="row" id="footer"> 
	  <div class="large-12 columns" style="text-align:center;">
	    <h6><small>Hand-made with <i class="fa fa-heart"></i> in Kentucky  &copy;2014 Curliceu <a href="privacy.html">Privacy Policy</a></small></h6>
	  </div>
	</div>
    <script src="../js/vendor/jquery.js"></script>
    <script src="../js/foundation.min.js"></script>  
    <script src="../js/curliceu.js"></script>

  </body>
</html>
