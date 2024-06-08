<?php
	require_once "stripe-php-master/init.php";
	require_once "products.php";

$stripeDetails = array(
		"secretKey" => "sk_test_51OwRs7SJHeuymCnZA8fLT6AOuPcTe0rjF24lQwrfNvBi246zijyaKk73TVm9Y7wEkUYMS8ToeLkfxOcBmK9hdjs000ZC5seBmH",  //Your Stripe Secret key
		"publishableKey" => "pk_test_51OwRs7SJHeuymCnZmHE7EthKwyPawHPLWHd3HnC6aJy5koM9PyETLaXITLAPtSyM082FwSsROYlqHLOKrLThZDZA00C1TwRMc4"  //Your Stripe Publishable key
	);

	// Set your secret key: remember to change this to your live secret key in production
	// See your keys here: https://dashboard.stripe.com/account/apikeys
	\Stripe\Stripe::setApiKey($stripeDetails['secretKey']);

	
?>
