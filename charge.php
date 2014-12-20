<?php

{
  "require": {
    "stripe/stripe-php": "1.*"
  }
}

Stripe::setApiKey("UmSEWvgAWiTGgQnWnfFMTdVRCEh8mSma");

// Get the credit card details submitted by the form
$token = $_POST['stripeToken'];
$price = $_POST['price'];
$email = $_POST['billingEmail'];

// Create a Customer
$customer = Stripe_Customer::create(array(
  "card" => $token,
  "description" => $email)
);

try {
	$charge = Stripe_Charge::create(array(
	  "amount" => $price, # amount in cents, again
	  "currency" => "usd",
	  "customer" => $customer->id)
	);
	echo $charge;
} catch(Stripe_CardError $e) {
  // The card has been declined
	echo $e;
}

?>
